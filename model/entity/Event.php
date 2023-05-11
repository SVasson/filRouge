<?php

namespace Model\Entity;

use Model\Entity\Participation;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $endDate;

         /**
     * @ORM\Column(type="string", name="img_path")
     * @var string
     */

     private string $imgPath;
     
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }



    /**
     * Set imgPath.
     *
     * @param string $imgPath
     *
     * @return Event
     */
    public function setImgPath($imgPath)
    {
        $this->imgPath = $imgPath;

        return $this;
    }

    /**
     * Get imgPath.
     *
     * @return string
     */
    public function getImgPath()
    {
        return $this->imgPath;
    }





        /**
     * @ORM\OneToMany(targetEntity=Participation::class, mappedBy="event")
     */
    private $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipation(): Collection
    {
        return $this->participations;
    }


    /**
     * Add participation.
     *
     * @param \Model\Entity\Participation $participation
     *
     * @return Event
     */
    public function addParticipation(Participation $participation)
    {
        $this->participations[] = $participation;

        return $this;
    }

    /**
     * Remove participation.
     *
     * @param \Model\Entity\Participation $participation
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeParticipation(Participation $participation)
    {
        return $this->participations->removeElement($participation);
    }
}
