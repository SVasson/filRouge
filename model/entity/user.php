<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity
 */
class User{
  /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
  
     * @var int
     */
    private int $id;
      /**
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $nom;
  /**
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $prenom;
      /**
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $numeroDeTel;
  /**
     * @ORM\Column(type="string", length="150")
     * @var string
     */
    private string $mail;
  /**
     * @ORM\Column(type="string", length="255")
     * @var string
     */
    private string $mdp;

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
     * @return User
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
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return User
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
     * Set mdp.
     *
     * @param string $mdp
     *
     * @return User
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

    public function hydrate(array $data): self
    {
        foreach ($data as $key => $value)
        {
            $method = 'set'.ucFirst($key);
            if(method_exists($this, $method))
            {
                $this->$method($value);

            }
        }
        return $this;
    }




    /**
     * Set numeroDeTel.
     *
     * @param string $numeroDeTel
     *
     * @return User
     */
    public function setNumeroDeTel($numeroDeTel)
    {
        $this->numeroDeTel = $numeroDeTel;

        return $this;
    }

    /**
     * Get numeroDeTel.
     *
     * @return string
     */
    public function getNumeroDeTel()
    {
        return $this->numeroDeTel;
    }
}
