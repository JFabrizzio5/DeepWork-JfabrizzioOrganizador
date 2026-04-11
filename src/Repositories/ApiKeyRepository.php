<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ApiKeyRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lookup an active token and join its owner's user data.
     */
    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ak.id, ak.name AS key_name, ak.is_active,
                    u.id AS user_id, u.name AS user_name,
                    u.email AS user_email, u.role AS user_role
             FROM api_keys ak
             JOIN users u ON ak.user_id = u.id
             WHERE ak.token = ? AND ak.is_active = 1'
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare(
            'SELECT ak.*, u.name AS user_name, u.email AS user_email, u.role AS user_role
             FROM api_keys ak
             JOIN users u ON ak.user_id = u.id
             ORDER BY ak.created_at DESC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM api_keys WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $name, string $token): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO api_keys (user_id, name, token) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $name, $token]);
        return (int) $this->db->lastInsertId();
    }

    public function revoke(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE api_keys SET is_active = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM api_keys WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function touchLastUsed(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE api_keys SET last_used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
