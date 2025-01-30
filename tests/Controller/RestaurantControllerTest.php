<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\RestaurantController;
use App\Service\RestaurantService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestaurantControllerTest extends TestCase
{
    private RestaurantController $controller;
    private $restaurantService;

    protected function setUp(): void
    {
        // Créer un mock du RestaurantService avant chaque test
        $this->restaurantService = $this->createMock(RestaurantService::class);
        $this->controller = new RestaurantController($this->restaurantService);
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
                'name' => 'Le Restaurant Italien',
                'address' => '123 Rue de la Pizza',
                'phone' => '0123456789',
                'description' => 'Restaurant italien authentique',
                'opening_hours' => '10:00-22:00'
            ],
            [
                'id' => 2,
                'name' => 'Le Bistrot Français',
                'address' => '456 Avenue du Croissant',
                'phone' => '9876543210',
                'description' => 'Cuisine française traditionnelle',
                'opening_hours' => '12:00-23:00'
            ]
        ];

        // Configuration du mock
        $this->restaurantService
            ->expects($this->once())
            ->method('getRestaurants')
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
        $this->restaurantService
            ->expects($this->once())
            ->method('getRestaurants')
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
     * Test de la méthode show() - cas de succès
     */
    public function testShowSuccess(): void
    {
        // Données de test
        $restaurantId = 1;
        $expectedData = [
            'id' => 1,
            'name' => 'Le Restaurant Italien',
            'address' => '123 Rue de la Pizza',
            'phone' => '0123456789',
            'description' => 'Restaurant italien authentique',
            'opening_hours' => '10:00-22:00'
        ];

        // Configuration du mock
        $this->restaurantService
            ->expects($this->once())
            ->method('getRestaurantById')
            ->with($restaurantId)
            ->willReturn(new JsonResponse($expectedData));

        // Exécution du test
        $response = $this->controller->show($restaurantId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedData, json_decode($response->getContent(), true));
    }

    /**
     * Test de la méthode show() - cas d'erreur
     */
    public function testShowError(): void
    {
        // Données de test
        $restaurantId = 999;

        // Configuration du mock
        $this->restaurantService
            ->expects($this->once())
            ->method('getRestaurantById')
            ->with($restaurantId)
            ->willThrowException(new \Exception('Restaurant not found'));

        // Exécution du test
        $response = $this->controller->show($restaurantId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Restaurant not found'],
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
            'name' => 'Nouveau Restaurant',
            'address' => '789 Boulevard de la Gastronomie',
            'phone' => '0123456789',
            'description' => 'Nouveau concept restaurant',
            'opening_hours' => '11:00-23:00'
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
        $this->restaurantService
            ->expects($this->once())
            ->method('createRestaurant')
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
        $restaurantId = 1;
        $requestData = [
            'name' => 'Restaurant Mis à Jour',
            'description' => 'Description mise à jour'
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
        $this->restaurantService
            ->expects($this->once())
            ->method('updateRestaurant')
            ->with($restaurantId, $requestData)
            ->willReturn(new JsonResponse(['status' => 'updated']));

        // Exécution du test
        $response = $this->controller->update($restaurantId, $request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            ['status' => 'updated'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode update() - cas d'erreur JSON invalide
     */
    public function testUpdateInvalidJson(): void
    {
        // Données de test
        $restaurantId = 1;
        
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
        $response = $this->controller->update($restaurantId, $request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Invalid JSON format'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode delete() - cas de succès
     */
    public function testDeleteSuccess(): void
    {
        // Données de test
        $restaurantId = 1;

        // Configuration du mock
        $this->restaurantService
            ->expects($this->once())
            ->method('deleteRestaurant')
            ->with($restaurantId)
            ->willReturn(new JsonResponse(['status' => 'deleted']));

        // Exécution du test
        $response = $this->controller->delete($restaurantId);

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
        $restaurantId = 999;

        // Configuration du mock
        $this->restaurantService
            ->expects($this->once())
            ->method('deleteRestaurant')
            ->with($restaurantId)
            ->willThrowException(new \Exception('Delete failed'));

        // Exécution du test
        $response = $this->controller->delete($restaurantId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Delete failed'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode getRestaurantByTags() - cas de succès
     */
    public function testGetRestaurantByTagsSuccess(): void
    {
        // Données de test
        $tagIds = [1, 2, 3];
        $expectedData = [
            [
                'id' => 1,
                'name' => 'Restaurant 1',
                'tags' => [1, 2]
            ],
            [
                'id' => 2,
                'name' => 'Restaurant 2',
                'tags' => [2, 3]
            ]
        ];

        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($tagIds)
        );

        // Configuration du mock
        $this->restaurantService
            ->expects($this->once())
            ->method('getRestaurantByTag')
            ->with($tagIds)
            ->willReturn(new JsonResponse($expectedData));

        // Exécution du test
        $response = $this->controller->getRestaurantByTags($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedData, json_decode($response->getContent(), true));
    }

    /**
     * Test de la méthode getRestaurantByTags() - cas d'erreur format invalide
     */
    public function testGetRestaurantByTagsInvalidFormat(): void
    {
        // Création d'une requête avec des données non valides (pas un tableau)
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode("not an array")
        );

        // Exécution du test
        $response = $this->controller->getRestaurantByTags($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Invalid JSON format. Expected array of tag IDs'],
            json_decode($response->getContent(), true)
        );
    }
}