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

    // ── User-project access ────────────────────────────────

    public function getProjectsForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.* FROM projects p
             JOIN user_projects up ON p.id = up.project_id
             WHERE up.user_id = ?
             ORDER BY p.name ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function setUserProjects(int $userId, array $projectIds): void
    {
        $del = $this->db->prepare('DELETE FROM user_projects WHERE user_id = ?');
        $del->execute([$userId]);
        $ins = $this->db->prepare('INSERT INTO user_projects (user_id, project_id) VALUES (?, ?)');
        foreach ($projectIds as $pid) {
            $ins->execute([$userId, (int)$pid]);
        }
    }

    public function getUsersForProject(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.role FROM users u
             JOIN user_projects up ON u.id = up.user_id
             WHERE up.project_id = ?
             ORDER BY u.name ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function userHasProject(int $userId, int $projectId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM user_projects WHERE user_id = ? AND project_id = ?'
        );
        $stmt->execute([$userId, $projectId]);
        return (bool)$stmt->fetch();
    }
}
