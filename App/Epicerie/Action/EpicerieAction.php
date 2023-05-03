<?php

namespace App\Epicerie\Action;

use Exception;
use DateTimeImmutable;
use Model\Entity\Event;
use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Session\SessionInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class EpicerieAction
{
    private Toaster $toaster;
    private EntityManager $entityManager;
    private RendererInterface $renderer;
    private ContainerInterface $container;
    private SessionInterface $session;
    private $repository;

    public function __construct(RendererInterface $renderer, SessionInterface $session, Toaster $toaster, EntityManager $entityManager)
    {
        $this->renderer = $renderer;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->toaster = $toaster;
    }
   
    /**
     * Affiche la page A propos
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function aPropos(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/aPropos');
    }

    /**
     * Affiche la page Contact
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function contact(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/contact');
    }
     /**
     * Affiche la page Mentions Légales 
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function mentionsLegales(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/mentionsLegales');
    }


    /**
     * Affiche la page Inscription
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function inscription(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/inscription');
    }

    /**
     * Affiche la page Connexion
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function connexion(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/connexion');
    }

    /**
     * Rend une vue avec le RendererInterface
     * @param string $view
     * @param array $params
     * @return ResponseInterface
     */
    private function render(string $view, array $params = []): ResponseInterface
    {
        $content = $this->renderer->render($view, $params);

        $response = new \GuzzleHttp\Psr7\Response();
        $response->getBody()->write($content);

        return $response;
    }

// ///////////////////////////////////////////



/////////////////////////////////////



}