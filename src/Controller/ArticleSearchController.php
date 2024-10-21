<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Repository\ArticleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ArticleSearchController extends AbstractController
{
    #[Route('/article/search', name: 'article_search')]
    public function search(
        Request $request,
        ArticleRepository $articleRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $form = $this->handleRequest($this->parseRequest($request));
        $tags = $form->get('tags')->getData();

        if (!empty($tags)) {
            $articles = $articleRepository->searchArticles($tags);
        } else {
            $articles = $articleRepository->findAll();
        }

        return $this->json([
            'message' => 'Search result',
            'articles' => $serializer->serialize($articles, 'json', [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context) {
                    return $object->getId();
                },
            ]),
        ]);
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article, SerializerInterface $serializer): Response
    {
        return $this->json([
            'message' => 'Search result',
            'article' => $serializer->serialize($article, 'json', [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context) {
                    return $object->getId();
                },
                'groups' => ['article', 'tag'],
            ]),
        ]);
    }

    private function handleRequest(array $requestData): FormInterface
    {
        $form = $this->createFormBuilder(new Article())
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

        return $form;
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
