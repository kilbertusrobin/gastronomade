<?php

namespace App\Service;

use App\Repository\RestaurantRepository;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class RestaurantService
{
    private $restaurantRepository;
    private $serializer;
    private $entityManager;

    public function __construct(RestaurantRepository $restaurantRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    public function getRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantRepository->findAll();
    
        $restaurantData = [];
        foreach ($restaurants as $restaurant) {
            $restaurantData[] = [
                'id' => $restaurant->getId(),
                'name' => $restaurant->getName(),
                'city' => $restaurant->getCity(),
                'postalCode' => $restaurant->getPostalCode(),
                'adress' => $restaurant->getAdress(),
                'phone' => $restaurant->getPhone(),
                'lat' => $restaurant->getLat(),
                'longitude' => $restaurant->getLongitude(),
            ];
        }
    
        $data = $this->serializer->serialize($restaurantData, 'json', ['groups' => 'list_restaurants']);
    
        return new JsonResponse($data, 200, [], true);
    }

    public function getRestaurantById(int $id): JsonResponse
    {
        $restaurant = $this->restaurantRepository->findOneBy(['id' => $id]);
    
        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], 404);
        }
    
        $flagshipDishes = $restaurant->getFlagshipDishes()->toArray();
        $flagshipDishesData = array_map(function ($dish) {
            return [
                'id' => $dish->getId(),
                'label' => $dish->getLabel(),
                'description' => $dish->getDescription(),
                'photo' => $dish->getPhoto(),
            ];
        }, $flagshipDishes);
    
        $restaurantData = [
            'id' => $restaurant->getId(),
            'name' => $restaurant->getName(),
            'city' => $restaurant->getCity(),
            'postalCode' => $restaurant->getPostalCode(),
            'adress' => $restaurant->getAdress(),
            'phone' => $restaurant->getPhone(),
            'lat' => $restaurant->getLat(),
            'longitude' => $restaurant->getLongitude(),
            'flagshipDishes' => $flagshipDishesData,
        ];
    
        $data = $this->serializer->serialize($restaurantData, 'json');
    
        return new JsonResponse($data, 200, [], true);
    }
    

    public function createRestaurant(array $data): JsonResponse
    {
        if (isset($data['id']) && $this->restaurantRepository->find($data['id'])) {
            return new JsonResponse(['error' => 'Restaurant with this ID already exists'], 400);
        }

        $restaurant = new Restaurant();
        $restaurant->setName($data['name']);
        $restaurant->setCity($data['city']);
        $restaurant->setPostalCode($data['postalCode']);
        $restaurant->setAdress($data['adress']);
        $restaurant->setPhone($data['phone']);
        $restaurant->setLat($data['lat']);
        $restaurant->setLongitude($data['longitude']);
    
        $this->entityManager->persist($restaurant);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Restaurant created'], 201);
    }

    public function updateRestaurant(int $id, array $data): JsonResponse
    {
        $restaurant = $this->restaurantRepository->findOneBy(['id' => $id]);
    
        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], 404);
        }
    
        $restaurant->setName($data['name']);
        $restaurant->setCity($data['city']);
        $restaurant->setPostalCode($data['postalCode']);
        $restaurant->setAdress($data['adress']);
        $restaurant->setPhone($data['phone']);
        $restaurant->setLat($data['lat']);
        $restaurant->setLongitude($data['longitude']);
    
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Restaurant updated'], 200);
    }

    public function deleteRestaurant(int $id): JsonResponse
    {
        $restaurant = $this->restaurantRepository->findOneBy(['id' => $id]);
    
        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], 404);
        }
    
        $this->entityManager->remove($restaurant);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Restaurant deleted'], 200);
    }
}