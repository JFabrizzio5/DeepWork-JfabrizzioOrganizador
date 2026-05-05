<?php
namespace App\Services;

use App\Core\Session;
use App\Repositories\TicketRepository;

class TicketService
{
    private TicketRepository $ticketRepo;

    public function __construct()
    {
        $this->ticketRepo = new TicketRepository();
    }

    public function create(array $data, int $userId): int
    {
        $data['user_id'] = $userId;
        return $this->ticketRepo->create($data);
    }

    public function getAll(array $filters = []): array
    {
        return $this->ticketRepo->findAll($filters);
    }

    public function getById(int $id): ?array
    {
        return $this->ticketRepo->findById($id);
    }

    public function updateStatus(int $id, string $status, int $userId): bool
    {
        $role = Session::get('user_role');
        if (!in_array($role, ['admin', 'dev'])) {
            return false;
        }
        return $this->ticketRepo->update($id, ['status' => $status]);
    }

    public function updatePhase(int $id, string $phase): bool
    {
        return $this->ticketRepo->update($id, ['phase' => $phase]);
    }

    public function addNote(int $ticketId, int $userId, string $note): bool
    {
        return $this->ticketRepo->addNote($ticketId, $userId, $note) > 0;
    }

    public function assignTo(int $ticketId, int $devId): bool
    {
        return $this->ticketRepo->update($ticketId, ['assigned_to' => $devId]);
    }

    public function getUserTickets(int $userId): array
    {
        return $this->ticketRepo->findByUserId($userId);
    }

    public function getDevTickets(int $devId): array
    {
        return $this->ticketRepo->findByAssignedTo($devId);
    }

    public function getNotes(int $ticketId): array
    {
        return $this->ticketRepo->getNotes($ticketId);
    }

    public function setEscalation(int $id, string $escalation): bool
    {
        return $this->ticketRepo->update($id, ['escalation' => $escalation]);
    }

    public function setResolved(int $id, int $resolved): bool
    {
        return $this->ticketRepo->update($id, ['is_resolved' => $resolved]);
    }

    public function getColaboradorTickets(int $userId, array $filters = []): array
    {
        return $this->ticketRepo->findForColaborador($userId, $filters);
    }
}