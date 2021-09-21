<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ExportLanguage;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="language",
 * )
 */
#[ApiResource(
    iri: 'http://schema.org/Language',
    attributes: [
        'security' => "is_granted('ROLE_READER')",
    ],
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_READER')",
        ],
        'export' => [
            'method' => 'GET',
            'pagination_enabled' => false,
            'route_name' => 'export_languages',
            'openapi_context' => [
                'parameters' => [
                    [
                        'name' => 'format',
                        'required' => true,
                        'type' => 'array',
                        'default' => 'json',
                        'in' => 'path',
                        'enum' => ['json', 'yaml'],
                        'description' => 'Format to download languages: json or yaml',
                    ],
                ],
            ],

            'controller' => ExportLanguage::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('ROLE_READER')",
        ],
    ],
)]

class Language
{
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected ?int $id = null;

    /**
     * Language name.
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    #[Assert\NotBlank]
    protected string $name;

    /**
     * Language iso code.
     *
     * @ORM\Column(name="isocode", type="string", length=10, nullable=false)
     */
    #[Assert\Locale]
    #[Assert\NotBlank]
    protected string $isoCode;

    /**
     * Is Left to Right?
     *
     * @ORM\Column(name="ltr", type="boolean", nullable=false)
     */
    protected bool $ltr;

    /**
     * Translations from this language.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Translation", mappedBy="language", cascade={"remove"})
     */
    protected Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->ltr = true;
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

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function isLtr(): bool
    {
        return $this->ltr;
    }

    public function setLtr(bool $ltr): self
    {
        $this->ltr = $ltr;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection|Translation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param ArrayCollection|Collection $translations
     *
     * @return Language
     */
    public function setTranslations(ArrayCollection|Collection $translations): self
    {
        $this->translations = $translations;

        return $this;
    }
}
