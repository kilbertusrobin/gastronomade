<?php

namespace App\Service;

use App\Repository\RestaurantRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

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
    
}
