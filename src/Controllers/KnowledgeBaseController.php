<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\KnowledgeBaseService;
use App\Services\ProjectService;

class KnowledgeBaseController
{
    private KnowledgeBaseService $kbService;
    private ProjectService $projectService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->kbService      = new KnowledgeBaseService();
        $this->projectService = new ProjectService();
        $this->request        = new Request();
    }

    public function index(): void
    {
        $user = $this->getCurrentUser();
        $filters = [
            'tag_type'   => $this->request->get('tag_type', ''),
            'project_id' => $this->request->get('project_id', ''),
        ];

        if ($user['role'] === 'colaborador') {
            $articles = $this->kbService->getAllForColaborador($user['id'], $filters);
        } else {
            $articles = $this->kbService->getAll($filters);
        }

        $userProjects = ($user['role'] === 'admin')
            ? $this->projectService->getAll()
            : $this->projectService->getProjectsForUser($user['id']);

        Response::view('knowledge/index', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $this->getCurrentUser(),
            'articles' => $articles,
            'filters'  => $filters,
            'projects' => $userProjects,
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $userProjects = ($user['role'] === 'admin')
            ? $this->projectService->getAll()
            : $this->projectService->getProjectsForUser($user['id']);

        Response::view('knowledge/create', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $user,
            'projects' => $userProjects,
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $title = trim($this->request->post('title', ''));
        $content = trim($this->request->post('content', ''));

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Title and content are required.');
            Response::redirect($_ENV['APP_URL'] . '/knowledge/create');
        }

        $id = $this->kbService->create([
            'title'      => $title,
            'content'    => $content,
            'tags'       => $this->request->post('tags', ''),
            'links'      => $this->request->post('links', ''),
            'tag_type'   => $this->request->post('tag_type', 'documentation'),
            'project_id' => $this->request->post('project_id', null) ?: null,
        ], $user['id']);

        // Handle file uploads
        $files = $_FILES['attachments'] ?? null;
        $uploadErrors = [];
        if ($files && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    $result = $this->kbService->uploadFile($file, $id, $user['id']);
                    if (is_string($result)) {
                        $uploadErrors[] = $result;
                    }
                }
            }
        }

        if (!empty($uploadErrors)) {
            Session::flash('error', implode(' ', $uploadErrors));
        } else {
            Session::flash('success', 'Article created successfully.');
        }
        Response::redirect($_ENV['APP_URL'] . '/knowledge/' . $id);
    }

    public function show(string $id): void
    {
        $article = $this->kbService->getById((int)$id);
        if (!$article) {
            Response::abort(404, 'Article not found.');
        }

        $files = $this->kbService->getFilesByArticleId((int)$id);

        Response::view('knowledge/show', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $this->getCurrentUser(),
            'article' => $article,
            'files' => $files,
        ]);
    }

    public function edit(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $article = $this->kbService->getById((int)$id);
        if (!$article) {
            Response::abort(404, 'Article not found.');
        }

        $files = $this->kbService->getFilesByArticleId((int)$id);
        $userProjects = ($user['role'] === 'admin')
            ? $this->projectService->getAll()
            : $this->projectService->getProjectsForUser($user['id']);

        Response::view('knowledge/edit', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $user,
            'article'  => $article,
            'files'    => $files,
            'projects' => $userProjects,
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $article = $this->kbService->getById((int)$id);
        if (!$article) {
            Response::abort(404, 'Article not found.');
        }

        $title   = trim($this->request->post('title', ''));
        $content = trim($this->request->post('content', ''));

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Title and content are required.');
            Response::redirect($_ENV['APP_URL'] . '/knowledge/' . $id . '/edit');
        }

        $this->kbService->update((int)$id, [
            'title'      => $title,
            'content'    => $content,
            'tags'       => $this->request->post('tags', ''),
            'links'      => $this->request->post('links', ''),
            'tag_type'   => $this->request->post('tag_type', 'documentation'),
            'project_id' => $this->request->post('project_id', null) ?: null,
        ]);

        // Handle file uploads
        $files = $_FILES['attachments'] ?? null;
        $uploadErrors = [];
        if ($files && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    $result = $this->kbService->uploadFile($file, (int)$id, $user['id']);
                    if (is_string($result)) {
                        $uploadErrors[] = $result;
                    }
                }
            }
        }

        if (!empty($uploadErrors)) {
            Session::flash('error', implode(' ', $uploadErrors));
        } else {
            Session::flash('success', 'Article updated successfully.');
        }
        Response::redirect($_ENV['APP_URL'] . '/knowledge/' . $id);
    }

    public function deleteFile(string $id, string $fileId): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $file = $this->kbService->getFileById((int)$fileId);
        if (!$file || (int)$file['article_id'] !== (int)$id) {
            Response::abort(404, 'File not found.');
        }

        $deleted = $this->kbService->deleteFile((int)$id, (int)$fileId);
        if ($deleted) {
            Session::flash('success', 'File deleted.');
        } else {
            Session::flash('error', 'Failed to delete file.');
        }
        Response::redirect($_ENV['APP_URL'] . '/knowledge/' . $id . '/edit');
    }

    public function delete(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $this->kbService->deleteWithFiles((int)$id);
        Session::flash('success', 'Article deleted.');
        Response::redirect($_ENV['APP_URL'] . '/knowledge');
    }

    public function serveFile(string $id, string $fileId): void
    {
        $file = $this->kbService->getFileById((int)$fileId);
        if (!$file || (int)$file['article_id'] !== (int)$id) {
            Response::abort(404, 'File not found.');
        }

        $filePath = dirname(__DIR__, 2) . '/storage/knowledge/' . $id . '/' . $file['filename'];
        if (!file_exists($filePath)) {
            Response::abort(404, 'File not found on disk.');
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._\-]/', '_', basename($file['original_name']));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');
        readfile($filePath);
        exit;
    }

    public function search(): void
    {
        $user  = $this->getCurrentUser();
        $query = $this->request->get('q', '');
        $articles = $query ? $this->kbService->search($query) : [];

        Response::view('knowledge/index', [
            'appUrl'      => $_ENV['APP_URL'],
            'user'        => $user,
            'articles'    => $articles,
            'filters'     => [],
            'projects'    => [],
            'searchQuery' => $query,
            'success'     => null,
            'error'       => null,
        ]);
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
