<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\UpdateTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @ORM\Table(
 *     name="key_lang",
 *     uniqueConstraints={@UniqueConstraint(name="name_idx", columns={"name"})}
 * )
 */
#[ApiResource(
    attributes: [
        'security' => "is_granted('ROLE_READER')",
    ],
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_READER')",
        ],
        'post' => [
            'security' => "is_granted('ROLE_ADMIN')",
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'get',
            'security' => "is_granted('ROLE_READER')",
        ],
        'put' => [
            'security' => "is_granted('ROLE_ADMIN')",
        ],
        'update_translation' => [
            'method' => 'PUT',
            'route_name' => 'update_translation',
            'openapi_context' => [
                'parameters' => [
                    [
                        'name' => 'id',
                        'required' => true,
                        'in' => 'path',
                        'enum' => ['json', 'yaml'],
                        'description' => 'Key id',
                    ],
                    [
                        'name' => 'isoCode',
                        'required' => true,
                        'in' => 'path',
                        'enum' => ['json', 'yaml'],
                        'description' => 'iso code',
                    ],
                    [
                        'name' => 'text',
                        'required' => true,
                        'in' => 'query',
                        'description' => 'Text to update',
                    ],
                ],
            ],
            'controller' => UpdateTranslation::class,
        ],

        'delete' => [
            'security' => "is_granted('ROLE_ADMIN')",
        ],
    ],
    normalizationContext: [
        'groups' => ['key:read'],
    ],
    denormalizationContext: [
        'groups' => ['key:write'],
    ],
)]
class Key
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    #[Groups(['key:read', 'key:write'])]
    protected ?int $id = null;

    /**
     * Key.
     *
     * @ORM\Column(name="name", type="string", nullable=false, unique=true)
     */
    #[Assert\NotBlank]
    #[Groups(['key:read', 'key:write'])]
    protected string $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Translation", mappedBy="key", cascade={"remove"})
     */
    #[ApiSubresource]
    protected Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection|Translation[]
     */
    public function getTranslations(): ArrayCollection|Collection
    {
        return $this->translations;
    }

    /**
     * @param ArrayCollection|Collection $translations
     */
    public function setTranslations(ArrayCollection|Collection $translations): self
    {
        $this->translations = $translations;

        return $this;
    }
}
