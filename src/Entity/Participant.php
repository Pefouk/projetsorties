<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Asserts;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 * @UniqueEntity(fields={"pseudo"})
 * @UniqueEntity(fields={"mail"})
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Asserts\NotBlank()
     * @Asserts\Length(min=2, minMessage="Merci de mettre au moins 2 caractères pour le nom !", max=40, maxMessage="Merci de mettre moins de 40 caractères pour le nom !")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=40)
     * @Asserts\NotBlank()
     * @Asserts\Length(min=2, minMessage="Merci de mettre au moins 2 caractères pour le prénom !", max=40, maxMessage="Merci de mettre mois de 40 caractères pour le prenom !")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=12)
     * @Asserts\NotBlank()
     * @Asserts\Length(min=8, minMessage="Numero de téléphone incorrect", max=12, maxMessage="Numero de téléphone incorrect")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Asserts\NotBlank()
     * @Asserts\Email()
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     * @Asserts\NotBlank()
     * @Asserts\Length(min=6, minMessage="Merci de mettre au moins 6 caractères pour le mot de passe !", max=50, maxMessage="Merci de mettre mois de 50 caractères pour le mot de passe !")
     */
    private $motPasse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Asserts\NotBlank()
     * @Asserts\Length(min=2, minMessage="Merci de mettre un pseudo d'au moins 2 caractère !", max=50, maxMessage="Merci de mettre un pseudo de moins de 50 caractères")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur")
     */
    private $organise;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie", mappedBy="inscrits")
     */
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->organise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMotPasse(): ?string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): self
    {
        $this->motPasse = $motPasse;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrganise(): ArrayCollection
    {
        return $this->organise;
    }

    /**
     * @param ArrayCollection $organise
     */
    public function setOrganise(ArrayCollection $organise): void
    {
        $this->organise = $organise;
    }

    /**
     * @return ArrayCollection
     */
    public function getSorties(): ArrayCollection
    {
        return $this->sorties;
    }

    /**
     * @param ArrayCollection $sorties
     */
    public function setSorties(ArrayCollection $sorties): void
    {
        $this->sorties = $sorties;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        if ($this->administrateur === true) {
            return (["ROLE_ADMIN"]);
        } else {
            return (["ROLE_USER"]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->getMotPasse();
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getPseudo();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
}
