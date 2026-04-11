<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\ApiAuth;
use App\Core\Response;
use App\Services\TicketService;
use App\Services\EvidenceService;
use App\Services\UserService;

/**
 * REST API controller — all responses are JSON.
 *
 * Authentication: every route is protected by ApiKeyMiddleware.
 * The authenticated caller is available via ApiAuth::user().
 *
 * Endpoints
 * ─────────────────────────────────────────────
 * GET    /api/tickets                   List tickets (role-scoped)
 * POST   /api/tickets                   Create ticket
 * GET    /api/tickets/{id}              Get ticket + notes + evidences
 * POST   /api/tickets/{id}/status       Update status   (dev, admin)
 * POST   /api/tickets/{id}/phase        Update phase    (dev, admin)
 * POST   /api/tickets/{id}/note         Add internal note (dev, admin)
 * POST   /api/tickets/{id}/assign       Assign to dev   (admin)
 * POST   /api/tickets/{id}/evidence     Upload evidence file (multipart)
 * GET    /api/tickets/{id}/evidence     List evidences for a ticket
 * GET    /api/users                     List users      (admin)
 */
class ApiController
{
    private TicketService $ticketService;
    private EvidenceService $evidenceService;
    private UserService $userService;

    public function __construct()
    {
        $this->ticketService   = new TicketService();
        $this->evidenceService = new EvidenceService();
        $this->userService     = new UserService();
    }

    // ──────────────────────────────────────────────────────────
    // GET /api/tickets
    // Query filters: status, type, impact
    // ──────────────────────────────────────────────────────────
    public function listTickets(): void
    {
        $user    = ApiAuth::user();
        $filters = array_filter([
            'status' => $_GET['status'] ?? null,
            'type'   => $_GET['type']   ?? null,
            'impact' => $_GET['impact'] ?? null,
        ]);

        if ($user['role'] === 'user') {
            $tickets = $this->ticketService->getUserTickets($user['id']);
        } elseif ($user['role'] === 'dev') {
            $tickets = $this->ticketService->getDevTickets($user['id']);
        } else {
            $tickets = $this->ticketService->getAll($filters);
        }

        Response::json(['data' => $tickets, 'count' => count($tickets)]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /api/tickets
    // Body (JSON or form-encoded):
    //   description*  requester_email*  title  type  impact
    //   priority_user  steps_to_reproduce  technical_context
    //   requester_name
    // ──────────────────────────────────────────────────────────
    public function createTicket(): void
    {
        $user = ApiAuth::user();
        $body = $this->body();

        if (empty($body['description'])) {
            Response::json(['error' => 'description is required.'], 422);
        }
        if (empty($body['requester_email'])) {
            Response::json(['error' => 'requester_email is required.'], 422);
        }

        $id     = $this->ticketService->create($body, $user['id']);
        $ticket = $this->ticketService->getById($id);

        Response::json(['message' => 'Ticket created.', 'data' => $ticket], 201);
    }

    // ──────────────────────────────────────────────────────────
    // GET /api/tickets/{id}
    // ──────────────────────────────────────────────────────────
    public function getTicket(string $id): void
    {
        $user   = ApiAuth::user();
        $ticket = $this->ticketService->getById((int) $id);

        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        // Regular users may only see their own tickets
        if ($user['role'] === 'user' && (int) $ticket['user_id'] !== $user['id']) {
            Response::json(['error' => 'Forbidden.'], 403);
        }

        $notes     = $this->ticketService->getNotes((int) $id);
        $evidences = $this->evidenceService->getByTicket((int) $id, $user);

        // Remove internal filesystem filename from evidence list
        $safeEvidences = array_map(fn($e) => [
            'id'            => $e['id'],
            'original_name' => $e['original_name'],
            'file_type'     => $e['file_type'],
            'file_size'     => $e['file_size'],
            'uploader_name' => $e['uploader_name'] ?? null,
            'created_at'    => $e['created_at'],
        ], $evidences);

        Response::json(['data' => array_merge($ticket, [
            'notes'     => $notes,
            'evidences' => $safeEvidences,
        ])]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /api/tickets/{id}/status
    // Body: { "status": "new|in_progress|review|done" }
    // ──────────────────────────────────────────────────────────
    public function updateStatus(string $id): void
    {
        if (!ApiAuth::hasRole('admin', 'dev')) {
            Response::json(['error' => 'Forbidden. Requires admin or dev role.'], 403);
        }

        $valid = ['new', 'in_progress', 'review', 'done'];
        $body  = $this->body();

        if (empty($body['status']) || !in_array($body['status'], $valid, true)) {
            Response::json(['error' => 'Invalid status. Allowed: ' . implode(', ', $valid)], 422);
        }

        $ticket = $this->ticketService->getById((int) $id);
        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        $this->ticketService->updateStatus((int) $id, $body['status'], ApiAuth::id());
        Response::json(['message' => 'Status updated.', 'status' => $body['status']]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /api/tickets/{id}/phase
    // Body: { "phase": "information|creation|in_progress|review|done" }
    // ──────────────────────────────────────────────────────────
    public function updatePhase(string $id): void
    {
        if (!ApiAuth::hasRole('admin', 'dev')) {
            Response::json(['error' => 'Forbidden. Requires admin or dev role.'], 403);
        }

        $valid = ['information', 'creation', 'in_progress', 'review', 'done'];
        $body  = $this->body();

        if (empty($body['phase']) || !in_array($body['phase'], $valid, true)) {
            Response::json(['error' => 'Invalid phase. Allowed: ' . implode(', ', $valid)], 422);
        }

        $ticket = $this->ticketService->getById((int) $id);
        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        $this->ticketService->updatePhase((int) $id, $body['phase']);
        Response::json(['message' => 'Phase updated.', 'phase' => $body['phase']]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /api/tickets/{id}/note
    // Body: { "note": "..." }
    // ──────────────────────────────────────────────────────────
    public function addNote(string $id): void
    {
        if (!ApiAuth::hasRole('admin', 'dev')) {
            Response::json(['error' => 'Forbidden. Requires admin or dev role.'], 403);
        }

        $body = $this->body();

        if (empty($body['note'])) {
            Response::json(['error' => 'note is required.'], 422);
        }

        $ticket = $this->ticketService->getById((int) $id);
        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        $this->ticketService->addNote((int) $id, ApiAuth::id(), $body['note']);
        Response::json(['message' => 'Note added.']);
    }

    // ──────────────────────────────────────────────────────────
    // POST /api/tickets/{id}/assign
    // Body: { "dev_id": 5 }
    // ──────────────────────────────────────────────────────────
    public function assign(string $id): void
    {
        if (!ApiAuth::hasRole('admin')) {
            Response::json(['error' => 'Forbidden. Requires admin role.'], 403);
        }

        $body = $this->body();

        if (empty($body['dev_id'])) {
            Response::json(['error' => 'dev_id is required.'], 422);
        }

        $ticket = $this->ticketService->getById((int) $id);
        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        $this->ticketService->assignTo((int) $id, (int) $body['dev_id']);
        Response::json(['message' => 'Ticket assigned.', 'assigned_to' => (int) $body['dev_id']]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /api/tickets/{id}/evidence   (multipart/form-data)
    // File field: "evidence"
    // Allowed types: png, jpg, jpeg, pdf, xml, zip, mp4
    // ──────────────────────────────────────────────────────────
    public function uploadEvidence(string $id): void
    {
        $user   = ApiAuth::user();
        $ticket = $this->ticketService->getById((int) $id);

        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        // Regular users may only upload to their own tickets
        if ($user['role'] === 'user' && (int) $ticket['user_id'] !== $user['id']) {
            Response::json(['error' => 'Forbidden.'], 403);
        }

        if (empty($_FILES['evidence'])) {
            Response::json(['error' => 'No file uploaded. Use multipart/form-data with field name "evidence".'], 422);
        }

        $result = $this->evidenceService->upload($_FILES['evidence'], (int) $id, $user['id']);

        if ($result !== true) {
            Response::json(['error' => $result], 422);
        }

        Response::json(['message' => 'Evidence uploaded successfully.'], 201);
    }

    // ──────────────────────────────────────────────────────────
    // GET /api/tickets/{id}/evidence
    // ──────────────────────────────────────────────────────────
    public function listEvidence(string $id): void
    {
        $user   = ApiAuth::user();
        $ticket = $this->ticketService->getById((int) $id);

        if ($ticket === null) {
            Response::json(['error' => 'Ticket not found.'], 404);
        }

        $evidences = $this->evidenceService->getByTicket((int) $id, $user);

        $safe = array_map(fn($e) => [
            'id'            => $e['id'],
            'original_name' => $e['original_name'],
            'file_type'     => $e['file_type'],
            'file_size'     => $e['file_size'],
            'uploader_name' => $e['uploader_name'] ?? null,
            'created_at'    => $e['created_at'],
        ], $evidences);

        Response::json(['data' => $safe, 'count' => count($safe)]);
    }

    // ──────────────────────────────────────────────────────────
    // GET /api/users   (admin only)
    // ──────────────────────────────────────────────────────────
    public function listUsers(): void
    {
        if (!ApiAuth::hasRole('admin')) {
            Response::json(['error' => 'Forbidden. Requires admin role.'], 403);
        }

        $users = $this->userService->getAll();

        $safe = array_map(fn($u) => [
            'id'         => $u['id'],
            'name'       => $u['name'],
            'email'      => $u['email'],
            'role'       => $u['role'],
            'created_at' => $u['created_at'],
        ], $users);

        Response::json(['data' => $safe, 'count' => count($safe)]);
    }

    // ──────────────────────────────────────────────────────────
    // Helper: parse request body
    // Supports application/json and application/x-www-form-urlencoded
    // ──────────────────────────────────────────────────────────
    private function body(): array
    {
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($ct, 'application/json')) {
            $decoded = json_decode(file_get_contents('php://input'), true);
            return is_array($decoded) ? $decoded : [];
        }
        return $_POST;
    }
}
