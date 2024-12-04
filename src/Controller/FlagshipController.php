<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FlagshipService;

#[Route('/api/flagship', name: 'flagship-')]
class FlagshipController extends AbstractController
{
    private $flagshipService;

    public function __construct(FlagshipService $flagshipService)
    {
        $this->flagshipService = $flagshipService;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->flagshipService->getFlagshipDishes();
    }

    #[Route('/{id}', name: 'read', methods: ['GET'])]
    public function read(Request $request): JsonResponse
    {
        return $this->flagshipService->readFlagshipDish($request->get('id'));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        return $this->flagshipService->deleteFlagshipDish($request->get('id'));
    }
}