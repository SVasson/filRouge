<?php

namespace App\Epicerie\Action;

use Core\Framework\Renderer\RendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EpicerieAction
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
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
     * Affiche la page Événement
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function evenement(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/evenement');
    }
    /**
     * Affiche la page cuisine
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function cuisine(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/cuisine');
    }

    /**
     * Affiche la page coiffeur
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function coiffeur(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('@epicerie/coiffeur');
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
}
