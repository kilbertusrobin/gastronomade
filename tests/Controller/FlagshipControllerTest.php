<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\FlagshipController;
use App\Service\FlagshipService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FlagshipControllerTest extends TestCase
{
    private FlagshipController $controller;
    private $flagshipService;

    protected function setUp(): void
    {
        // Créer un mock du FlagshipService avant chaque test
        $this->flagshipService = $this->createMock(FlagshipService::class);
        $this->controller = new FlagshipController($this->flagshipService);
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
                'name' => 'Spaghetti Carbonara',
                'description' => 'Plat signature italien',
                'price' => 15.99,
                'category' => 'Pâtes'
            ],
            [
                'id' => 2,
                'name' => 'Coq au Vin',
                'description' => 'Plat traditionnel français',
                'price' => 22.50,
                'category' => 'Viandes'
            ]
        ];

        // Configuration du mock
        $this->flagshipService
            ->expects($this->once())
            ->method('getFlagshipDishes')
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
        $this->flagshipService
            ->expects($this->once())
            ->method('getFlagshipDishes')
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
        $dishId = 1;
        $expectedData = [
            'id' => 1,
            'name' => 'Spaghetti Carbonara',
            'description' => 'Plat signature italien',
            'price' => 15.99,
            'category' => 'Pâtes'
        ];

        // Configuration du mock
        $this->flagshipService
            ->expects($this->once())
            ->method('readFlagshipDish')
            ->with($dishId)
            ->willReturn(new JsonResponse($expectedData));

        // Exécution du test
        $response = $this->controller->read($dishId);

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
        $dishId = 999;

        // Configuration du mock
        $this->flagshipService
            ->expects($this->once())
            ->method('readFlagshipDish')
            ->with($dishId)
            ->willThrowException(new \Exception('Dish not found'));

        // Exécution du test
        $response = $this->controller->read($dishId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Dish not found'],
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
            'name' => 'Nouvelle Recette',
            'description' => 'Description de la nouvelle recette',
            'price' => 18.99,
            'category' => 'Desserts'
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
        $this->flagshipService
            ->expects($this->once())
            ->method('createFlagshipDish')
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
     * Test de la méthode update() - cas de succès
     */
    public function testUpdateSuccess(): void
    {
        // Données de test
        $requestData = [
            'name' => 'Plat Mis à Jour',
            'description' => 'Description mise à jour',
            'price' => 19.99,
            'category' => 'Plats Principaux'
        ];
        
        $request = new Request(
            [],
            [],
            ['id' => 1],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        // Configuration du mock
        $this->flagshipService
            ->expects($this->once())
            ->method('updateFlagshipDish')
            ->with($requestData, 1)
            ->willReturn(new JsonResponse(['status' => 'updated'], 201));

        // Exécution du test
        $response = $this->controller->update($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            ['status' => 'updated'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode update() - cas d'erreur
     */
    public function testUpdateError(): void
    {
        // Données de test
        $requestData = [
            'name' => 'Plat Mis à Jour'
        ];
        
        $request = new Request(
            [],
            [],
            ['id' => 1],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        // Configuration du mock pour simuler une erreur
        $this->flagshipService
            ->expects($this->once())
            ->method('updateFlagshipDish')
            ->with($requestData, 1)
            ->willThrowException(new \Exception('Update failed'));

        // Exécution du test
        $response = $this->controller->update($request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Update failed'],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test de la méthode delete() - cas de succès
     */
    public function testDeleteSuccess(): void
    {
        // Données de test
        $dishId = 1;

        // Configuration du mock
        $this->flagshipService
            ->expects($this->once())
            ->method('deleteFlagshipDish')
            ->with($dishId)
            ->willReturn(new JsonResponse(['status' => 'deleted']));

        // Exécution du test
        $response = $this->controller->delete($dishId);

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
        $dishId = 999;

        // Configuration du mock
        $this->flagshipService
            ->expects($this->once())
            ->method('deleteFlagshipDish')
            ->with($dishId)
            ->willThrowException(new \Exception('Delete failed'));

        // Exécution du test
        $response = $this->controller->delete($dishId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Delete failed'],
            json_decode($response->getContent(), true)
        );
    }
}