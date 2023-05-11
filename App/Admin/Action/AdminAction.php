<?php

namespace App\Admin\Action;

use Model\Entity\User;
use Model\Entity\Event;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Model\Entity\Participation;
use Core\Framework\Router\Router;
use GuzzleHttp\Psr7\UploadedFile;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Core\Framework\Router\RedirectTrait;
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

    public function __construct(
        RendererInterface $renderer,
        EntityManager $entityManager,
        Toaster $toaster,
        ContainerInterface $container,
        SessionInterface $session
    ) {

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
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    //////////////////////////////////////////////////////////////////////

    public function home(ServerRequest $request)
    {
        // Recupere tout les utilisateur de la base de donnée
        $users = $this->entityManager->getRepository(User::class)->findAll();


        return $this->renderer->render('@admin/home', ['users' => $users]);
        //les passent en vue 
    }
    /////////////////////////////////////////////////////////////////////////////////////////////

    public function addEvent(ServerRequestInterface $request)
    {
        //Récupère la méthode utilisée pour la requête (POST ou GET)
        $method = $request->getMethod();
        //Si le formulaire à été soumis
        if ($method === 'POST') {
            //On récupère le contenu de $_POST (les valeur saisie dans le formulaire)
            $data = $request->getParsedBody();
            //On récupère le contenu de $_FILES à l'index "img" (Les fichiers chargés dans le formulaire, avec un input de type 'file')
            $file = $request->getUploadedFiles()["img"];

            $validator = new Validator($data);

            $errors = $validator
                ->required('name', 'description', 'startDate', 'endDate')
                ->getErrors();
            //Si il y a des erreurs, on crée un Toast par erreur et on redirige l'utilisateur afin d'afficher les messages
            if ($errors) {
                //Boucle sur le tableau d'erreurs
                foreach ($errors as $error) {
                    //Création du Toast
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                //Redirection
                return $this->redirect('admin.add-event');
            }

            //On vérifie que l'img soit conforme (voir commentaire de la méthode)
            $error = $this->fileGuards($file);
            //si on a des erreurs on retourne le Toast (Le Toast a été généré par 'fileGuard')
            if ($error !== true) {
                return $error;
            }

            //Si tout va bien avec le fichier, on récupère le nom
            $fileName = $file->getClientFileName();
            //On assemble le nom du fichier avec le chemin du dossier où il sera enregistré
            $imgPath = $this->container->get('img.basePath') . $fileName;
            //On tente de le déplacer au chemin voulu
            $file->moveTo($imgPath);
            //Si le déplacement n'est pas possible on crée un Toast et on redirige
            if (!$file->isMoved()) {
                $this->toaster->makeToast("Une erreur s'est produite durant l'enregistrement de votre img, merci de réessayer.", Toaster::ERROR);
                return $this->redirect('admin.add-event');
            }
            $event = new Event();

            $event->setName($data['name']);
            $event->setDescription($data['description']);
            $event->setStartDate(new \DateTimeImmutable($data['startDate']));
            $event->setEndDate(new \DateTimeImmutable($data['endDate']));
            $event->setImgPath($fileName);

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $this->toaster->makeToast('L\'événement a été ajouté avec succès.', Toaster::SUCCESS);

            return $this->redirect('admin.listEvent');
        }

        return $this->renderer->render('@admin/add-event');
    }

    // ////////////////////////////////////////////////////////////////////////////

    public function editEvent(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');

        //On récupère l'évenement qui correspond à l'id
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            $this->toaster->makeToast('Cet événement n\'existe pas.', Toaster::ERROR);
            return $this->renderer->render('@admin/home');
        }

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            //On récupère les fichier chargé si il y en a, sinon un tableau vide
            $files = $request->getUploadedFiles();

            if (sizeof($files) > 0 && $files['img']->getError() !== 4) {
                //On récupère le nom de l'ancienne img de l'evenement
                $oldImg = $event->getImgPath();
                //On récupère toutes les informations de la nouvelle img
                $newImg = $files['img'];
                //On récupère le nom de la nouvelle img
                $imgName = $newImg->getClientFileName();
                //On joint le nom de l'img au chemin du dossier ou l'in souhaite l'enregistrer
                $imgPath = $this->container->get('img.basePath') . $imgName;
                //On vérifie la nouvelle img
                $error = $this->fileGuards($newImg);
                //Si il y a une erreur avec le fichier on retourne l'erreur
                if ($error) {
                    return $error;
                }
                //On tente de la déplacer
                $newImg->moveTo($imgPath);
                //Si l'img à bien été déplacé
                if ($newImg->isMoved()) {
                    //On lie la nouvelle img avec l'evenement
                    $event->setImgPath($imgName);
                    //On supprime l'ancienne du server avec la fonction unlink
                    $oldPath = $this->container->get('img.basePath') . $oldImg;
                    unlink($oldPath);
                }
            }

            $event->setName($params['name']);
            $event->setDescription($params['description']);
            $event->setStartDate(new \DateTimeImmutable($params['start_date']));
            $event->setEndDate(new \DateTimeImmutable($params['end_date']));

            $this->entityManager->flush();

            $this->toaster->makeToast('L\'événement a été modifié avec succès.', Toaster::SUCCESS);
        }

        return $this->renderer->render('@admin/edit-event', [
            'event' => $event,
            'startDate' => $event->getStartDate()->format('Y-m-d'),
            'endDate' => $event->getEndDate()->format('Y-m-d'),
        ]);
    }
    //////////////////



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

    //////////////////////////////////////////////////////////////////////

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
    //////////////////////////////////////////////////////////////////////
    public function listParticipant(ServerRequestInterface $request): ResponseInterface
    {
        // Récupération des participations depuis la base de données
        $participations = $this->entityManager->getRepository(Participation::class)->findAll();

        // Récupération des événements depuis la base de données
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        // Recupere tout les utilisateur de la base de donnée
        $users = $this->entityManager->getRepository(User::class)->findAll();

        // Rendu de la vue avec les participation
        return $this->render('@admin/listParticipant', [
            'participations' => $participations,
            'events' => $events,
            'users' => $users,
            'admin' => $this->session->get('admin')
        ]);
    }
    //////////////////////////////////////////////////////////////////////

    public function delete(ServerRequestInterface $request)
    {

        //On récupère l'id passez en paramètre de requête
        $id = $request->getAttribute('id');

        //On récupère l'évenement qui correspond à l'id
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        $participations = $event->getParticipation();

        $arrayLenght = sizeof($participations);
        if ($arrayLenght > 0) {
            foreach ($participations as $participation) {
                $participation = $this->entityManager->getRepository(Participation::class)->find($participation->getId());
                $this->entityManager->remove($participation);
            }
        }

        //On prépare l'objet à etre supprimer de la base de données
        $this->entityManager->remove($event);
        //On execute la suppression
        $this->entityManager->flush();
        //On récupère le nom de l'ancienne img de l'event
        $oldImg = $event->getImgPath();
        //On supprime l'ancienne du server avec la fonction unlink
        $oldPath = $this->container->get('img.basePath') . $oldImg;
        unlink($oldPath);
        //On créer un Toast success pour l'utilisateur
        $this->toaster->makeToast('Evenement supprimé', Toaster::SUCCESS);

        //On redirige sur la liste des event
        return $this->redirect('admin.listEvent');
    }


    //////////////////////////////////////////////////////////////////////

    public function deleteUser(ServerRequestInterface $request)
    {
        // On récupère l'id passé en paramètre de requête
        $id = $request->getAttribute('id');

        // On récupère l'utilisateur qui correspond à l'id
        $user = $this->entityManager->getRepository(User::class)->find($id);

        // On récupère toutes les participations de cet utilisateur dans la table de jointure
        $participations = $user->getParticipation();

        $arrayLenght = sizeof($participations);
        // On supprime toutes les participations de cet utilisateur dans la table de jointure
        if ($arrayLenght > 0) {
            foreach ($participations as $participation) {
                $participation = $this->entityManager->getRepository(Participation::class)->find($participation->getId());
                $this->entityManager->remove($participation);
            }
        }

        // On prépare l'objet à être supprimé de la base de données
        $this->entityManager->remove($user);

        // On exécute la suppression
        $this->entityManager->flush();

        // On créer un Toast success pour l'utilisateur
        $this->toaster->makeToast('Utilisateur supprimé', Toaster::SUCCESS);

        // On redirige sur la liste des utilisateurs
        return $this->redirect('admin.home');
    }


    ////////////////////////////////////////////////////////////////
    public function deleteParticipant(ServerRequestInterface $request): ResponseInterface
    {
        // On récupère l'id de la participation à supprimer passé en paramètre de requête
        $id = $request->getAttribute('id');
    
        // On récupère la participation qui correspond à l'id
        $participation = $this->entityManager->getRepository(Participation::class)->find($id);
    
        if (!$participation) {
            // On créer un Toast error pour l'utilisateur
            $this->toaster->makeToast('Participation introuvable', Toaster::ERROR);
        } else {
            // On prépare l'objet à être supprimé de la base de données
            $this->entityManager->remove($participation);
    
            // On exécute la suppression
            $this->entityManager->flush();
    
            // On créer un Toast success pour l'utilisateur
            $this->toaster->makeToast('Participation supprimée', Toaster::SUCCESS);
        }
    
        // On redirige sur la liste des participations
        return $this->redirect('admin.listParticipant');
    }
    
 ////////////////////////////////////////////////////////////////
    private function fileGuards(UploadedFile $file)
    {
        //Handle Server error
        //S'assure qu'il n'y a pas eu d'erreur au chargement de l'img
        if ($file->getError() === 4) {
            $this->toaster->makeToast("Une erreur est survenu lors du chargement du fichier.", Toaster::SUCCESS);
            return $this->redirect('admin.add-event');
        }

        //list permet de décomposé le contenu d'un tableau afin d'en extraire les valeur et de les stockées dans des variables
        //On récupère le type et le format du fichier
        list($type, $format) = explode('/', $file->getClientMediaType()); //getClientMediaType renvoi le type MIME d'un fichier
        // exemple de type MIME : img/jpg

        //Handle format error
        //On vérifie que le format et le type de fichier correspondent aux formats et type autorisé, sinon on renvoie une erreur

        if (!in_array($type, ['img', 'image']) or !in_array($format, ['jpg', 'jpeg', 'png'])) {
            $this->toaster->makeToast(
                "ERREUR : Le format du fichier n'est pas valide, merci de charger un .png, .jpeg ou .jpg",
                Toaster::ERROR
            );
            return $this->redirect('admin.add-event');
        }


        //Handle excessive size
        //Vérifie que la taille du fichier en octets ne dépasse pas les 2Mo
        if ($file->getSize() > 2047674) {
            $this->toaster->makeToast("Merci de choisir un fichier n'excédant pas 2Mo", Toaster::ERROR);
            return $this->redirect('admin.add-event');
        }

        return true;
    }
    ///////////////////////////////////////////////
    public function editUser(ServerRequestInterface $request)
    {
        // Récupération de l'id de l'utilisateur à modifier depuis les attributs de la requête
        $id = $request->getAttribute('id');

        // Récupération de l'utilisateur à modifier depuis la base de données
        $user = $this->entityManager->getRepository(User::class)->find($id);

        // Vérification que l'utilisateur existe
        if (!$user) {
            $this->toaster->makeToast('Cet utilisateur n\'existe pas.', Toaster::ERROR);
            return $this->redirect('admin.home');
        }

        // Récupération de la méthode HTTP utilisée pour la requête
        $method = $request->getMethod();

        // Si le formulaire a été soumis
        if ($method === 'POST') {
            // Récupération des données du formulaire
            $data = $request->getParsedBody();

            // Validation des données du formulaire
            $validator = new Validator($data);
            $errors = $validator
                ->required('nom', 'prenom', 'numeroDeTel')
                ->getErrors();

            // Si des erreurs ont été trouvées, on affiche un message d'erreur
            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return $this->renderer->render('@admin/edit-user', ['user' => $user]);
            }

            // Modification de l'utilisateur avec les nouvelles données
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setNumeroDeTel($data['numeroDeTel']);

            // Enregistrement des modifications dans la base de données
            $this->entityManager->flush();

            // Affichage d'un message de succès
            $this->toaster->makeToast('L\'utilisateur a été modifié avec succès.', Toaster::SUCCESS);

            // Redirection vers la page d'accueil de l'administration
            return $this->redirect('admin.home');
        }

        // Affichage du formulaire de modification de l'utilisateur
        return $this->renderer->render('@admin/edit-user', ['user' => $user]);
    }
}
