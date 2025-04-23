<?php
namespace app\controllers;

abstract class Controller
{
    /**
     * Render an HTML view.
     *
     * @param string $view   path under public/assets/views (e.g. "main/contact")
     * @param array  $data   variables to extract into the view
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $file = __DIR__ . '/../../public/assets/views/' . $view . '.html';

        if (! is_file($file)) {
            http_response_code(500);
            echo "View not found at: {$file}";
            exit;
        }

        require $file;
    }
}
