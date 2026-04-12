<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class SucursalRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(): array
    {
        return $this->db->query('SELECT * FROM sucursales ORDER BY nombre ASC')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM sucursales WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $nombre, ?string $descripcion): int
    {
        $stmt = $this->db->prepare('INSERT INTO sucursales (nombre, descripcion) VALUES (?, ?)');
        $stmt->execute([$nombre, $descripcion]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM sucursales WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function getSucursalesForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.* FROM sucursales s
             JOIN user_sucursales us ON s.id = us.sucursal_id
             WHERE us.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function setUserSucursales(int $userId, array $sucursalIds): void
    {
        $del = $this->db->prepare('DELETE FROM user_sucursales WHERE user_id = ?');
        $del->execute([$userId]);
        $ins = $this->db->prepare('INSERT INTO user_sucursales (user_id, sucursal_id) VALUES (?, ?)');
        foreach ($sucursalIds as $sId) {
            $ins->execute([$userId, (int)$sId]);
        }
    }
}
