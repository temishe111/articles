<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return new Response(
            json_encode([
                'description' => 'Успешная регистрация',
                'content' => ['user_id' => 123456]
            ]),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}