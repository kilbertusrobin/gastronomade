<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Service\FlagshipService;
use App\Entity\FlagshipDish;

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
        try {
            return $this->flagshipService->getFlagshipDishes();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
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
        try {
            return $this->flagshipService->readFlagshipDish($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
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
        try {
            return $this->flagshipService->deleteFlagshipDish($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/flagship/create',
        summary: 'Create a flagship dish',
        tags: ['Flagship'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: FlagshipDish::class)
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Flagship dish created successfully'
            )
        ]
    )]
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $this->flagshipService->createFlagshipDish($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/flagship/update/{id}',
        summary: 'Update a flagship dish',
        tags: ['Flagship'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: FlagshipDish::class)
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Flagship dish updated successfully'
            )
        ]
    )]
    #[Route('/update/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $id = $request->attributes->get('id');
            return $this->flagshipService->updateFlagshipDish($data, $id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
