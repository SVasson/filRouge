<?php

namespace App\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\Action\AuthAction;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;

class AdminModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->router = $container->get(Router::class);
        $authAction = $container->get(AuthAction::class);
        $adminAction = $container->get(AdminAction::class);

        $this->renderer->addPath('admin', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        $this->router->get('/admin/login', [$authAction, 'login'], 'admin.login');
        $this->router->post('/admin/login', [$authAction, 'login']);
        $this->router->get('/admin/logout', [$authAction, 'logout'], 'admin.logout');
        $this->router->get('/admin/home', [$adminAction, 'home'], 'admin.home');
        $this->router->get('/admin/add-event', [$adminAction, 'addEvent'], 'admin.add-event');
        $this->router->get('/admin/edit-event/{id:[\d]+}', [$adminAction, 'editEvent'], 'admin.edit-event');
        $this->router->post('/admin/add-event', [$adminAction, 'addEvent']);
        $this->router->post('/admin/edit-event/{id:[\d]+}', [$adminAction, 'editEvent']);
        $this->router->get('/admin/listEvent', [$adminAction, 'listEvent'], 'admin.listEvent');
        $this->router->get('/admin/delete/{id:[\d]+}', [$adminAction, 'delete'], 'admin.delete');

    }
}
