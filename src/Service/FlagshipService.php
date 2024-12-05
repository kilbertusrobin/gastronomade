<?php

namespace App\Service;

use App\Repository\FlagshipDishRepository;
use App\Entity\FlagshipDish;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class FlagshipService
{
    private $flagshipDishRepository;
    private $serializer;
    private $entityManager;

    public function __construct(FlagshipDishRepository $flagshipDishRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->flagshipDishRepository = $flagshipDishRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    public function getFlagshipDishes(): JsonResponse
    {
        $flagshipDishList = $this->flagshipDishRepository->findAll();
    
        $flagshipDishData = [];
        foreach ($flagshipDishList as $flagshipDish) {
            $flagshipDishData[] = [
                'id' => $flagshipDish->getId(),
                'name' => $flagshipDish->getLabel(),
                'description' => $flagshipDish->getDescription(),
                'photo' => $flagshipDish->getPhoto(),
                'restaurant' => [
                    'id' => $flagshipDish->getRestaurant()->getId(),
                    'name' => $flagshipDish->getRestaurant()->getName(),
                ],
            ];
        }
    
        $data = $this->serializer->serialize($flagshipDishData, 'json');
    
        return new JsonResponse($data, 200, [], true);
    }

    public function readFlagshipDish(int $id): JsonResponse
    {
        $flagshipDish = $this->flagshipDishRepository->findOneBy(['id' => $id]);
    
        if (!$flagshipDish) {
            return new JsonResponse(['message' => 'Flagship dish not found'], 404);
        }
    
        $flagshipDishData = [
            'id' => $flagshipDish->getId(),
            'name' => $flagshipDish->getLabel(),
            'description' => $flagshipDish->getDescription(),
            'photo' => $flagshipDish->getPhoto(),
            'restaurant' => [
                'id' => $flagshipDish->getRestaurant()->getId(),
                'name' => $flagshipDish->getRestaurant()->getName(),
            ],
        ];
    
        $data = $this->serializer->serialize($flagshipDishData, 'json');
    
        return new JsonResponse($data, 200, [], true);
    }

    public function deleteFlagshipDish(int $id): JsonResponse
    {
        $flagshipDish = $this->flagshipDishRepository->findOneBy(['id' => $id]);
    
        if (!$flagshipDish) {
            return new JsonResponse(['message' => 'Flagship dish not found'], 404);
        }
    
        $this->entityManager->remove($flagshipDish);
        $this->entityManager->flush();
    
        return new JsonResponse(null, 204);
    }
}