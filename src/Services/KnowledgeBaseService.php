<?php
namespace App\Services;

use App\Repositories\KnowledgeBaseRepository;

class KnowledgeBaseService
{
    private KnowledgeBaseRepository $kbRepo;
    private array $allowedTypes = [
        'png', 'jpg', 'jpeg', 'gif', 'pdf', 'doc', 'docx',
        'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'xml',
        'zip', 'rar', '7z', 'tar', 'gz', 'mp4', 'mp3', 'json',
        'sql', 'md', 'html', 'css', 'js', 'php', 'py',
    ];

    public function __construct()
    {
        $this->kbRepo = new KnowledgeBaseRepository();
    }

    public function getAll(array $filters = []): array
    {
        return $this->kbRepo->findAll($filters);
    }

    public function getById(int $id): ?array
    {
        return $this->kbRepo->findById($id);
    }

    public function search(string $query): array
    {
        return $this->kbRepo->search($query);
    }

    public function create(array $data, int $userId): int
    {
        $data['created_by'] = $userId;
        return $this->kbRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->kbRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->kbRepo->delete($id);
    }

    public function uploadFile(array $file, int $articleId, int $userId): bool|string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload error: ' . $file['error'];
        }

        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowedTypes)) {
            return 'File type not allowed. Allowed: ' . implode(', ', $this->allowedTypes);
        }

        // Store files outside the public document root for security
        $uploadDir = dirname(__DIR__, 2) . '/storage/knowledge/' . $articleId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return 'Failed to move uploaded file.';
        }

        $this->kbRepo->createFile([
            'article_id'    => $articleId,
            'user_id'       => $userId,
            'filename'      => $filename,
            'original_name' => $originalName,
            'file_type'     => $ext,
            'file_size'     => $file['size'],
        ]);

        return true;
    }

    public function getFilesByArticleId(int $articleId): array
    {
        return $this->kbRepo->findFilesByArticleId($articleId);
    }

    public function getFileById(int $fileId): ?array
    {
        return $this->kbRepo->findFileById($fileId);
    }

    public function deleteFile(int $articleId, int $fileId): bool
    {
        $file = $this->kbRepo->findFileById($fileId);
        if (!$file || (int)$file['article_id'] !== $articleId) {
            return false;
        }

        $filePath = dirname(__DIR__, 2) . '/storage/knowledge/' . $articleId . '/' . $file['filename'];
        if (file_exists($filePath) && !unlink($filePath)) {
            return false;
        }

        return $this->kbRepo->deleteFile($fileId);
    }

    public function deleteWithFiles(int $id): bool
    {
        // Delete physical files
        $files = $this->kbRepo->findFilesByArticleId($id);
        $uploadDir = dirname(__DIR__, 2) . '/storage/knowledge/' . $id . '/';
        foreach ($files as $file) {
            $path = $uploadDir . $file['filename'];
            if (file_exists($path)) {
                unlink($path);
            }
        }
        if (is_dir($uploadDir)) {
            rmdir($uploadDir);
        }

        // Delete DB records (files cascade via FK)
        return $this->kbRepo->delete($id);
    }
}
