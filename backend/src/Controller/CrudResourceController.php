<?php

namespace App\Controller;

use App\Services\JwtService;
use Cake\Utility\Inflector;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Datasource\EntityInterface;

class CrudResourceController extends AppController
{
    use LocatorAwareTrait;

    protected ?object $currentUser = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->autoRender = false;

        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new UnauthorizedException('Jeton de sécurité manquant.');
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $jwtService = new JwtService();
        $this->currentUser = $jwtService->validateToken($token);

        if (!$this->currentUser) {
            throw new UnauthorizedException('Jeton invalide, corrompu ou expiré.');
        }
    }

    protected function getDynamicTable(): \Cake\ORM\Table
    {
        $resource = $this->request->getParam('resource');
        $tableName = Inflector::camelize($resource);

        try {
            return $this->getTableLocator()->get($tableName);
        } catch (\Exception $e) {
            throw new NotFoundException("La ressource '{$resource}' n'existe pas sur ce serveur.");
        }
    }

    protected function sanitizeData(mixed $data): mixed
    {
        if ($data instanceof EntityInterface) {
            $data->unset('password');
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeData($value);
            }
        }
        return $data;
    }

    public function index(): \Cake\Http\Response
    {
        $this->request->allowMethod(['get']);
        $table = $this->getDynamicTable();
        $query = $table->find();

        $queryParams = $this->request->getQueryParams();
        $columns = $table->getSchema()->columns();

        $page = max(1, (int)($queryParams['page'] ?? 1));
        $limit = min(100, max(1, (int)($queryParams['limit'] ?? 20)));

        $ignoredParams = ['page', 'limit', 'sort', 'direction', 'q'];
        foreach ($queryParams as $field => $value) {
            if (!in_array($field, $ignoredParams, true) && in_array($field, $columns, true)) {
                $query->where(["{$table->getAlias()}.{$field}" => $value]);
            }
        }

        if (!empty($queryParams['q'])) {
            $displayField = $table->getDisplayField();
            if (in_array($displayField, $columns, true)) {
                $query->where(["{$table->getAlias()}.{$displayField} LIKE" => "%{$queryParams['q']}%"]);
            }
        }

        $sort = $queryParams['sort'] ?? null;
        $direction = strtolower($queryParams['direction'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
        if (!empty($sort) && in_array($sort, $columns, true)) {
            $query->order(["{$table->getAlias()}.{$sort}" => $direction]);
        }

        $totalRecords = (clone $query)->count();
        $query->limit($limit)->page($page);

        $result = $this->sanitizeData($query->toArray());

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'data' => $result,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_records' => $totalRecords,
                'total_pages' => (int)ceil($totalRecords / $limit),
            ]
        ]));
    }

    public function view($id = null): \Cake\Http\Response
    {
        $this->request->allowMethod(['get']);
        $table = $this->getDynamicTable();
        $entity = $table->get($id); // Lève un 404 automatique si introuvable

        return $this->response->withType('application/json')->withStringBody(json_encode($this->sanitizeData($entity)));
    }

    public function add()
    {
        $this->request->allowMethod(['post']);
        $table = $this->getDynamicTable();

        $entity = $table->newEntity($this->request->getData());

        if ($entity->hasErrors()) {
            return $this->response->withStatus(400)->withType('application/json')->withStringBody(json_encode(['error' => 'Échec validation', 'errors' => $entity->getErrors()]));
        }

        if ($table->save($entity)) {
            return $this->response->withStatus(210)->withType('application/json')->withStringBody(json_encode($this->sanitizeData($entity)));
        }
        return $this->response->withStatus(500)->withStringBody(json_encode(['message' => 'Erreur d\'écriture système.']));
    }

    public function edit($id = null)
    {
        $this->request->allowMethod(['put', 'patch']);
        $table = $this->getDynamicTable();

        $entity = $table->get($id);
        $entity = $table->patchEntity($entity, $this->request->getData());

        if ($entity->hasErrors()) {
            return $this->response->withStatus(400)->withType('application/json')->withStringBody(json_encode(['errors' => $entity->getErrors()]));
        }

        if ($table->save($entity)) {
            return $this->response->withType('application/json')->withStringBody(json_encode($this->sanitizeData($entity)));
        }
        return $this->response->withStatus(500)->withStringBody(json_encode(['message' => 'Erreur de mise à jour système.']));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);

        if ($this->currentUser->role !== 'admin') {
            throw new ForbiddenException('Privilèges insuffisants pour supprimer des ressources.');
        }

        $table = $this->getDynamicTable();
        $entity = $table->get($id);

        if ($table->delete($entity)) {
            return $this->response->withStatus(204);
        }
        return $this->response->withStatus(400)->withStringBody(json_encode(['error' => 'Suppression compromise.']));
    }

    public function indexNested(): \Cake\Http\Response
    {
        $this->request->allowMethod(['get']);

        $parentTable = $this->getTableLocator()->get(Inflector::camelize($this->request->getParam('parentResource')));
        $childTableName = Inflector::camelize($this->request->getParam('resource'));
        $parentId = $this->request->getParam('parentId');

        if (!$parentTable->hasAssociation($childTableName)) {
            throw new NotFoundException("La relation '{$childTableName}' n'existe pas.");
        }

        $association = $parentTable->getAssociation($childTableName);
        $query = $association->find()->where([$association->getForeignKey() => $parentId]);

        $queryParams = $this->request->getQueryParams();
        $page = max(1, (int)($queryParams['page'] ?? 1));
        $limit = min(100, max(1, (int)($queryParams['limit'] ?? 20)));

        $totalRecords = (clone $query)->count();
        $query->limit($limit)->page($page);

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'data' => $this->sanitizeData($query->toArray()),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_records' => $totalRecords,
                'total_pages' => (int)ceil($totalRecords / $limit)
            ]
        ]));
    }

    public function addNested()
    {
        $this->request->allowMethod(['post']);

        $parentTable = $this->getTableLocator()->get(Inflector::camelize($this->request->getParam('parentResource')));
        $childTableName = Inflector::camelize($this->request->getParam('resource'));
        $parentId = $this->request->getParam('parentId');

        if (!$parentTable->hasAssociation($childTableName) || !$parentTable->exists(['id' => $parentId])) {
            throw new NotFoundException("Ressource parente ou relation introuvable.");
        }

        $association = $parentTable->getAssociation($childTableName);
        $childTable = $association->getTarget();

        $data = $this->request->getData();
        $data[$association->getForeignKey()] = $parentId; // Injection contextuelle forcée

        $entity = $childTable->newEntity($data);
        if ($entity->hasErrors()) {
            return $this->response->withStatus(400)->withType('application/json')->withStringBody(json_encode(['errors' => $entity->getErrors()]));
        }

        if ($childTable->save($entity)) {
            return $this->response->withStatus(201)->withType('application/json')->withStringBody(json_encode($this->sanitizeData($entity)));
        }
        return $this->response->withStatus(500)->withStringBody(json_encode(['message' => 'Erreur d\'association.']));
    }

    public function linkResources(): \Cake\Http\Response
    {
        $this->request->allowMethod(['post']);
        $parentTable = $this->getTableLocator()->get(Inflector::camelize($this->request->getParam('parentResource')));
        $childTableName = Inflector::camelize($this->request->getParam('resource'));
        if (!$parentTable->hasAssociation($childTableName)) {
            throw new NotFoundException("Liaison structurelle inexistante.");
        }
        $parentEntity = $parentTable->get($this->request->getParam('parentId'));
        $childEntity = $parentTable->getAssociation($childTableName)->getTarget()->get($this->request->getParam('id'));
        $parentTable->{$childTableName}->link($parentEntity, [$childEntity]);
        return $this->response->withType('application/json')->withStringBody(json_encode([
            'success' => true,
            'message' => 'Association établie avec succès.'
        ]));
    }

    public function unlinkResources()
    {
        $this->request->allowMethod(['delete']);
        $parentTable = $this->getTableLocator()->get(Inflector::camelize($this->request->getParam('parentResource')));
        $childTableName = Inflector::camelize($this->request->getParam('resource'));
        if (!$parentTable->hasAssociation($childTableName)) {
            throw new NotFoundException("Liaison structurelle inexistante.");
        }
        $parentEntity = $parentTable->get($this->request->getParam('parentId'));
        $childEntity = $parentTable->getAssociation($childTableName)->getTarget()->get($this->request->getParam('id'));
        $parentTable->{$childTableName}->unlink($parentEntity, [$childEntity]);
        return $this->response->withType('application/json')->withStringBody(json_encode([
            'success' => true,
            'message' => 'Liaison rompue avec succès.'
        ]));
    }
}
