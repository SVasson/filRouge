<?php

namespace Model\Entity;

use Model\Entity\User;
use Model\Entity\Event;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Participation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"}, inversedBy="participations")
     * @ORM\JoinColumn(nullable="false", name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    // getters and setters for $id, $user and $event
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }
}
