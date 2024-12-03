<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class UserService
{
    private $userRepository;
    private $serializer;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    public function getUsers(): JsonResponse
    {
        $users = $this->userRepository->findAll();
    
        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'eMail' => $user->getEMail(),
                'userName' => $user->getUserName(),
                'typeUser' => [
                    'id' => $user->getTypeUser()->getId(),
                    'name' => $user->getTypeUser()->getLabel(),
                ],
            ];
        }
    
        $data = $this->serializer->serialize($userData, 'json');
    
        return new JsonResponse($data, 200, [], true);
    }

    public function readUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
    
        $userData = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'eMail' => $user->getEMail(),
            'userName' => $user->getUserName(),
            'typeUser' => [
                'id' => $user->getTypeUser()->getId(),
                'name' => $user->getTypeUser()->getLabel(),
            ],
        ];
    
        $data = $this->serializer->serialize($userData, 'json');
    
        return new JsonResponse($data, 200, [], true);
    }

    public function createUser($data): JsonResponse
    {
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEMail($data['eMail']);
        $user->setUserName($data['userName']);
        $user->setTypeUser($data['typeUser']);
    
        $this->userRepository->persist($user);
        $this->userRepository->flush();
    
        return new JsonResponse(['message' => 'User created'], 201);
    }
       
}
