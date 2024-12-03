<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserService;

#[Route('/api/user', name: 'user-')]
class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->userService->getUsers();
    }

    #[Route('/{id}', name: 'read', methods: ['GET'])]
    public function read(Request $request): JsonResponse
    {
        $id = $request->get('id');
        return $this->userService->readUser($id);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->userService->createUser($data);
    }

    #[Route('/update/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $data = json_decode($request->getContent(), true);
        return $this->userService->updateUser($id, $data);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $id = $request->get('id');
        return $this->userService->deleteUser($id);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->userService->login($data);
    }
}