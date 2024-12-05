<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Service\FlagshipService;
use App\Entity\FlagshipDish; // Assurez-vous que ce namespace est correct

#[Route('/api/flagship', name: 'flagship-')]
class FlagshipController extends AbstractController
{
    private $flagshipService;

    public function __construct(FlagshipService $flagshipService)
    {
        $this->flagshipService = $flagshipService;
    }

    #[OA\Get(
        path: '/api/flagship',
        summary: 'Get all flagship dishes',
        tags: ['Flagship'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the list of all flagship dishes',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: FlagshipDish::class, groups: ['list_flagship_dishes'])))
            )
        ]
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->flagshipService->getFlagshipDishes();
    }

    #[OA\Get(
        path: '/api/flagship/{id}',
        summary: 'Get a flagship dish by ID',
        tags: ['Flagship'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the flagship dish',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the flagship dish details',
                content: new OA\JsonContent(ref: new Model(type: FlagshipDish::class, groups: ['detail_flagship_dish']))
            ),
            new OA\Response(
                response: 404,
                description: 'Flagship dish not found'
            )
        ]
    )]
    #[Route('/{id}', name: 'read', methods: ['GET'])]
    public function read(int $id): JsonResponse
    {
        return $this->flagshipService->readFlagshipDish($id);
    }

    #[OA\Delete(
        path: '/api/flagship/delete/{id}',
        summary: 'Delete a flagship dish',
        tags: ['Flagship'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the flagship dish',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Flagship dish deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Flagship dish not found'
            )
        ]
    )]
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->flagshipService->deleteFlagshipDish($id);
    }
}