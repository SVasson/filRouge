<?php

use Model\Entity\Admin;
use Doctrine\ORM\EntityManager;

include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

if ($app) {
    $container = $app->getContainer();
    $manager = $container->get(EntityManager::class);
    $admin = new Admin();
    $admin->setNom('Admin')
        ->setMail('test@test.fr')
        ->setPhone('1234567890')
        ->setMdp(password_hash('admin', PASSWORD_BCRYPT));
    $manager->persist($admin);
    $manager->flush();
    echo "Admin enregistrée";
}