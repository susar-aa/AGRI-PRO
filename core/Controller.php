<?php
namespace Core;

abstract class Controller {

    protected function render(string $view, array $data = []): void {
        extract($data);
        
        // Expose helper and auth in view scope
        $companyConfig = require __DIR__ . '/../config/company.php';
        
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View file not found: {$view}");
        }

        // Standard layout includes header, navbar, sidebar, content, footer
        require __DIR__ . '/../app/Views/layouts/header.php';
        require __DIR__ . '/../app/Views/layouts/navbar.php';
        require __DIR__ . '/../app/Views/layouts/sidebar.php';
        echo '<div class="main-content-wrapper"><main class="main-content container-fluid px-4 py-4">';
        require $viewFile;
        echo '</main>';
        require __DIR__ . '/../app/Views/layouts/footer.php';
        echo '</div>';
    }

    protected function renderAuthView(string $view, array $data = []): void {
        extract($data);
        $companyConfig = require __DIR__ . '/../config/company.php';
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("Auth View file not found: {$view}");
        }
        require $viewFile;
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function validateCsrf(): void {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!CSRF::validate($token)) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            exit;
        }
    }
}
