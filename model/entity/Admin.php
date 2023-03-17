<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="administrateur")
 */

class Admin
{
    /**
     * @ORM\Id
     *@ORM\Column(type="integer", name="id")
     *@orm\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private int $id;
    /**
     * @ORM\Column(type="string", name="nom", length="55")
     * @var string
     */
    private string $nom;

    /**
     * @ORM\Column(type="string", name="mdp", length="255")
     * @var string
     */
    private string $mdp;

       /**
     * @ORM\Column(type="string", name="mail", length="55")
     * @var string
     */
    private string $mail;

    /**
     * @ORM\Column(type="string", name="phone", length="255")
     * @var string
     */
    private string $phone;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Admin
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set mdp.
     *
     * @param string $mdp
     *
     * @return Admin
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;

        return $this;
    }

    /**
     * Get mdp.
     *
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return Admin
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Admin
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
