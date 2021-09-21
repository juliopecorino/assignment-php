<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Key;
use App\Entity\Language;
use App\Entity\Translation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class UpdateTranslation extends AbstractController
{
    /**
     * @Route("/api/keys/{id}/{isoCode}", name="update_translation")
     */
    public function __invoke(int $id, string $isoCode, Request $request): Response
    {
        if (!$this->isGranted('ROLE_READER')) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $key = $entityManager->getRepository(Key::class)->find($id);

        if (null === $key) {
            return new Response('Key not found', 500);
        }

        $language = $entityManager->getRepository(Language::class)->findOneBy([
            'isoCode' => $isoCode,
        ]);

        /** @var Translation|null $translation */
        $translation = $entityManager->getRepository(Translation::class)->findOneBy([
            'key' => $key,
            'language' => $language,
        ]);

        if (null !== $translation) {
            $text = (string) $request->get('text');
            $translation->setText($text);
            $entityManager->persist($translation);
            $entityManager->flush();

            return new Response('Updated', 200);
        }

        return new Response('Translation not found', 500);
    }
}
