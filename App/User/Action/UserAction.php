<?php

namespace App\User\Action;


use Model\Entity\User;
use Model\Entity\Event;
use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Model\Entity\Participation;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Core\Framework\Router\RedirectTrait;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class UserAction
{
    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;
    private Toaster $toaster;
    private EntityManager $entityManager;
    private EntityRepository $repository;
    private SessionInterface $session;


    public function __construct(ContainerInterface $container,  EntityManager $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->renderer = $container->get(RendererInterface::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->repository = $container->get(EntityManager::class)->getRepository(User::class);
        // $this->repository = $container->get(EntityManager::class)->getRepository(Event::class);
        // $this->repository = $container->get(EntityManager::class)->getRepository(Participation::class);
        $this->session = $container->get(SessionInterface::class);
        $user = $this->session->get('auth');
        if ($user) {
            $this->renderer->addGlobal('user', $user);
        }
    }

    public function logView(ServerRequest $request)
    {
        return $this->renderer->render('@user/forms');
    }

    public function signin(ServerRequest $request)
    {
        $auth = $this->container->get(UserAuth::class);
        $data = $request->getParsedBody();
        $validator = new Validator($data);
        $errors = $validator
            ->required('nom', 'prenom', 'mail', 'numeroDeTel', 'mdp', 'mdp_confirm')
            ->email('mail')
            ->strSize('mdp', 12, 50)
            ->confirm('mdp')
            ->isUnique('mail', $this->repository, 'mail')
            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }
        $result = $auth->signIn($data);

        if ($result !== true) {
            return $result;
        }
        $this->toaster->makeToast("Inscription reussie, vous pouvez vous connecter", Toaster::SUCCESS);
        return $this->redirect('user.home');
    }

    public function login(ServerRequest $request)
    {
        $data = $request->getParsedBody();

        $validator = new Validator($data);
        $errors = $validator
            ->required('mail', 'mdp')
            ->email('mail')
            ->getErrors();
        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }

        $auth = $this->container->get(UserAuth::class);
        $res = $auth->login($data['mail'], $data['mdp']);
        if ($res) {
            $this->toaster->makeToast('', Toaster::SUCCESS);
            return $this->redirect('user.home');
        }
        $this->toaster->makeToast("Connexion échoué, merci de vérifier email et mot de passe", Toaster::ERROR);
        return $this->redirect('user.login');
    }
    public function logout()
    {
        $auth = $this->container->get(UserAuth::class);
        $auth->logout();
        $this->toaster->makeToast('Deconnexion reussie.', Toaster::SUCCESS);
        return (new Response())
            ->withHeader('Location', '/login');
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





    public function listEventUser(ServerRequestInterface $request): ResponseInterface
    {

        // Récupération des événements depuis la base de données
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        // Rendu de la vue avec les événements
        return $this->render('@user/listEventUser', [
            'events' => $events,
            'user' => $this->session->get('user')
        ]);
    }
    public function home(ServerRequest $request)
    {
        // Récupération des événements depuis la base de données
        $events = $this->entityManager->getRepository(Event::class)->findAll();
        $user = $this->session->get('auth');
        return $this->renderer->render('@user/home', [
            'events' => $events,
            'user' => $user
        ]);
    }




    /**
     * @Route("/participer/{id}", name="user.participer", methods={"GET"})
     */
    public function participerAction(ServerRequest $request, array $params = []): ResponseInterface
    {
        // Récupérer l'id de l'événement à partir des paramètres de la route
        $eventId = (int) $request->getAttribute('id');

        // Récupérer l'utilisateur connecté
        $user = $this->session->get('auth');

        // Récupérer l'événement correspondant à l'id
        $event = $this->entityManager->getRepository(Event::class)->find($eventId);
        if ($event === null) {
            // L'événement n'a pas été trouvé dans la base de données
            // Rediriger l'utilisateur vers une page d'erreur ou afficher un message d'erreur

            $this->toaster->makeToast('L\'événement demandé n\'existe pas', Toaster::ERROR);

            return $this->redirect('user.listEventUser');
        }

        // Vérifier si l'utilisateur participe déjà à l'événement
        $participation = $this->entityManager->getRepository(Participation::class)->findOneBy(['user' => $user, 'event' => $event]);
        if ($participation !== null) {
            // L'utilisateur participe déjà à l'événement
            // Rediriger l'utilisateur vers une page d'erreur ou afficher un message d'erreur

            $this->toaster->makeToast('Vous participez déjà à cet événement', Toaster::ERROR);

            return $this->redirect('user.listEventUser');
        }

        // Créer une nouvelle Participation
        $participation = new Participation();

        //     // Récupérer l'utilisateur à partir de l'ID dans la session
        // $userId = $user->getId();
        // $userRepository = $this->entityManager->getRepository(User::class);
        // $user = $userRepository->find($userId);

        // if ($user === null) {
        //     // L'utilisateur n'a pas été trouvé dans la base de données
        //     // Rediriger l'utilisateur vers une page d'erreur ou afficher un message d'erreur
        //     $this->toaster->makeToast('Utilisateur introuvable', Toaster::ERROR);
        //     return $this->redirect('user.listEventUser');
        // }

        // // Définir l'utilisateur pour la participation
        // $participation->setUser($user);

        // Définir l'événement
        $participation->setUser($user);
        $participation->setEvent($event);

        // Enregistrer la participation dans la base de données
        $this->entityManager->persist($participation);
        $this->entityManager->flush();

        // Rediriger l'utilisateur vers la liste des événements
        return $this->redirect('user.listEventUser');
    }
}
