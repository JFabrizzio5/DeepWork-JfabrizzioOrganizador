<?php
namespace App\Core;

class Response
{
    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function view(string $template, array $data = []): void
    {
        extract($data);
        $templatePath = dirname(__DIR__, 2) . '/views/' . $template . '.php';
        if (!file_exists($templatePath)) {
            http_response_code(404);
            echo "View not found: {$template}";
            exit;
        }
        require $templatePath;
    }

    public static function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        echo $message ?: "Error {$code}";
        exit;
    }
}
