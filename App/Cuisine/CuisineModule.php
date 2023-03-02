<?php

namespace App\Cuisine;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class CuisineModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
    
        $this->renderer->addPath('cuisine', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        $this->router->get('/Cuisine', [$this, 'cuisine'], 'Cuisine.vue');
    }

    public function cuisine()
    {
        return $this->renderer->render('@cuisine/cuisine', 
        ['siteName' => 'Epicerie Mozart']);
    }
}