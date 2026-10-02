<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Cake\I18n\DateTime;
use Random\RandomException;

class JwtService
{
    private string $secretKey = 'VOTRE_CLEF_SECRETE_SUPER_SECURISEE';
    private string $algo = 'HS256';

    public function generateAccessToken(int $userId, string $role): string
    {
        $payload = [
            'iss' => 'mon-api-cakephp',
            'sub' => $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + 900 // 15 minutes
        ];
        return JWT::encode($payload, $this->secretKey, $this->algo);
    }

    /**
     * @throws RandomException
     */
    public function generateRefreshToken(): string
    {
        return bin2hex(random_bytes(40));
    }

    public function validateToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, $this->algo));
        } catch (\Exception $e) {
            return null;
        }
    }
}
