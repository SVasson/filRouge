<?php


use Core\App;
use App\User\UserModule;


use DI\ContainerBuilder;
use App\Admin\AdminModule;
use App\Epicerie\EpicerieModule;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use App\PageDeGarde\PageDeGardeModule;
use Model\Repository\EventDateRepository;
use Core\Framework\Middleware\CSRFMiddleware;
use Core\Framework\Middleware\RouterMiddleware;
use Core\Framework\Middleware\NotFoundMiddleware;
use Core\Framework\Middleware\UserAuthMiddleware;
use Core\Framework\Middleware\AdminAuthMiddleware;
use Core\Framework\Middleware\TrailingSlashMiddleware;
use Core\Framework\Middleware\RouterDispatcherMiddleware;

//Inclusion de l'autoloader de composer
require dirname(__DIR__) . '/vendor/autoload.php';


//Déclaration du tableau de modules à charger
$modules = [
    
    PageDeGardeModule::class,
    EpicerieModule::class,
    UserModule::class,
    AdminModule::class


];

//Instanciation du builder du container de dépendance, le builder permet de construire l'objet container de dépendances
//mais ce n'est pas le container de dépendances
$builder = new ContainerBuilder();
//Ajout de la feuille de configuration principale
$builder->addDefinitions(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($modules as $module) {
    if (!is_null($module::DEFINITIONS)) {
        //Si les modules possédent une feuille de configuration personnalisé on l'ajoute aussi
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

//On récupère l'instance du container de dépendance
$container = $builder->build();

//On instancie notre application en lui la liste des modules et le container de dépendances
$app = new App($container, $modules);

//On link le premier middleware de la chaine de responsabilité à l'application
//Puis on ajoute les middleware suivant en leur passant le container de dépendances si besoin
$app->linkFirst(new TrailingSlashMiddleware())
    ->linkWith(new RouterMiddleware($container))
    ->linkWith(new CSRFMiddleware($container, [
        '/admin/add-event'
    ]))
    ->linkWith(new AdminAuthMiddleware($container))
    ->linkWith(new UserAuthMiddleware($container))
    ->linkWith(new RouterDispatcherMiddleware())
    ->linkWith(new NotFoundMiddleware());
   

//Si l'index n'est pas executé à partir de la CLI (Command Line Interface)
if (php_sapi_name() !== 'cli') {
    //On récupère la réponse de notre application en lancant la methode 'run' et en lui passant un objet ServerRequest
    //rempli avec toutes les information de la requête envoyé par la machine client
    $response = $app->run(ServerRequest::fromGlobals());
    //On renvoi la réponse au server après avoir transformer le retour de l'application en une réponse compréhensible par
    //la machine client
    send($response);
}
// Une modif