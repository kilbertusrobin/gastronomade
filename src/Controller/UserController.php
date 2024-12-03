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
}