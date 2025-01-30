<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\UserController;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends TestCase
{
    private UserController $controller;
    private $userService;

    protected function setUp(): void
    {
        // Créer un mock du UserService avant chaque test
        $this->userService = $this->createMock(UserService::class);
        $this->controller = new UserController($this->userService);
    }

    /**
     * Test de la méthode index() - cas de succès
     */
    public function testIndexSuccess(): void
    {
        // Données de test
        $expectedData = [
            [
                'id' => 1,
                'username' => 'john_doe',
                'email' => 'john@example.com',
                'roles' => ['ROLE_USER']
            ],
            [
                'id' => 2,
                'username' => 'jane_doe',
                'email' => 'jane@example.com',
                'roles' => ['ROLE_ADMIN']
            ]
        ];

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('getUsers')
            ->willReturn(new JsonResponse($expectedData));

        // Exécution du test
        $response = $this->controller->index();

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedData, json_decode($response->getContent(), true));
    }

    /**
     * Test de la méthode index() - cas d'erreur
     */
    public function testIndexError(): void
    {
        // Configuration du mock pour simuler une erreur
        $this->userService
            ->expects($this->once())
            ->method('getUsers')
            ->willThrowException(new \Exception('Database error'));

        // Exécution du test
        $response = $this->controller->index();

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Database error'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode read() - cas de succès
     */
    public function testReadSuccess(): void
    {
        // Données de test
        $userId = 1;
        $expectedData = [
            'id' => 1,
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'roles' => ['ROLE_USER']
        ];

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('readUser')
            ->with($userId)
            ->willReturn(new JsonResponse($expectedData));

        // Exécution du test
        $response = $this->controller->read($userId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedData, json_decode($response->getContent(), true));
    }

    /**
     * Test de la méthode read() - cas d'erreur
     */
    public function testReadError(): void
    {
        // Données de test
        $userId = 999;

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('readUser')
            ->with($userId)
            ->willThrowException(new \Exception('User not found'));

        // Exécution du test
        $response = $this->controller->read($userId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'User not found'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode create() - cas de succès
     */
    public function testCreateSuccess(): void
    {
        // Données de test
        $requestData = [
            'username' => 'new_user',
            'email' => 'new@example.com',
            'password' => 'password123',
            'roles' => ['ROLE_USER']
        ];
        
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('createUser')
            ->with($requestData)
            ->willReturn(new JsonResponse(['status' => 'created'], 201));

        // Exécution du test
        $response = $this->controller->create($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            ['status' => 'created'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode create() - cas d'erreur JSON invalide
     */
    public function testCreateInvalidJson(): void
    {
        // Création d'une requête avec un JSON invalide
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        // Exécution du test
        $response = $this->controller->create($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Invalid JSON format'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode update() - cas de succès
     */
    public function testUpdateSuccess(): void
    {
        // Données de test
        $userId = 1;
        $requestData = [
            'username' => 'updated_user',
            'email' => 'updated@example.com'
        ];
        
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('updateUser')
            ->with($userId, $requestData)
            ->willReturn(new JsonResponse(['status' => 'updated']));

        // Exécution du test
        $response = $this->controller->update($userId, $request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            ['status' => 'updated'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode delete() - cas de succès
     */
    public function testDeleteSuccess(): void
    {
        // Données de test
        $userId = 1;

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('deleteUser')
            ->with($userId)
            ->willReturn(new JsonResponse(['status' => 'deleted']));

        // Exécution du test
        $response = $this->controller->delete($userId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            ['status' => 'deleted'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode delete() - cas d'erreur
     */
    public function testDeleteError(): void
    {
        // Données de test
        $userId = 999;

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('deleteUser')
            ->with($userId)
            ->willThrowException(new \Exception('Delete failed'));

        // Exécution du test
        $response = $this->controller->delete($userId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Delete failed'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode login() - cas de succès
     */
    public function testLoginSuccess(): void
    {
        // Données de test
        $credentials = [
            'username' => 'john_doe',
            'password' => 'password123'
        ];
        
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($credentials)
        );

        $expectedResponse = new JsonResponse([
            'token' => 'jwt_token_example',
            'user' => [
                'id' => 1,
                'username' => 'john_doe',
                'roles' => ['ROLE_USER']
            ]
        ]);

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('login')
            ->with($credentials)
            ->willReturn($expectedResponse);

        // Exécution du test
        $response = $this->controller->login($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('user', $responseData);
    }

    /**
     * Test de la méthode login() - cas d'échec d'authentification
     */
    public function testLoginFailure(): void
    {
        // Données de test
        $credentials = [
            'username' => 'wrong_user',
            'password' => 'wrong_password'
        ];
        
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($credentials)
        );

        // Configuration du mock
        $this->userService
            ->expects($this->once())
            ->method('login')
            ->with($credentials)
            ->willThrowException(new \Exception('Invalid credentials'));

        // Exécution du test
        $response = $this->controller->login($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Invalid credentials'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode login() - cas d'erreur JSON invalide
     */
    public function testLoginInvalidJson(): void
    {
        // Création d'une requête avec un JSON invalide
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        // Exécution du test
        $response = $this->controller->login($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Invalid JSON format'],
            json_decode($response->getContent(), true)
        );
    }
}