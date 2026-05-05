<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProjectNoteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByProject(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT pn.*, u.name as author_name
             FROM project_notes pn
             JOIN users u ON pn.user_id = u.id
             WHERE pn.project_id = ?
             ORDER BY pn.created_at ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM project_notes WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(int $projectId, int $userId, string $note): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO project_notes (project_id, user_id, note) VALUES (?, ?, ?)'
        );
        $stmt->execute([$projectId, $userId, $note]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM project_notes WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
