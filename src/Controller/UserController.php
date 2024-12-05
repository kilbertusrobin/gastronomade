<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Service\UserService;
use App\Entity\User;

#[Route('/api/user', name: 'user-')]
class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[OA\Get(
        path: '/api/user',
        summary: 'Get all users',
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the list of all users',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: User::class, groups: ['list_users'])))
            )
        ]
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->userService->getUsers();
    }

    #[OA\Get(
        path: '/api/user/{id}',
        summary: 'Get a user by ID',
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the user',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the user details',
                content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['detail_user']))
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    #[Route('/{id}', name: 'read', methods: ['GET'])]
    public function read(int $id): JsonResponse
    {
        return $this->userService->readUser($id);
    }

    #[OA\Post(
        path: '/api/user/create',
        summary: 'Create a new user',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            description: 'User data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: User::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully'
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
        $data = json_decode($request->getContent(), true);
        return $this->userService->createUser($data);
    }

    #[OA\Put(
        path: '/api/user/update/{id}',
        summary: 'Update a user',
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the user',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated user data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: User::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    #[Route('/update/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->userService->updateUser($id, $data);
    }

    #[OA\Delete(
        path: '/api/user/delete/{id}',
        summary: 'Delete a user',
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the user',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->userService->deleteUser($id);
    }

    #[OA\Post(
        path: '/api/user/login',
        summary: 'Login a user',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            description: 'Login data',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'password', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User logged in successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials'
            )
        ]
    )]
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->userService->login($data);
    }
}