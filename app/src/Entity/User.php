<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user",
 * )
 */
#[ApiResource(
    attributes: [
        'security' => "is_granted('ROLE_ADMIN')",
    ],
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_ADMIN')",
        ],
        'post' => [
            'security' => "is_granted('ROLE_ADMIN')",
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'get',
        ],
    ],
    normalizationContext: [
        'groups' => ['user:read'],
    ],
    denormalizationContext: [
        'groups' => ['user:write'],
    ],
)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    #[Groups(['user:read'])]
    protected ?int $id = null;

    /**
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    protected string $email;

    #[Groups(['user:write'])]
    protected ?string $plainPassword = null;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected ?string $password = null;

    /**
     * An array of roles. Example: ROLE_READER, ROLE_ADMIN.
     *
     * @ORM\Column(type="array")
     *
     * @var mixed[]|string[]
     */
    #[Groups(['user:read', 'user:write'])]
    protected array $roles = [];

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed[]|string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(string $role): self
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (! \in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param mixed[]|string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
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

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
