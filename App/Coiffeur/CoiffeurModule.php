<?php

namespace App\Coiffeur;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class CoiffeurModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
    
        $this->renderer->addPath('coiffeur', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        $this->router->get('/Coiffeur', [$this, 'coiffeur'], 'Coiffeur.vue');
    }

    public function coiffeur()
    {
        return $this->renderer->render('@coiffeur/coiffeur', 
        ['siteName' => 'Epicerie Mozart']);
    }
}