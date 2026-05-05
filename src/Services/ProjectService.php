<?php
namespace App\Services;

use App\Repositories\ProjectRepository;
use App\Repositories\ProjectFileRepository;
use App\Repositories\ProjectNoteRepository;
use App\Repositories\UserProjectProfileRepository;

class ProjectService
{
    private ProjectRepository $repo;
    private ProjectFileRepository $fileRepo;
    private ProjectNoteRepository $noteRepo;
    private UserProjectProfileRepository $profileRepo;

    private array $allowedTypes = [
        'png', 'jpg', 'jpeg', 'gif', 'pdf', 'doc', 'docx',
        'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'xml',
        'zip', 'rar', '7z', 'tar', 'gz', 'mp4', 'mp3', 'json',
        'sql', 'md', 'html', 'css', 'js', 'php', 'py',
    ];

    public function __construct()
    {
        $this->repo        = new ProjectRepository();
        $this->fileRepo    = new ProjectFileRepository();
        $this->noteRepo    = new ProjectNoteRepository();
        $this->profileRepo = new UserProjectProfileRepository();
    }

    // ── CRUD ──────────────────────────────────────────────

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): int
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    // ── User-project access ───────────────────────────────

    public function getProjectsForUser(int $userId): array
    {
        return $this->repo->getProjectsForUser($userId);
    }

    public function setUserProjects(int $userId, array $projectIds): void
    {
        $this->repo->setUserProjects($userId, $projectIds);
    }

    public function getUsersForProject(int $projectId): array
    {
        return $this->repo->getUsersForProject($projectId);
    }

    public function userHasProject(int $userId, int $projectId): bool
    {
        return $this->repo->userHasProject($userId, $projectId);
    }

    // ── Files ─────────────────────────────────────────────

    public function getFiles(int $projectId): array
    {
        return $this->fileRepo->findByProject($projectId);
    }

    public function getFileById(int $fileId): ?array
    {
        return $this->fileRepo->findById($fileId);
    }

    public function uploadFile(array $file, int $projectId, int $userId, string $description = ''): bool|string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Error al subir el archivo: ' . $file['error'];
        }

        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowedTypes)) {
            return 'Tipo de archivo no permitido. Permitidos: ' . implode(', ', $this->allowedTypes);
        }

        $uploadDir = dirname(__DIR__, 2) . '/storage/projects/' . $projectId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename    = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return 'No se pudo guardar el archivo.';
        }

        $this->fileRepo->create([
            'project_id'    => $projectId,
            'user_id'       => $userId,
            'filename'      => $filename,
            'original_name' => $originalName,
            'file_type'     => $ext,
            'file_size'     => $file['size'],
            'description'   => $description,
        ]);

        return true;
    }

    public function deleteFile(int $projectId, int $fileId): bool
    {
        $file = $this->fileRepo->findById($fileId);
        if (!$file || (int)$file['project_id'] !== $projectId) {
            return false;
        }

        $path = dirname(__DIR__, 2) . '/storage/projects/' . $projectId . '/' . $file['filename'];
        if (file_exists($path)) {
            unlink($path);
        }

        return $this->fileRepo->delete($fileId);
    }

    // ── Notes ─────────────────────────────────────────────

    public function getNotes(int $projectId): array
    {
        return $this->noteRepo->findByProject($projectId);
    }

    public function addNote(int $projectId, int $userId, string $note): int
    {
        return $this->noteRepo->create($projectId, $userId, $note);
    }

    public function deleteNote(int $projectId, int $noteId): bool
    {
        $note = $this->noteRepo->findById($noteId);
        if (!$note || (int)$note['project_id'] !== $projectId) {
            return false;
        }
        return $this->noteRepo->delete($noteId);
    }

    // ── Profiles ──────────────────────────────────────────

    public function getProfile(int $userId, int $projectId): ?array
    {
        return $this->profileRepo->findByUserAndProject($userId, $projectId);
    }

    public function getProfilesByProject(int $projectId): array
    {
        return $this->profileRepo->findByProject($projectId);
    }

    public function updateProfile(int $userId, int $projectId, array $data): void
    {
        $this->profileRepo->upsert($userId, $projectId, $data);
    }
}

