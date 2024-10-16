<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    #[Route('/tag/create', name: 'create_tag', methods: ['POST'])]
    public function createTag(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $requestData = $this->parseRequest($request);

        $tag = new Tag();
        $this->validate($tag, $requestData);

        $em->persist($tag);
        $em->flush();

        return $this->json([
            'message' => sprintf('Tag %s created', $tag->getName()),
        ]);
    }

    #[Route('/tag/update/{id}', name: 'update_tag', methods: ['POST'])]
    public function updateTag(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $requestData = $this->parseRequest($request);

        $tag = $em->getRepository(Tag::class)->find($id);
        if ($tag === null) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Tag to update not found');
        }

        $this->validate($tag, $requestData);
        $em->persist($tag);
        $em->flush();


        return $this->json([
            'message' => sprintf('Tag id: %d %s updated', $id, $tag->getName()),
        ]);
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

    /**
     * @param Tag $tag
     * @param array $requestData
     * @return void
     */
    private function validate(Tag $tag, array $requestData): void
    {
        $form = $this->createFormBuilder($tag)
            ->add('name', TextType::class)
            ->getForm();
        $form->submit($requestData);

        if (!$form->isValid()) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST, sprintf('Invalid data: %s', $form->getErrors(true)));
        }
    }
}
