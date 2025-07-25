<?php

namespace Core;

abstract class Controller {
    protected array $data = [];
    protected string $view = '';
    protected array $headers = [];

    protected function render(string $view, array $data = []): void {
        $this->view = $view;
        $this->data = array_merge($this->data, $data);

        $viewPath = dirname(__DIR__, 2) . '/public_html/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }

        foreach ($this->headers as $header) {
            header($header);
        }

        extract($this->data);
        require dirname(__DIR__, 2) . '/public_html/views/layouts/header.php';
        require $viewPath;
        require dirname(__DIR__, 2) . '/public_html/views/layouts/footer.php';
    }

    protected function json(array $data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    protected function getPost(string $key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    protected function getQuery(string $key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function setHeader(string $header): void {
        $this->headers[] = $header;
    }
}