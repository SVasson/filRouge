<?php

namespace App\Epicerie;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class EpicerieModule extends AbstractModule
{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
    
        $this->renderer->addGlobal('siteName', 'Epicerie Mozart');
        $this->renderer->addPath('home', __DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/', [$this, 'index'], 'acceuil');


    }
    public function index()
    {
        return $this->renderer->render('@home/index',
        ['siteName' => 'Epicerie Mozart']);
    }

}