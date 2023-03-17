<?php

namespace App\Admin\Action;

use Core\Toaster\Toaster;
use Model\Entity\EventDate;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Model\Repository\EventDateRepository;
use Core\Framework\Renderer\RendererInterface;

class AdminAction
{
    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Toaster $toaster;
    private EventDateRepository $eventDateRepository;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        // $this->eventDateRepository = $container->get(EventDateRepository::class);
    }

    public function home(ServerRequest $request)
    {
        return $this->renderer->render('@admin/home');
    }

    // public function editDate(ServerRequest $request)
    // {
    //     $event = $request->getAttribute('event');
    //     $eventDate = $this->eventDateRepository->findByEvent($event);

    //     if (!$eventDate) {
    //         $eventDate = new EventDate();
    //         $eventDate->setEvent($event);
    //     } else {
    //         $eventDate = $eventDate[0];
    //     }

    //     if ($request->getMethod() === 'POST') {
    //         $data = $request->getParsedBody();
    //         $eventDate->setDate(new \DateTime($data['date']));
    //         $this->eventDateRepository->save($eventDate);
    //         $this->toaster->makeToast("La date a été modifiée avec succès.", Toaster::SUCCESS);
            
    //     }

    //     return $this->renderer->render('@admin/edit-date', [
    //         'event' => $event,
    //         'eventDate' => $eventDate
    //     ]);
    // }
}
