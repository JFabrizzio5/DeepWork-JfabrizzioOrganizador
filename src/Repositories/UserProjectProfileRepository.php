<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserProjectProfileRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUserAndProject(int $userId, int $projectId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM user_project_profiles WHERE user_id = ? AND project_id = ?'
        );
        $stmt->execute([$userId, $projectId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByProject(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT upp.*, u.name as user_name, u.email as user_email, u.role as user_role
             FROM user_project_profiles upp
             JOIN users u ON upp.user_id = u.id
             WHERE upp.project_id = ?
             ORDER BY u.name ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function upsert(int $userId, int $projectId, array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO user_project_profiles (user_id, project_id, display_name, bio, contact_info)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               display_name = VALUES(display_name),
               bio          = VALUES(bio),
               contact_info = VALUES(contact_info)'
        );
        $stmt->execute([
            $userId,
            $projectId,
            $data['display_name'] ?? null,
            $data['bio']          ?? null,
            $data['contact_info'] ?? null,
        ]);
    }
}
