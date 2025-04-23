<?php
namespace app\core;

use app\controllers\MainController;
use app\controllers\ContactController;
use app\controllers\ReviewController;

class Router
{
    private string $method;
    private string $path;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = strtok($_SERVER['REQUEST_URI'], '?') ?: '/';
        
        error_log("Router: Method={$this->method}, Path={$this->path}");
        
        $this->dispatch();
    }

    private function dispatch(): void
    {
        // Public pages
        if ($this->method === 'GET' && $this->path === '/') {
            error_log("Router: Dispatching to MainController->homepage()");
            (new MainController())->homepage();
            return;
        }

        if ($this->method === 'GET' && $this->path === '/about') {
            error_log("Router: Dispatching to MainController->about()");
            (new MainController())->about();
            return;
        }

        if ($this->method === 'GET' && $this->path === '/contact') {
            error_log("Router: Dispatching to ContactController->contactView()");
            (new ContactController())->contactView();
            return;
        }

        if ($this->method === 'GET' && $this->path === '/japan') {
            error_log("Router: Dispatching to MainController->japan()");
            (new MainController())->japan();
            return;
        }

        if ($this->method === 'GET' && $this->path === '/turkey') {
            error_log("Router: Dispatching to MainController->turkey()");
            (new MainController())->turkey();
            return;
        }

        if ($this->method === 'GET' && $this->path === '/montenegro') {
            error_log("Router: Dispatching to MainController->montenegro()");
            (new MainController())->montenegro();
            return;
        }

        // Contact API
        if ($this->method === 'POST' && $this->path === '/api/contact') {
            error_log("Router: Dispatching to ContactController->postContact()");
            (new ContactController())->postContact();
            return;
        }

        // Newsletter API
        if ($this->method === 'POST' && $this->path === '/api/newsletter') {
            error_log("Router: Dispatching to ContactController->postNewsletter()");
            (new ContactController())->postNewsletter();
            return;
        }

        // Reviews API
        if ($this->method === 'GET' && $this->path === '/api/reviews') {
            error_log("Router: Dispatching to ReviewController->getReviews()");
            (new ReviewController())->getReviews();
            return;
        }

        if ($this->method === 'POST' && $this->path === '/api/reviews') {
            error_log("Router: Dispatching to ReviewController->postReview()");
            (new ReviewController())->postReview();
            return;
        }
        
        // Test route for debugging
        if ($this->method === 'GET' && $this->path === '/test-newsletter') {
            error_log("Router: Testing Newsletter functionality");
            try {
                $newsletter = new \app\models\Newsletter([
                    'id' => 0,
                    'email' => 'test@example.com',
                    'name' => 'Test User',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                echo "Newsletter class loaded successfully.";
            } catch (\Throwable $e) {
                error_log("Newsletter test error: " . $e->getMessage());
                echo "Error: " . $e->getMessage();
            }
            return;
        }

        // 404
        error_log("Router: No route found for {$this->method} {$this->path}");
        http_response_code(404);
        echo 'Not Found';
    }
}