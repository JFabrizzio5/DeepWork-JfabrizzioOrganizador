<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\TicketService;
use App\Services\EvidenceService;
use App\Services\UserService;
use App\Services\ProjectService;
use App\Repositories\SucursalRepository;

class TicketController
{
    private TicketService $ticketService;
    private EvidenceService $evidenceService;
    private UserService $userService;
    private SucursalRepository $sucursalRepo;
    private ProjectService $projectService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->ticketService   = new TicketService();
        $this->evidenceService = new EvidenceService();
        $this->userService     = new UserService();
        $this->sucursalRepo    = new SucursalRepository();
        $this->projectService  = new ProjectService();
        $this->request         = new Request();
    }

    public function index(): void
    {
        $user = $this->getCurrentUser();
        $filters = [
            'status'      => $this->request->get('status', ''),
            'type'        => $this->request->get('type', ''),
            'impact'      => $this->request->get('impact', ''),
            'escalation'  => $this->request->get('escalation', ''),
            'is_resolved' => $this->request->get('is_resolved', ''),
            'sucursal_id' => $this->request->get('sucursal_id', ''),
            'project_id'  => $this->request->get('project_id', ''),
            'date_from'   => $this->request->get('date_from', ''),
            'date_to'     => $this->request->get('date_to', ''),
            'highlighted' => $this->request->get('highlighted', ''),
        ];

        if ($user['role'] === 'user') {
            $tickets = $this->ticketService->getUserTickets($user['id']);
        } elseif ($user['role'] === 'colaborador') {
            $tickets = $this->ticketService->getColaboradorTickets($user['id'], $filters);
        } else {
            $tickets = $this->ticketService->getAll($filters);
        }

        $sucursales      = $this->sucursalRepo->findAll();
        $userProjects    = ($user['role'] === 'admin')
            ? $this->projectService->getAll()
            : $this->projectService->getProjectsForUser($user['id']);

        Response::view('tickets/list', [
            'appUrl'      => $_ENV['APP_URL'],
            'user'        => $user,
            'tickets'     => $tickets,
            'filters'     => $filters,
            'sucursales'  => $sucursales,
            'projects'    => $userProjects,
            'success'     => Session::getFlash('success'),
            'error'       => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $user = $this->getCurrentUser();
        $sucursales = $this->sucursalRepo->findAll();
        $userProjects = ($user['role'] === 'admin')
            ? $this->projectService->getAll()
            : $this->projectService->getProjectsForUser($user['id']);

        Response::view('tickets/create', [
            'appUrl'    => $_ENV['APP_URL'],
            'user'      => $user,
            'sucursales' => $sucursales,
            'projects'  => $userProjects,
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $user = $this->getCurrentUser();

        $data = [
            'title'              => trim($this->request->post('title', '')),
            'description'        => trim($this->request->post('description', '')),
            'type'               => $this->request->post('type', 'support'),
            'impact'             => $this->request->post('impact', 'medium'),
            'priority_user'      => $this->request->post('priority_user', 'medium'),
            'steps_to_reproduce' => trim($this->request->post('steps_to_reproduce', '')),
            'technical_context'  => trim($this->request->post('technical_context', '')),
            'requester_name'     => trim($this->request->post('requester_name', '')),
            'requester_email'    => trim($this->request->post('requester_email', '')),
            'sucursal_id'        => $this->request->post('sucursal_id', null) ?: null,
            'project_id'         => $this->request->post('project_id', null) ?: null,
        ];

        if (empty($data['description'])) {
            Session::flash('error', 'La descripción es requerida.');
            Response::redirect($_ENV['APP_URL'] . '/tickets/create');
        }
        if (empty($data['requester_email'])) {
            Session::flash('error', 'El correo del solicitante es requerido.');
            Response::redirect($_ENV['APP_URL'] . '/tickets/create');
        }

        $ticketId = $this->ticketService->create($data, $user['id']);

        if (!empty($_FILES['evidence']['name'][0])) {
            $files = $_FILES['evidence'];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name'     => $files['name'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                        'type'     => $files['type'][$i],
                    ];
                    $this->evidenceService->upload($singleFile, $ticketId, $user['id']);
                }
            }
        }

        Session::flash('success', 'Ticket #' . $ticketId . ' creado exitosamente.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $ticketId);
    }

    public function show(string $id): void
    {
        $user = $this->getCurrentUser();
        $ticket = $this->ticketService->getById((int)$id);

        if (!$ticket) {
            Response::abort(404, 'Ticket no encontrado.');
        }

        // user: only their own tickets
        if ($user['role'] === 'user' && (int)$ticket['user_id'] !== $user['id']) {
            Response::abort(403, 'Acceso denegado.');
        }

        // colaborador: only cambio tickets within their assigned projects
        if ($user['role'] === 'colaborador') {
            if ($ticket['type'] !== 'cambio') {
                Response::abort(403, 'Acceso denegado.');
            }
            $projectId = (int)($ticket['project_id'] ?? 0);
            if (!$projectId || !$this->projectService->userHasProject($user['id'], $projectId)) {
                Response::abort(403, 'Acceso denegado.');
            }
        }

        $notes      = $this->ticketService->getNotes((int)$id);
        $evidences  = $this->evidenceService->getByTicket((int)$id, $user);
        $developers = $this->userService->getDevelopers();
        $sucursales = $this->sucursalRepo->findAll();

        Response::view('tickets/detail', [
            'appUrl'    => $_ENV['APP_URL'],
            'user'      => $user,
            'ticket'    => $ticket,
            'notes'     => $notes,
            'evidences' => $evidences,
            'developers' => $developers,
            'sucursales' => $sucursales,
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Acceso denegado.');
        }

        $status = $this->request->post('status', '');
        $phase  = $this->request->post('phase', '');

        if ($status) {
            $this->ticketService->updateStatus((int)$id, $status, $user['id']);
        }
        if ($phase) {
            $this->ticketService->updatePhase((int)$id, $phase);
        }

        Session::flash('success', 'Ticket actualizado.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function updateEscalation(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Acceso denegado.');
        }

        $escalation = $this->request->post('escalation', 'none');
        $allowed = ['none', 'escalate', 'no_escalate'];
        if (!in_array($escalation, $allowed)) {
            $escalation = 'none';
        }

        $this->ticketService->setEscalation((int)$id, $escalation);
        Session::flash('success', 'Escalación actualizada.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function toggleResolved(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Acceso denegado.');
        }

        $ticket = $this->ticketService->getById((int)$id);
        if (!$ticket) {
            Response::abort(404, 'Ticket no encontrado.');
        }

        $newVal = $ticket['is_resolved'] ? 0 : 1;
        $this->ticketService->setResolved((int)$id, $newVal);
        Session::flash('success', $newVal ? 'Ticket marcado como resuelto.' : 'Ticket marcado como no resuelto.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function addNote(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Acceso denegado.');
        }

        $note = trim($this->request->post('note', ''));
        if (empty($note)) {
            Session::flash('error', 'La nota no puede estar vacía.');
            Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
        }

        $this->ticketService->addNote((int)$id, $user['id'], $note);
        Session::flash('success', 'Nota agregada.');
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function assign(string $id): void
    {
        $user = $this->getCurrentUser();
        if ($user['role'] !== 'admin') {
            Response::abort(403, 'Acceso denegado.');
        }

        $devId = (int)$this->request->post('assigned_to', 0);
        if ($devId > 0) {
            $this->ticketService->assignTo((int)$id, $devId);
            Session::flash('success', 'Ticket asignado exitosamente.');
        }
        Response::redirect($_ENV['APP_URL'] . '/tickets/' . $id);
    }

    public function serveEvidence(string $id, string $evidenceId): void
    {
        $user     = $this->getCurrentUser();
        $evidence = $this->evidenceService->findById((int)$evidenceId);

        if (!$evidence) {
            Response::abort(404, 'Evidencia no encontrada.');
        }
        if ((int)$evidence['ticket_id'] !== (int)$id) {
            Response::abort(403, 'Acceso denegado.');
        }

        $ticket    = $this->ticketService->getById((int)$id);
        $isOwner   = $ticket && (int)$ticket['user_id'] === $user['id'];
        $isAdminOrDev = in_array($user['role'], ['admin', 'dev']);

        if (!$isOwner && !$isAdminOrDev) {
            Response::abort(403, 'Acceso denegado.');
        }

        $filePath = dirname(__DIR__, 2) . '/public/uploads/tickets/' . $id . '/' . $evidence['filename'];
        if (!file_exists($filePath)) {
            Response::abort(404, 'Archivo no encontrado.');
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

        $ext  = strtolower(pathinfo($evidence['filename'], PATHINFO_EXTENSION));
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

