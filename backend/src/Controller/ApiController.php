<?php
namespace App\Controller;

use App\Services\JwtService;
use Cake\I18n\DateTime;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Random\RandomException;

class ApiController extends AppController
{
    protected JwtService $jwtService;

    public function initialize(): void
    {
        parent::initialize();
        $this->jwtService = new JwtService();
        $this->autoRender = false;
    }

    /**
     * @throws RandomException
     */
    public function login(): \Cake\Http\Response
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();

        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->find()->where(['email' => $data['email'] ?? ''])->first();

        if (!$user) {
            return $this->response->withStatus(401)->withStringBody(json_encode([
                'error' => 'Utilisateur introuvable avec cet email'
            ]));
        }

        $hasher = new DefaultPasswordHasher();
        $passwordCorrect = $hasher->check($data['password'] ?? '', $user->password);

        if (!$passwordCorrect) {
            return $this->response->withStatus(401)->withStringBody(json_encode([
                'error' => 'Le mot de passe ne correspond pas au hash de la BDD',
                'hash_en_bdd' => $user->password // Permet de vérifier visuellement s'il est resté en clair
            ]));
        }

        $accessToken = $this->jwtService->generateAccessToken($user->id, $user->role);
        $refreshToken = $this->jwtService->generateRefreshToken();

        $refreshTokensTable = $this->fetchTable('RefreshTokens');
        $rtEntity = $refreshTokensTable->newEntity([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => (new DateTime())->modify('+7 days')
        ]);
        $refreshTokensTable->save($rtEntity);

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ]));
    }

    /**
     * @throws RandomException
     */
    public function refresh(): \Cake\Http\Response
    {
        $this->request->allowMethod(['post']);
        $refreshTokenString = $this->request->getData('refresh_token');

        $refreshTokensTable = $this->fetchTable('RefreshTokens');
        $tokenEntity = $refreshTokensTable->find()
            ->contain(['Users'])
            ->where(['token' => $refreshTokenString, 'expires_at >' => new DateTime()])
            ->first();

        if (!$tokenEntity) {
            return $this->response->withStatus(401)->withStringBody(json_encode(['error' => 'Refresh token invalide ou expiré']));
        }

        $newAccessToken = $this->jwtService->generateAccessToken($tokenEntity->user_id, $tokenEntity->user->role);
        $newRefreshToken = $this->jwtService->generateRefreshToken();

        $tokenEntity->token = $newRefreshToken;
        $tokenEntity->expires_at = (new DateTime())->modify('+7 days');
        $refreshTokensTable->save($tokenEntity);

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ]));
    }

    public function logout(): \Cake\Http\Response
    {
        $this->request->allowMethod(['post']);
        $refreshTokenString = $this->request->getData('refresh_token');

        $refreshTokensTable = $this->fetchTable('RefreshTokens');
        $refreshTokensTable->deleteAll(['token' => $refreshTokenString]);

        return $this->response->withStatus(200)->withStringBody(json_encode(['message' => 'Déconnecté et token révoqué']));
    }

    /**
     * POST /api/v1/auth/register
     * Enregistre un nouvel utilisateur en base de données.
     */
    public function register(): \Cake\Http\Response
    {
        $this->request->allowMethod(['post']);

        $rawData = $this->request->getData();
        if (empty($rawData)) {
            return $this->response->withStatus(400)->withStringBody(json_encode([
                'error' => 'Le corps de la requête JSON est vide ou mal formaté.'
            ]));
        }

        $usersTable = $this->fetchTable('Users');

        $user = $usersTable->newEntity($rawData);

        if (empty($user->getVisible())) {
            return $this->response->withStatus(400)->withStringBody(json_encode([
                'error' => 'Les données ont été ignorées par CakePHP. Vérifiez le tableau $_accessible dans l\'entité User.php.',
                'donnees_recues_par_le_serveur' => $rawData
            ]));
        }

        if ($usersTable->save($user)) {
            $user->unset('password');
            return $this->response
                ->withStatus(201)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'message' => 'Utilisateur enregistré avec succès',
                    'user' => $user
                ]));
        }

        return $this->response
            ->withStatus(400)
            ->withType('application/json')
            ->withStringBody(json_encode([
                'error' => 'Échec de la validation ou de la sauvegarde',
                'validation_errors' => $user->getErrors()
            ]));
    }
}
