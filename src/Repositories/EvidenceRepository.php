<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class EvidenceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByTicketId(int $ticketId): array
    {
        $stmt = $this->db->prepare(
            'SELECT e.*, u.name as uploader_name 
             FROM evidences e 
             JOIN users u ON e.user_id = u.id 
             WHERE e.ticket_id = ? 
             ORDER BY e.created_at ASC'
        );
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM evidences WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO evidences (ticket_id, user_id, filename, original_name, file_type, file_size)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['ticket_id'],
            $data['user_id'],
            $data['filename'],
            $data['original_name'],
            $data['file_type'],
            $data['file_size'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM evidences WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
