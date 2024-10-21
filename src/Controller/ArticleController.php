<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ArticleController extends AbstractController
{
    #[Route('/article/create', name: 'create_article', methods: ['POST'])]
    public function createArticle(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $requestData = $this->parseRequest($request);

        $article = new Article();
        $this->handleRequest($article, $requestData);
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($article);
        $em->flush();

        return $this->json([
            'message' => sprintf('Article "%s" is created', $article->getTitle()),
            'article' => $serializer->serialize($article, 'json', [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context) {
                    return $object->getId();
                },
                'groups' => ['article', 'tag'],
            ])
        ]);
    }

    #[Route('/article/update/{id}', name: 'update_article', methods: ['POST'])]
    public function updateArticle(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $requestData = $this->parseRequest($request);

        $article = $em->getRepository(Article::class)->find($id);
        if (!$article) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND, sprintf('Article with id: %d not found', $id)
            );
        }

        $this->handleRequest($article, $requestData);
        $article->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($article);
        $em->flush();

        return $this->json([
            'message' => sprintf('Article "%s" is updated', $article->getTitle()),
            'article' => $serializer->serialize($article, 'json', [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context) {
                    return $object->getId();
                },
                'groups' => ['article', 'tag'],
            ])
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article', methods: ['POST'])]
    public function deleteArticle(int $id, EntityManagerInterface $em): JsonResponse
    {
        $article = $em->getRepository(Article::class)->find($id);
        if (!$article) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND, sprintf('Article with id: %d not found', $id)
            );
        }

        $em->remove($article);
        $em->flush();

        return $this->json(['message' => sprintf('Article "%s" is deleted', $article->getTitle())]);
    }

    private function handleRequest(Article $article, array $requestData): void
    {
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('body', TextareaType::class)
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'by_reference' => true,
            ])
            ->getForm();
        $form->submit($requestData);
        if (!$form->isValid()) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST, sprintf('Invalid data: %s', $form->getErrors(true))
            );
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function parseRequest(Request $request): array
    {
        $requestData = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid json');
        }

        return $requestData;
    }
}
