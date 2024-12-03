<?php

namespace App\Service;

use App\Repository\RestaurantRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class RestaurantService
{
    private $restaurantRepository;
    private $serializer;

    public function __construct(RestaurantRepository $restaurantRepository, SerializerInterface $serializer)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->serializer = $serializer;
    }

    public function getRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantRepository->findAll();
        $data = $this->serializer->serialize($restaurants, 'json');
        return new JsonResponse($data, 200, [], true);
    }
    
    public function getRestaurantById(int $id): JsonResponse
    {
        $restaurant = $this->restaurantRepository->find($id);
    
        if (!$restaurant) {
            return new JsonResponse(['error' => 'Restaurant not found'], 404);
        }
    
        $data = $this->serializer->serialize($restaurant, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    public function createRestaurant(array $data): JsonResponse
    {
        if (isset($data['id']) && $this->restaurantRepository->findBy($data['id'])) {
            return new JsonResponse(['error' => 'Restaurant with this ID already exists'], 400);
        }

        $restaurant = $this->restaurantRepository->createRestaurant($data);
        $data = $this->serializer->serialize($restaurant, 'json');
        return new JsonResponse($data, 201, [], true);
    }
}