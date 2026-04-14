<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class KnowledgeBaseRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(array $filters = []): array
    {
        $sql = 'SELECT kb.*, u.name as creator_name FROM knowledge_base kb LEFT JOIN users u ON kb.created_by = u.id WHERE 1=1';
        $params = [];

        if (!empty($filters['tag_type'])) {
            $sql .= ' AND kb.tag_type = ?';
            $params[] = $filters['tag_type'];
        }

        $sql .= ' ORDER BY kb.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT kb.*, u.name as creator_name FROM knowledge_base kb LEFT JOIN users u ON kb.created_by = u.id WHERE kb.id = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function search(string $query): array
    {
        $stmt = $this->db->prepare(
            'SELECT kb.*, u.name as creator_name FROM knowledge_base kb LEFT JOIN users u ON kb.created_by = u.id 
             WHERE kb.title LIKE ? OR kb.content LIKE ? OR kb.tags LIKE ? 
             ORDER BY kb.created_at DESC'
        );
        $like = '%' . $query . '%';
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO knowledge_base (title, content, tags, links, tag_type, created_by) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['tags'] ?? null,
            $data['links'] ?? null,
            $data['tag_type'] ?? 'documentation',
            $data['created_by'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = 'UPDATE knowledge_base SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        // Files are deleted via CASCADE
        $stmt = $this->db->prepare('DELETE FROM knowledge_base WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function createFile(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO knowledge_base_files (article_id, user_id, filename, original_name, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['article_id'],
            $data['user_id'],
            $data['filename'],
            $data['original_name'],
            $data['file_type'] ?? null,
            $data['file_size'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findFilesByArticleId(int $articleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT kf.*, u.name as uploader_name FROM knowledge_base_files kf LEFT JOIN users u ON kf.user_id = u.id WHERE kf.article_id = ? ORDER BY kf.created_at ASC'
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }

    public function findFileById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM knowledge_base_files WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function deleteFile(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM knowledge_base_files WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
