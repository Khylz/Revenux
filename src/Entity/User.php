<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(nullable: true)]
    private ?bool $opption = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isOpption(): ?bool
    {
        return $this->opption;
    }

    public function setOpption(?bool $opption): static
    {
        $this->opption = $opption;

        return $this;
    }
}


// <?php

// namespace App\Entity;

// use App\Repository\IncomeCategoryRepository;
// use Doctrine\ORM\Mapping as ORM;

// #[ORM\Entity(repositoryClass: IncomeCategoryRepository::class)]
// class IncomeCategory
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column(type: 'integer')]
//     private $id;

//     #[ORM\ManyToOne(targetEntity: User::class)]
//     #[ORM\JoinColumn(nullable: false)]
//     private $user;

//     #[ORM\Column(type: 'string', length: 100)]
//     private $categoryName;

//     #[ORM\Column(type: 'text', nullable: true)]
//     private $description;

//     #[ORM\Column(type: 'string', length: 7, nullable: true)]
//     private $color;

//     #[ORM\Column(type: 'datetime_immutable')]
//     private $creationDate;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getUser(): ?User
//     {
//         return $this->user;
//     }

//     public function setUser(?User $user): self
//     {
//         $this->user = $user;

//         return $this;
//     }

//     public function getCategoryName(): ?string
//     {
//         return $this->categoryName;
//     }

//     public function setCategoryName(string $categoryName): self
//     {
//         $this->categoryName = $categoryName;

//         return $this;
//     }

//     public function getDescription(): ?string
//     {
//         return $this->description;
//     }

//     public function setDescription(?string $description): self
//     {
//         $this->description = $description;

//         return $this;
//     }

//     public function getColor(): ?string
//     {
//         return $this->color;
//     }

//     public function setColor(?string $color): self
//     {
//         $this->color = $color;

//         return $this;
//     }

//     public function getCreationDate(): ?\DateTimeImmutable
//     {
//         return $this->creationDate;
//     }

//     public function setCreationDate(\DateTimeImmutable $creationDate): self
//     {
//         $this->creationDate = $creationDate;

//         return $this;
//     }
// }

