<?php

namespace App\Admin\Action;

use Model\Entity\Event;
use Core\Framework\Router\RedirectTrait;
use Core\Toaster\Toaster;
use Model\Entity\Participant;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class AdminAction
{
    use RedirectTrait;
    private ContainerInterface $container;
    private RendererInterface $renderer;
    private EntityManager $entityManager;
    private Toaster $toaster;
    private Router $router;
    private SessionInterface $session;
    private EntityRepository $repository;

    public function __construct(RendererInterface $renderer,
        EntityManager $entityManager,
        Toaster $toaster,
        ContainerInterface $container, SessionInterface $session)
    {
        
        $this->renderer = $renderer;
        $this->entityManager = $entityManager;
        $this->toaster = $toaster;
        $this->container = $container;
        $this->session = $session;
        $this->router = $container->get(Router::class);
        $this->renderer = $container->get(RendererInterface::class);
        $this->entityManager = $container->get(EntityManager::class);
        $this->toaster = $container->get(Toaster::class);

        $this->repository = $this->entityManager->getRepository(Event::class);
    }

    public function home(ServerRequest $request)
    {
        return $this->renderer->render('@admin/home');
    }

    public function addEvent(ServerRequest $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $event = new Event();
            $event->setName($params['name']);
            $event->setDescription($params['description']);
            $event->setStartDate(new \DateTimeImmutable($params['startDate']));
            $event->setEndDate(new \DateTimeImmutable($params['endDate']));

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $this->toaster->makeToast('L\'événement a été ajouté avec succès.', Toaster::SUCCESS);

            return $this->renderer->render('@admin/home');
        }

        return $this->renderer->render('@admin/add-event');
    }


    public function editEvent(ServerRequestInterface $request)
{
    $id = $request->getAttribute('id');
    $event = $this->repository->find($id);

    if (!$event) {
        $this->toaster->makeToast('Cet événement n\'existe pas.', Toaster::ERROR);
        return $this->renderer->render('@admin/home');
    }

    if ($request->getMethod() === 'POST') {
        $params = $request->getParsedBody();

        $event->setName($params['name']);
        $event->setDescription($params['description']);
        $event->setStartDate(new \DateTimeImmutable($params['start_date']));
        $event->setEndDate(new \DateTimeImmutable($params['end_date']));

        $this->entityManager->flush();

        $this->toaster->makeToast('L\'événement a été modifié avec succès.', Toaster::SUCCESS);

        return $this->renderer->render('@admin/home');
    }

    return $this->renderer->render('@admin/edit-event', [
        'event' => $event,
        'startDate' => $event->getStartDate()->format('Y-m-d'),
        'endDate' => $event->getEndDate()->format('Y-m-d'),
    ]);
}

/////////////////////
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


public function listEvent(ServerRequestInterface $request): ResponseInterface
{
    // Récupération des événements depuis la base de données
    $events = $this->entityManager->getRepository(Event::class)->findAll();

    // Rendu de la vue avec les événements
    return $this->render('@admin/listEvent', [
        'events' => $events,
        'admin' => $this->session->get('admin')
    ]);
}
////////////////////

public function delete(ServerRequestInterface $request) {

    //On récupère l'id passez en paramètre de requête
    $id = $request->getAttribute('id');

    //On récupère l'évenemtn qui correspond à l'id
    $event = $this->repository->find($id);

    //On prépare l'objet à etre supprimer de la base de données
    $this->entityManager->remove($event);
    //On execute la suppression
    $this->entityManager->flush();

    //On créer un Toast success pour l'utilisateur
    $this->toaster->makeToast('Evenement supprimé', Toaster::SUCCESS);

    //On redirige sur la liste des véhicule
    return $this->redirect('admin.listEvent');
}

}
