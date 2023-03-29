<?php

// namespace Model\Repository;

// use Model\Entity\EventDate;
// use Doctrine\ORM\EntityManagerInterface;


// class EventDateRepository
// {
//     private EntityManagerInterface $entityManager;

//     public function __construct(EntityManagerInterface $entityManager)
//     {
//         $this->entityManager = $entityManager;
//     }

//     public function find($id): ?EventDate
//     {
//         return $this->entityManager->getRepository(EventDate::class)->find($id);
//     }

//     public function findByEvent($event): array
//     {
//         return $this->entityManager->getRepository(EventDate::class)->findBy(['event' => $event]);
//     }

//     public function save(EventDate $eventDate): void
//     {
//         $this->entityManager->persist($eventDate);
//         $this->entityManager->flush();
//     }

//     public function delete(EventDate $eventDate): void
//     {
//         $this->entityManager->remove($eventDate);
//         $this->entityManager->flush();
//     }
// }
