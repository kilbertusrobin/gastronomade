<?php
namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\TypeUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class UserService
{
    private $userRepository;
    private $serializer;
    private $entityManager;
    private $passwordHasher;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Groups(['list_users'])]
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
    
        $data = $this->serializer->serialize($userData, 'json', ['groups' => 'list_users']);
    
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
        $existingUser = $this->userRepository->findOneBy(['eMail' => $data['eMail']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'User already exists'], 400);
        }
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEMail($data['eMail']);
        $user->setUserName($data['userName']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        $typeUser = $this->entityManager->getRepository(TypeUser::class)->findOneBy(['id' => $data['typeUserId']]);
        if (!$typeUser) {
            return new JsonResponse(['message' => 'TypeUser not found'], 404);
        }
        $user->setTypeUser($typeUser);
    
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'User created'], 201);
    }

    public function updateUser($id, $data): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
    
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEMail($data['eMail']);
        $user->setUserName($data['userName']);
        
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
    
        $typeUser = $this->entityManager->getRepository(TypeUser::class)->findOneBy(['id' => $data['typeUserId']]);
        if (!$typeUser) {
            return new JsonResponse(['message' => 'TypeUser not found'], 404);
        }
        $user->setTypeUser($typeUser);
    
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'User updated'], 200);
    }

    public function deleteUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
    
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'User deleted'], 200);
    }

    public function login($data): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['eMail' => $data['eMail']]);
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
    
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['message' => 'Invalid password'], 400);
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
        return new JsonResponse($userData, 200);
    }
}
