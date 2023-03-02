<?php

namespace App\Contact;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class ContactModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;

        $this->renderer->addPath('contact', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        $this->router->get('/Contact', [$this, 'contact'], 'Contact.vue');
    }
    public function contact()
    {
        return $this->renderer->render('@contact/contact', 
        ['siteName' => 'Epicerie Mozart']);
    }
}