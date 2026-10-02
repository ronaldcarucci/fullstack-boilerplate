<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp'); // Gère automatiquement created et modified
    }

    /**
     * Règles de validation des données reçues (ex: longueur du mot de passe)
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->email('email', false, 'L\'adresse email n\'est pas valide.')
            ->notEmptyString('email', 'L\'adresse email est obligatoire.');

        $validator
            ->notEmptyString('password', 'Le mot de passe est obligatoire.')
            ->lengthBetween('password', [8, 100], 'Le mot de passe doit contenir entre 8 et 100 caractères.');

        return $validator;
    }

    /**
     * Règles de cohérence au niveau de la base de données (ex: unicité de l'email)
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email'], 'Cette adresse email est déjà utilisée.'));

        return $rules;
    }
}
