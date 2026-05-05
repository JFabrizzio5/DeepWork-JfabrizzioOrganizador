<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProjectFileRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByProject(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT pf.*, u.name as uploader_name
             FROM project_files pf
             JOIN users u ON pf.user_id = u.id
             WHERE pf.project_id = ?
             ORDER BY pf.created_at DESC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM project_files WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO project_files (project_id, user_id, filename, original_name, file_type, file_size, description)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['user_id'],
            $data['filename'],
            $data['original_name'],
            $data['file_type'],
            $data['file_size'],
            $data['description'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM project_files WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
