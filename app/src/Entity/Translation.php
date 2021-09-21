<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="translation",
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
            'security' => "is_granted('ROLE_READER')",
        ],
        'put' => [
            'security' => "is_granted('ROLE_ADMIN')",
        ],
    ],
)]
class Translation
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected ?int $id = null;

    /**
     * Translation text.
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    protected string $text;

    /**
     * Language.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="translations")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=false)
     */
    #[Groups(['key:read', 'key:write'])]
    protected Language $language;

    /**
     * Key.
     *
     * @ORM\ManyToOne(targetEntity="Key", inversedBy="translations")
     * @ORM\JoinColumn(name="key_id", referencedColumnName="id", nullable=false)
     */
    #[Groups(['key:read', 'key:write'])]
    protected Key $key;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getKey(): Key
    {
        return $this->key;
    }

    public function setKey(Key $key): self
    {
        $this->key = $key;

        return $this;
    }
}
