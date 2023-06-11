<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EntrepriseRepository;
// use Doctrine\Common\Collections\Collection;
// use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
// use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[UniqueEntity('email')]
#[UniqueEntity('nom')]
#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\EntrepriseListener'])]
class Entreprise implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(min: 2, max: 180)]
    #[Assert\Email()] 
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private $roles = ["ROLE_USER"];

    private $plainPassword;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $base_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $api_key = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_tel = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_ifu = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_nim = null;

    #[ORM\ManyToOne(inversedBy: 'entreprises')]
    private ?Installation $installation = null;

    #[ORM\Column(length:255)]
    private ?string $nomDeLaBase = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $user = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $passBase = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $codeEntreprise = null;

    public function __construct()
    {
        // $this->setNomDeLaBase();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    
//---------------------------------------------------------------
    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }    

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername()
    {
        // Retourner le nom d'utilisateur de l'entreprise
        return $this->email;
    }
    
//----------------------------------------------------------------------------
    public function getBaseUrl(): ?string
    {
        return $this->base_url;
    }

    public function setBaseUrl(string $base_url): self
    {
        $this->base_url = $base_url;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiKey(string $api_key): self
    {
        $this->api_key = $api_key;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->num_tel;
    }

    public function setNumTel(int $num_tel): self
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    public function getNumIfu(): ?int
    {
        return $this->num_ifu;
    }

    public function setNumIfu(int $num_ifu): self
    {
        $this->num_ifu = $num_ifu;

        return $this;
    }

    public function getNumNim(): ?int
    {
        return $this->num_nim;
    }

    public function setNumNim(int $num_nim): self
    {
        $this->num_nim = $num_nim;

        return $this;
    }

    public function getInstallation(): ?Installation
    {
        return $this->installation;
    }

    public function setInstallation(?Installation $installation): self
    {
        $this->installation = $installation;

        return $this;
    }

    public function getNomDeLaBase(): ?string
    {
        return $this->nomDeLaBase;
    }

    public function setNomDeLaBase(): self
    {
        
        $this->nomDeLaBase = 'erp' . str_replace(' ', '', strtolower($this->getNom()));

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(): self
    {
        $this->user = 'erp_' . str_replace(' ', '', strtolower($this->getNom()));

        return $this;
    }

    public function getPassBase(): ?string
    {
        return $this->passBase;
    }

    public function setPassBase(): self
    {
        $this->passBase = $this->generateStrongPassword();
        return $this;
    }

    public function getCodeEntreprise(): ?string
    {
        return $this->codeEntreprise;
    }

    public function setCodeEntreprise(): self
    {
        $this->codeEntreprise = $this->genererCode();

        return $this;
    }

    public function generateStrongPassword(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_';
        $password = '';
        $length = 24;

        $characterCount = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, $characterCount - 1);
            $password .= $characters[$index];
        }

        return $password;
    }

    public function genererCode() : string
    {
        //  $chiffre = mt_rand(0, 9);
        $lettres = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
        $code = 1 . $lettres;
        return $code;
    }

    public function createjdb(string $dbName) : string{
        try {
            $pdo = new \PDO('mysql:host=localhost', 'root', '');
            $pdo->exec("CREATE DATABASE $dbName");

            // Attribution des informations de connexion à l'entreprise
            return 'La base de données ' . $dbName . ' a été créée avec succès.';
            // echo "La base de données $dbName a été créée avec succès.";
        } catch (\PDOException $e) {

            return 'Erreur lors de la création du dossier de destination : ' . $e->getMessage();
            // echo "Erreur lors de la création de la base de données : " . $e->getMessage();
        } 
    }
  
}