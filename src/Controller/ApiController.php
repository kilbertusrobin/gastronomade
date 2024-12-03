<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Api fonctionnelle'
        ]);
    }
}
