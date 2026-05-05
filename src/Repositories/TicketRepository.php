<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class TicketRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.name as requester_user_name, u.is_vip as requester_is_vip, u.highlight_color as requester_highlight_color,
                    a.name as assigned_name, s.nombre as sucursal_nombre, p.name as project_name
             FROM tickets t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN users a ON t.assigned_to = a.id
             LEFT JOIN sucursales s ON t.sucursal_id = s.id
             LEFT JOIN projects p ON t.project_id = p.id
             WHERE t.id = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAll(array $filters = []): array
    {
        $sql = 'SELECT t.*, u.name as requester_user_name, u.is_vip as requester_is_vip, u.highlight_color as requester_highlight_color,
                       a.name as assigned_name, s.nombre as sucursal_nombre, p.name as project_name
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN sucursales s ON t.sucursal_id = s.id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND t.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $sql .= ' AND t.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['impact'])) {
            $sql .= ' AND t.impact = ?';
            $params[] = $filters['impact'];
        }
        if (!empty($filters['escalation'])) {
            $sql .= ' AND t.escalation = ?';
            $params[] = $filters['escalation'];
        }
        if (isset($filters['is_resolved']) && $filters['is_resolved'] !== '') {
            $sql .= ' AND t.is_resolved = ?';
            $params[] = (int)$filters['is_resolved'];
        }
        if (!empty($filters['sucursal_id'])) {
            $sql .= ' AND t.sucursal_id = ?';
            $params[] = (int)$filters['sucursal_id'];
        }
        if (!empty($filters['project_id'])) {
            $sql .= ' AND t.project_id = ?';
            $params[] = (int)$filters['project_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= ' AND DATE(t.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= ' AND DATE(t.created_at) <= ?';
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['highlighted'])) {
            $sql .= ' AND u.is_vip = 1';
        }

        $sql .= ' ORDER BY u.is_vip DESC, t.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, a.name as assigned_name, s.nombre as sucursal_nombre,
                    u.is_vip as requester_is_vip, u.highlight_color as requester_highlight_color
             FROM tickets t
             LEFT JOIN users a ON t.assigned_to = a.id
             LEFT JOIN sucursales s ON t.sucursal_id = s.id
             LEFT JOIN users u ON t.user_id = u.id
             WHERE t.user_id = ?
             ORDER BY t.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findByAssignedTo(int $devId): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.name as requester_user_name 
             FROM tickets t 
             LEFT JOIN users u ON t.user_id = u.id 
             WHERE t.assigned_to = ? 
             ORDER BY t.created_at DESC'
        );
        $stmt->execute([$devId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tickets
             (title, description, type, impact, priority_user, status, phase, steps_to_reproduce, technical_context, requester_name, requester_email, user_id, assigned_to, escalation, is_resolved, sucursal_id, project_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'] ?? null,
            $data['description'],
            $data['type'] ?? 'support',
            $data['impact'] ?? 'medium',
            $data['priority_user'] ?? 'medium',
            $data['status'] ?? 'new',
            $data['phase'] ?? 'information',
            $data['steps_to_reproduce'] ?? null,
            $data['technical_context'] ?? null,
            $data['requester_name'] ?? null,
            $data['requester_email'],
            $data['user_id'] ?? null,
            $data['assigned_to'] ?? null,
            $data['escalation'] ?? 'none',
            $data['is_resolved'] ?? 0,
            $data['sucursal_id'] ?? null,
            $data['project_id'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Tickets visible to a colaborador: only type='cambio' within their assigned projects.
     */
    public function findForColaborador(int $userId, array $filters = []): array
    {
        $sql = 'SELECT t.*, u.name as requester_user_name, u.is_vip as requester_is_vip, u.highlight_color as requester_highlight_color,
                       a.name as assigned_name, s.nombre as sucursal_nombre, p.name as project_name
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN sucursales s ON t.sucursal_id = s.id
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN user_projects up ON t.project_id = up.project_id AND up.user_id = ?
                WHERE t.type = \'cambio\'';
        $params = [$userId];

        if (!empty($filters['status'])) {
            $sql .= ' AND t.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['project_id'])) {
            $sql .= ' AND t.project_id = ?';
            $params[] = (int)$filters['project_id'];
        }

        $sql .= ' ORDER BY t.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
        $sql = 'UPDATE tickets SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function addNote(int $ticketId, int $userId, string $note): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)'
        );
        $stmt->execute([$ticketId, $userId, $note]);
        return (int)$this->db->lastInsertId();
    }

    public function getNotes(int $ticketId): array
    {
        $stmt = $this->db->prepare(
            'SELECT tn.*, u.name as user_name 
             FROM ticket_notes tn 
             JOIN users u ON tn.user_id = u.id 
             WHERE tn.ticket_id = ? 
             ORDER BY tn.created_at ASC'
        );
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
}
