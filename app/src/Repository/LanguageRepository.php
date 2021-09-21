<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    public function getLanguagesList(): array
    {
        /** @var Language[] $languages */
        $languages = $this->findAll();

        $list = [];
        foreach ($languages as $language) {
            $translations = $language->getTranslations();
            $result = [];
            foreach ($translations as $translation) {
                $result[$translation->getKey()->getName()] = $translation->getText();
            }
            $list[$language->getIsoCode()] = $result;
        }

        return $list;
    }
}
