<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class WeeklyPlanRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(array $filters = []): array
    {
        $sql = 'SELECT wp.*, u.name as assigned_name, c.name as creator_name 
                FROM weekly_plans wp 
                LEFT JOIN users u ON wp.assigned_to = u.id 
                LEFT JOIN users c ON wp.created_by = c.id 
                WHERE 1=1';
        $params = [];

        if (!empty($filters['project'])) {
            $sql .= ' AND wp.project = ?';
            $params[] = $filters['project'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND wp.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['assigned_to'])) {
            $sql .= ' AND wp.assigned_to = ?';
            $params[] = $filters['assigned_to'];
        }

        $sql .= ' ORDER BY wp.week_start DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT wp.*, u.name as assigned_name, c.name as creator_name 
             FROM weekly_plans wp 
             LEFT JOIN users u ON wp.assigned_to = u.id 
             LEFT JOIN users c ON wp.created_by = c.id 
             WHERE wp.id = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByAssignedTo(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM weekly_plans WHERE assigned_to = ? ORDER BY week_start DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO weekly_plans (week_start, project, summary, assigned_to, status, progress_percent, file_path, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['week_start'],
            $data['project'] ?? 'A',
            $data['summary'] ?? null,
            $data['assigned_to'] ?? null,
            $data['status'] ?? 'pending',
            $data['progress_percent'] ?? 0,
            $data['file_path'] ?? null,
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
        $sql = 'UPDATE weekly_plans SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM weekly_plans WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function addTask(int $planId, string $title): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO weekly_tasks (plan_id, title, status) VALUES (?, ?, ?)'
        );
        $stmt->execute([$planId, $title, 'not_done']);
        return (int)$this->db->lastInsertId();
    }

    public function updateTask(int $taskId, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE weekly_tasks SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $taskId]);
    }

    public function getTasksByPlanId(int $planId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM weekly_tasks WHERE plan_id = ? ORDER BY id ASC');
        $stmt->execute([$planId]);
        return $stmt->fetchAll();
    }

    public function findTaskById(int $taskId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM weekly_tasks WHERE id = ?');
        $stmt->execute([$taskId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
