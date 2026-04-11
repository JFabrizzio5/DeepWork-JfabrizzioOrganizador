<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\TicketService;
use App\Services\EvidenceService;
use App\Services\UserService;

class TicketController
{
    private TicketService $ticketService;
    private EvidenceService $evidenceService;
    private UserService $userService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->ticketService = new TicketService();
        $this->evidenceService = new EvidenceService();
        $this->userService = new UserService();
        $this->request = new Request();
    }

    public function index(): void
    {
        $user = $this->getCurrentUser();
        $filters = [
            'status' => $this->request->get('status', ''),
            'type' => $this->request->get('type', ''),
            'impact' => $this->request->get('impact', ''),
        ];

        if ($user['role'] === 'user') {
            $tickets = $this->ticketService->getUserTickets($user['id']);
        } else {
            $tickets = $this->ticketService->getAll($filters);
        }

        Response::view('tickets/list', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $user,
            'tickets' => $tickets,
            'filters' => $filters,
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $user = $this->getCurrentUser();
        Response::view('tickets/create', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $user,
            'error' => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $user = $this->getCurrentUser();

        $data = [
            'title' => trim($this->request->post('title', '')),
            'description' => trim($this->request->post('description', '')),
            'type' => $this->request->post('type', 'support'),
            'impact' => $this->request->post('impact', 'medium'),
            'priority_user' => $this->request->post('priority_user', 'medium'),
            'steps_to_reproduce' => trim($this->request->post('steps_to_reproduce', '')),
            'technical_context' => trim($this->request->post('technical_context', '')),
            'requester_name' => trim($this->request->post('requester_name', '')),
            'requester_email' => trim($this->request->post('requester_email', '')),
        ];

        if (empty($data['description'])) {
            Session::flash('error', 'Description is required.');
            Response::redirect($_ENV['APP_URL'] . '/tickets/create');
        }
        if (empty($data['requester_email'])) {
            Session::flash('error', 'Requester email is required.');
            Response::redirect($_ENV['APP_URL'] . '/tickets/create');
        }

        $ticketId = $this->ticketService->create($data, $user['id']);

        if (!empty($_FILES['evidence']['name'][0])) {
            $files = $_FILES['evidence'];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name' => $files['name'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i],
                        'type' => $files['type'][$i],
                    ];
                    $this->evidenceService->upload($singleFile, $ticketId, $user['id']);
                }
            }
        }

        Session::flash('success', 'Ticket #' . $ticketId . ' created successfully.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $ticketId);
    }

    public function show(string $id): void
    {
        $user = $this->getCurrentUser();
        $ticket = $this->ticketService->getById((int)$id);

        if (!$ticket) {
            Response::abort(404, 'Ticket not found.');
        }

        if ($user['role'] === 'user' && (int)$ticket['user_id'] !== $user['id']) {
            Response::abort(403, 'Access denied.');
        }

        $notes = $this->ticketService->getNotes((int)$id);
        $evidences = $this->evidenceService->getByTicket((int)$id, $user);
        $developers = $this->userService->getDevelopers();

        Response::view('tickets/detail', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $user,
            'ticket' => $ticket,
            'notes' => $notes,
            'evidences' => $evidences,
            'developers' => $developers,
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $status = $this->request->post('status', '');
        $phase = $this->request->post('phase', '');

        if ($status) {
            $this->ticketService->updateStatus((int)$id, $status, $user['id']);
        }
        if ($phase) {
            $this->ticketService->updatePhase((int)$id, $phase);
        }

        Session::flash('success', 'Ticket updated.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function addNote(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $note = trim($this->request->post('note', ''));
        if (empty($note)) {
            Session::flash('error', 'Note cannot be empty.');
            Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
        }

        $this->ticketService->addNote((int)$id, $user['id'], $note);
        Session::flash('success', 'Note added.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function assign(string $id): void
    {
        $user = $this->getCurrentUser();
        if ($user['role'] !== 'admin') {
            Response::abort(403, 'Access denied.');
        }

        $devId = (int)$this->request->post('assigned_to', 0);
        if ($devId > 0) {
            $this->ticketService->assignTo((int)$id, $devId);
            Session::flash('success', 'Ticket assigned successfully.');
        }
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function serveEvidence(string $id, string $evidenceId): void
    {
        $user = $this->getCurrentUser();
        $evidence = $this->evidenceService->findById((int)$evidenceId);

        if (!$evidence) {
            Response::abort(404, 'Evidence not found.');
        }
        if ((int)$evidence['ticket_id'] !== (int)$id) {
            Response::abort(403, 'Access denied.');
        }

        $ticket = $this->ticketService->getById((int)$id);
        $isOwner = $ticket && (int)$ticket['user_id'] === $user['id'];
        $isAdminOrDev = in_array($user['role'], ['admin', 'dev']);

        if (!$isOwner && !$isAdminOrDev) {
            Response::abort(403, 'Access denied.');
        }

        $filePath = dirname(__DIR__, 2) . '/public/uploads/tickets/' . $id . '/' . $evidence['filename'];
        if (!file_exists($filePath)) {
            Response::abort(404, 'File not found.');
        }

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'xml'  => 'application/xml',
            'zip'  => 'application/zip',
            'mp4'  => 'video/mp4',
        ];

        $ext = strtolower(pathinfo($evidence['filename'], PATHINFO_EXTENSION));
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $evidence['original_name'] . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    private function getCurrentUser(): array
    {
        return [
            'id'    => (int)Session::get('user_id'),
            'name'  => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'role'  => Session::get('user_role'),
        ];
    }
}
