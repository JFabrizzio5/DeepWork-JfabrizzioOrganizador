<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProjectRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM projects ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM projects WHERE name = ?');
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO projects (name, color, description) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['color'] ?? '#3B82F6',
            $data['description'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE projects SET name = ?, color = ?, description = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['name'],
            $data['color'] ?? '#3B82F6',
            $data['description'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM projects WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
