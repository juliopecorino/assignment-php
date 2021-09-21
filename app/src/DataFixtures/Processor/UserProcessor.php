<?php

declare(strict_types=1);

namespace App\DataFixtures\Processor;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->entityManager = $entityManager;
    }

    public function preProcess(string $fixtureId, $object): void
    {
        if (false === ($object instanceof User)) {
            return;
        }

        $result = $this->hasher->hashPassword(
            $object,
            (string) $object->getPlainPassword()
        );
        $object->setPassword($result);
    }

    public function postProcess(string $fixtureId, $object): void
    {
    }
}
