<?php

namespace App\Service;

use App\Repository\AvisRepository;
use App\Entity\Avis;
use App\Entity\User;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class AvisService
{
    private $avisRepository;
    private $serializer;
    private $entityManager;

    public function __construct(AvisRepository $avisRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->avisRepository = $avisRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    public function getAvis(): JsonResponse
    {
        $avisList = $this->avisRepository->findAll();
    
        $avisData = [];
        foreach ($avisList as $avis) {
            $avisData[] = [
                'id' => $avis->getId(),
                'content' => $avis->getContent(),
                'star_nb' => $avis->getStarNb(),
                'user' => [
                    'id' => $avis->getUser()->getId(),
                    'firstName' => $avis->getUser()->getFirstName(),
                    'lastName' => $avis->getUser()->getLastName(),
                ],
                'restaurant' => [
                    'id' => $avis->getRestaurant()->getId(),
                    'name' => $avis->getRestaurant()->getName(),
                ],
            ];
        }
    
        $data = $this->serializer->serialize($avisData, 'json', ['groups' => 'list_avis']);
    
        return new JsonResponse($data, 200, [], true);
    }

    public function readAvis(int $id): JsonResponse
    {
        $avis = $this->avisRepository->findOneBy(['id' => $id]);
    
        if (!$avis) {
            return new JsonResponse(['message' => 'Avis not found'], 404);
        }
    
        $avisData = [
            'id' => $avis->getId(),
            'content' => $avis->getContent(),
            'star_nb' => $avis->getStarNb(),
            'user' => [
                'id' => $avis->getUser()->getId(),
                'firstName' => $avis->getUser()->getFirstName(),
                'lastName' => $avis->getUser()->getLastName(),
            ],
            'restaurant' => [
                'id' => $avis->getRestaurant()->getId(),
                'name' => $avis->getRestaurant()->getName(),
            ],
        ];
    
        $data = $this->serializer->serialize($avisData, 'json');
    
        return new JsonResponse($data, 200, [], true);
    }

    public function createAvis(array $data): JsonResponse
    {
        $avis = new Avis();
        $avis->setContent($data['content']);
        $avis->setStarNb($data['star_nb']);
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['user']['id']]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
        $avis->setUser($user);

        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $data['restaurant']['id']]);
        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], 404);
        }
        $avis->setRestaurant($restaurant);
    
        $this->entityManager->persist($avis);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Avis created'], 201);
    }

    public function updateAvis(int $id, array $data): JsonResponse
    {
        $avis = $this->avisRepository->findOneBy(['id' => $id]);
    
        if (!$avis) {
            return new JsonResponse(['message' => 'Avis not found'], 404);
        }
    
        $avis->setContent($data['content']);
        $avis->setStarNb($data['star_nb']);
    
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['user']['id']]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
        $avis->setUser($user);
    
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => $data['restaurant']['id']]);
        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], 404);
        }
        $avis->setRestaurant($restaurant);
    
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Avis updated'], 200);
    }

    public function deleteAvis(int $id): JsonResponse
    {
        $avis = $this->avisRepository->findOneBy(['id' => $id]);
    
        if (!$avis) {
            return new JsonResponse(['message' => 'Avis not found'], 404);
        }
    
        $this->entityManager->remove($avis);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Avis deleted'], 200);
    }
}