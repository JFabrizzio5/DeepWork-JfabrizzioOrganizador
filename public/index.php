<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AuthMiddleware;
use App\Controllers\AuthController;
use App\Controllers\TicketController;
use App\Controllers\AdminController;
use App\Controllers\KnowledgeBaseController;
use App\Controllers\WeeklyPlanController;
use App\Controllers\ApiController;
use App\Middleware\ApiKeyMiddleware;

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

Session::start();

$router = new Router();

// Home redirect
$router->get('/', function () {
    if (Session::has('user_id')) {
        Response::redirect($_ENV['APP_URL'] . '/tickets/list');
    } else {
        Response::redirect($_ENV['APP_URL'] . '/login');
    }
});

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);

// Ticket routes
$router->get('/tickets/list', [TicketController::class, 'index'], [AuthMiddleware::class]);
$router->get('/tickets/create', [TicketController::class, 'create'], [AuthMiddleware::class]);
$router->post('/tickets/store', [TicketController::class, 'store'], [AuthMiddleware::class]);
$router->get('/tickets/{id}', [TicketController::class, 'show'], [AuthMiddleware::class]);
$router->post('/tickets/{id}/status', [TicketController::class, 'updateStatus'], [AuthMiddleware::class]);
$router->post('/tickets/{id}/note', [TicketController::class, 'addNote'], [AuthMiddleware::class]);
$router->post('/tickets/{id}/assign', [TicketController::class, 'assign'], [AuthMiddleware::class]);
$router->get('/tickets/{id}/evidence/{evidenceId}', [TicketController::class, 'serveEvidence'], [AuthMiddleware::class]);

// Admin routes
$router->get('/admin/users', [AdminController::class, 'users'], [AuthMiddleware::class]);
$router->get('/admin/users/create', [AdminController::class, 'createUser'], [AuthMiddleware::class]);
$router->post('/admin/users/store', [AdminController::class, 'storeUser'], [AuthMiddleware::class]);
$router->post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'], [AuthMiddleware::class]);

// Knowledge Base routes (specific before parametric)
$router->get('/knowledge', [KnowledgeBaseController::class, 'index'], [AuthMiddleware::class]);
$router->get('/knowledge/search', [KnowledgeBaseController::class, 'search'], [AuthMiddleware::class]);
$router->get('/knowledge/create', [KnowledgeBaseController::class, 'create'], [AuthMiddleware::class]);
$router->post('/knowledge/store', [KnowledgeBaseController::class, 'store'], [AuthMiddleware::class]);
$router->get('/knowledge/{id}', [KnowledgeBaseController::class, 'show'], [AuthMiddleware::class]);
$router->post('/knowledge/{id}/delete', [KnowledgeBaseController::class, 'delete'], [AuthMiddleware::class]);

// Weekly Plan routes (specific before parametric)
$router->get('/weekly-plan', [WeeklyPlanController::class, 'index'], [AuthMiddleware::class]);
$router->get('/weekly-plan/create', [WeeklyPlanController::class, 'create'], [AuthMiddleware::class]);
$router->post('/weekly-plan/store', [WeeklyPlanController::class, 'store'], [AuthMiddleware::class]);
$router->post('/weekly-plan/task/toggle', [WeeklyPlanController::class, 'toggleTask'], [AuthMiddleware::class]);
$router->get('/weekly-plan/{id}', [WeeklyPlanController::class, 'show'], [AuthMiddleware::class]);
$router->post('/weekly-plan/{id}/task', [WeeklyPlanController::class, 'addTask'], [AuthMiddleware::class]);
$router->post('/weekly-plan/{id}/delete', [WeeklyPlanController::class, 'delete'], [AuthMiddleware::class]);

// ─────────────────────────────────────────────────────────
// REST API routes — authenticated with API key (Bearer token)
// All responses are JSON. No session required.
// ─────────────────────────────────────────────────────────
$api = [ApiKeyMiddleware::class];

// Ticket endpoints
$router->get('/api/tickets', [ApiController::class, 'listTickets'], $api);
$router->post('/api/tickets', [ApiController::class, 'createTicket'], $api);

// Specific sub-routes MUST be registered before the /api/tickets/{id} catch-all
$router->get('/api/tickets/{id}/evidence', [ApiController::class, 'listEvidence'], $api);
$router->post('/api/tickets/{id}/evidence', [ApiController::class, 'uploadEvidence'], $api);
$router->post('/api/tickets/{id}/status', [ApiController::class, 'updateStatus'], $api);
$router->post('/api/tickets/{id}/phase', [ApiController::class, 'updatePhase'], $api);
$router->post('/api/tickets/{id}/note', [ApiController::class, 'addNote'], $api);
$router->post('/api/tickets/{id}/assign', [ApiController::class, 'assign'], $api);
$router->get('/api/tickets/{id}', [ApiController::class, 'getTicket'], $api);

// User endpoint (admin only)
$router->get('/api/users', [ApiController::class, 'listUsers'], $api);

// Admin — API key management
$router->get('/admin/api-keys', [AdminController::class, 'apiKeys'], [AuthMiddleware::class]);
$router->post('/admin/api-keys/generate', [AdminController::class, 'generateKey'], [AuthMiddleware::class]);
$router->post('/admin/api-keys/{id}/revoke', [AdminController::class, 'revokeKey'], [AuthMiddleware::class]);
$router->post('/admin/api-keys/{id}/delete', [AdminController::class, 'deleteApiKey'], [AuthMiddleware::class]);

$request = new Request();
$router->dispatch($request->uri(), $request->method());
