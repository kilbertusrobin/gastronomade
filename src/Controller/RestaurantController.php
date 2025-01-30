<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RestaurantService;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Entity\Restaurant;

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
        try {
            return $this->restaurantService->getRestaurants();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
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
        try {
            return $this->restaurantService->getRestaurantById($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
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
        try {
            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON format');
            }
            return $this->restaurantService->createRestaurant($data);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
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
        try {
            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON format');
            }
            return $this->restaurantService->updateRestaurant($id, $data);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
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
        try {
            return $this->restaurantService->deleteRestaurant($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/restaurant/tags',
        summary: 'Get restaurants by tags',
        tags: ['Restaurant'],
        requestBody: new OA\RequestBody(
            description: 'Array of tag IDs',
            required: true,
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(type: 'integer')
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the list of restaurants with the specified tags',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Restaurant::class, groups: ['list_restaurants'])))
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    #[Route('/tags', name: 'tags', methods: ['POST'])]
    public function getRestaurantByTags(Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();
            $tagIds = json_decode($content, true);
            
            if (!is_array($tagIds)) {
                throw new \InvalidArgumentException('Invalid JSON format. Expected array of tag IDs');
            }
            
            return $this->restaurantService->getRestaurantByTag($tagIds);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}