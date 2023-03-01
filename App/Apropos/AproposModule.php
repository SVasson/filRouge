<?php

namespace App\Apropos;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class AproposModule extends AbstractModule
{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
    
        $this->renderer->addPath('apropos', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        $this->router->get('/Apropos', [$this, 'aPropos'], 'Apropos.vue');



    }
    public function apropos()
    {
        return $this->renderer->render('@apropos/aPropos',
        ['siteName' => 'Epicerie Mozart']);
    }

}