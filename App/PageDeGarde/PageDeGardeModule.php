<?php

namespace App\PageDeGarde;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class PageDeGardeModule extends AbstractModule
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
        $this->router->get('/', [$this, 'index'], 'PageDeGarde');

    }
  
    public function index()
    {
        return $this->renderer->render(
            '@home/index',
            ['siteName' => 'Epicerie Mozart',
            'videoUrl' => '/assets/img/stand-de-legumes-151.mp4',
            'videoType' => 'video/mp4',
            'secondVideoUrl' => '/assets/img/Homme - 86866 (2).mp4',
            'secondVideoType' => 'video/mp4'
            ]
        );
    }
}
