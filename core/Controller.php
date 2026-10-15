<?php
namespace Core;

abstract class Controller
{
    /**
     * @param string $view Which page to view
     * @param array $data Data that should be available in the page. Returns view page
     */
    public function view(string $view, ?array $data = []): void
    {
        $pageTitle = $view;
        $data['pagetitle'] = $pageTitle;

        $viewLocation = VIEWS_DIR . "$view.php";
        $data['viewlocation'] = $viewLocation;

        $baseLocation = VIEWS_DIR . 'base.php';
        // die($viewLocation);
        if (file_exists($viewLocation)) {
            extract($data);
            require_once $baseLocation;
        }
    }

    /**
     * @param string $modelName Returns model Object
     */
    public function model(string $modelName)
    {
        $modelLocation = MODELS_DIR . "$modelName.php";
        if (file_exists($modelLocation)) {
            require_once $modelLocation;
            return new $modelName();
        }
    }

    /**
     * @param string $middleware Returns middelware Object
     */
    public function middleware(string $middleware)
    {
        $middlewareLocation = MIDDLEWARE_DIR . "$middleware.php";
        if (file_exists($middlewareLocation)) {
            require_once $middlewareLocation;
            return new $middleware();
        }
    }
}
