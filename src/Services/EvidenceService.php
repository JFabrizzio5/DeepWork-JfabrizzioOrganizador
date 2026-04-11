<?php
namespace App\Services;

use App\Core\Session;
use App\Repositories\EvidenceRepository;
use App\Repositories\TicketRepository;

class EvidenceService
{
    private EvidenceRepository $evidenceRepo;
    private TicketRepository $ticketRepo;
    private array $allowedTypes = ['png', 'jpg', 'jpeg', 'pdf', 'xml', 'zip', 'mp4'];

    public function __construct()
    {
        $this->evidenceRepo = new EvidenceRepository();
        $this->ticketRepo = new TicketRepository();
    }

    public function upload(array $file, int $ticketId, int $userId): bool|string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload error: ' . $file['error'];
        }

        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowedTypes)) {
            return 'File type not allowed. Allowed: ' . implode(', ', $this->allowedTypes);
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/tickets/' . $ticketId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid('ev_', true) . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return 'Failed to move uploaded file.';
        }

        $this->evidenceRepo->create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'filename' => $filename,
            'original_name' => $originalName,
            'file_type' => $ext,
            'file_size' => $file['size'],
        ]);

        return true;
    }

    public function getByTicket(int $ticketId, array $requestingUser): array
    {
        $role = $requestingUser['role'];
        $userId = $requestingUser['id'];

        if (in_array($role, ['admin', 'dev'])) {
            return $this->evidenceRepo->findByTicketId($ticketId);
        }

        $ticket = $this->ticketRepo->findById($ticketId);
        if ($ticket && (int)$ticket['user_id'] === $userId) {
            return $this->evidenceRepo->findByTicketId($ticketId);
        }

        return [];
    }

    public function delete(int $id, int $userId): bool
    {
        $evidence = $this->evidenceRepo->findById($id);
        if (!$evidence) {
            return false;
        }

        $role = Session::get('user_role');
        if ($role !== 'admin' && (int)$evidence['user_id'] !== $userId) {
            return false;
        }

        $filePath = dirname(__DIR__, 2) . '/public/uploads/tickets/' . $evidence['ticket_id'] . '/' . $evidence['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->evidenceRepo->delete($id);
    }

    public function findById(int $id): ?array
    {
        return $this->evidenceRepo->findById($id);
    }
}
