<?php

namespace App\Evenement;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class EvenementModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
    
        $this->renderer->addPath('evenement', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        $this->router->get('/Evenement', [$this, 'evenement'], 'Evenement.vue');
    }

    public function evenement()
    {
        return $this->renderer->render('@evenement/evenement', 
        ['siteName' => 'Epicerie Mozart']);
    }
}