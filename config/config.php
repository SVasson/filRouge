<?php

use Core\Toaster\Toaster;
use Core\Db\DatabaseFactory;
use Core\Session\PHPSession;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Core\Toaster\ToasterTwigExtension;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\RouterTwigExtension;
use Core\Framework\Security\CSRFTwigExtension;
use Core\Framework\Renderer\TwigRendererFactory;
use Core\Framework\TwigExtensions\AssetsTwigExtension;

return [
    "doctrine.user" => "root",
    "doctrine.dbname" => "filrouge",
    "doctrine.mdp" => "",
    "doctrine.driver" => "pdo_mysql",
    "doctrine.devMode" => true,
    "config.viewPath" => dirname(__DIR__).DIRECTORY_SEPARATOR.'view',
    "twig.extensions" => [
        RouterTwigExtension::class,
        ToasterTwigExtension::class,
        AssetsTwigExtension::class,
        CSRFTwigExtension::class
    ],
    Router::class => \DI\create(),
    SessionInterface::class => \DI\get(PHPSession::class),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    EntityManager::class => \DI\factory(DatabaseFactory::class),
    Toaster::class => \DI\autowire()
];