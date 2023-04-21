<?php

namespace App\Epicerie;

use App\Epicerie\Action\EpicerieAction;
use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;
use Psr\Container\ContainerInterface;

class EpicerieModule extends AbstractModule
{

    private Router $router;
    private RendererInterface $renderer;

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    /**
     * Déclare les routes et les methodes disponible pour ce module, definie le chemin vers le dossier de vues du module,
     * définie éventuellement des variables global a toutes les vues
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        //Router pour déclarer les routes
        $this->router = $container->get(Router::class);
        //Renderer pour déclarer les vues
        $this->renderer = $container->get(RendererInterface::class);

        //Ensembles d'action possible
        $EpicerieAction = $container->get(EpicerieAction::class);


        //Declaration du chemin des vue sous le namespace 'epicerie'
        $this->renderer->addPath('epicerie', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        //Déclaration des routes disponibles en method GET
        $this->router->get('/aPropos', [$EpicerieAction, 'aPropos'], 'epicerie.apropos');
        
        //Ajout des routes pour d'autres pages
        $this->router->get('/contact', [$EpicerieAction, 'contact'], 'epicerie.contact');
        $this->router->get('/inscription', [$EpicerieAction, 'inscription'], 'epicerie.inscription');
        $this->router->get('/connexion', [$EpicerieAction, 'connexion'], 'epicerie.connexion');
        $this->router->get('/mentionsLegales', [$EpicerieAction, 'mentionsLegales'], 'epicerie.mentionsLegales');

    }
}
