<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RestaurantService;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Entity\Restaurant; // Assurez-vous que ce namespace est correct

#[Route('/api/restaurant', name: 'app_restaurant-')]
class RestaurantController extends AbstractController
{
    private $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    #[OA\Get(
        path: '/api/restaurant',
        summary: 'Get all restaurants',
        tags: ['Restaurant'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the list of all restaurants',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Restaurant::class, groups: ['list_restaurants'])))
            )
        ]
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->restaurantService->getRestaurants();
    }

    #[OA\Get(
        path: '/api/restaurant/{id}',
        summary: 'Get a restaurant by ID',
        tags: ['Restaurant'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the restaurant',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the restaurant details',
                content: new OA\JsonContent(ref: new Model(type: Restaurant::class, groups: ['detail_restaurant']))
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant not found'
            )
        ]
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->restaurantService->getRestaurantById($id);
    }

    #[OA\Post(
        path: '/api/restaurant/create',
        summary: 'Create a new restaurant',
        tags: ['Restaurant'],
        requestBody: new OA\RequestBody(
            description: 'Restaurant data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Restaurant::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Restaurant created successfully'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->restaurantService->createRestaurant($data);
    }

    #[OA\Put(
        path: '/api/restaurant/{id}/update',
        summary: 'Update a restaurant',
        tags: ['Restaurant'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the restaurant',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated restaurant data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Restaurant::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Restaurant updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant not found'
            )
        ]
    )]
    #[Route('/{id}/update', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->restaurantService->updateRestaurant($id, $data);
    }

    #[OA\Delete(
        path: '/api/restaurant/{id}/delete',
        summary: 'Delete a restaurant',
        tags: ['Restaurant'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the restaurant',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Restaurant deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant not found'
            )
        ]
    )]
    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->restaurantService->deleteRestaurant($id);
    }
}