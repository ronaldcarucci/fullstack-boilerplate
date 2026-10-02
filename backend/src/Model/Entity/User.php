<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;

class User extends Entity
{
    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'role' => true,
        'created' => true,
        'modified' => true,
    ];

    protected function _setPassword(string $password): ?string
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
        return null;
    }
}
