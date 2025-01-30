<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\AvisController;
use App\Service\AvisService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AvisControllerTest extends TestCase
{
    private AvisController $controller;
    private $avisService;

    protected function setUp(): void
    {
        // Créer un mock du AvisService avant chaque test
        $this->avisService = $this->createMock(AvisService::class);
        $this->controller = new AvisController($this->avisService);
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
                'note' => 5,
                'commentaire' => 'Excellent service',
                'date' => '2024-01-30'
            ],
            [
                'id' => 2,
                'note' => 4,
                'commentaire' => 'Très bon repas',
                'date' => '2024-01-30'
            ]
        ];

        // Configuration du mock
        $this->avisService
            ->expects($this->once())
            ->method('getAvis')
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
        $this->avisService
            ->expects($this->once())
            ->method('getAvis')
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
        $avisId = 1;
        $expectedData = [
            'id' => 1,
            'note' => 5,
            'commentaire' => 'Excellent service',
            'date' => '2024-01-30'
        ];

        // Configuration du mock
        $this->avisService
            ->expects($this->once())
            ->method('readAvis')
            ->with($avisId)
            ->willReturn(new JsonResponse($expectedData));

        // Exécution du test
        $response = $this->controller->show($avisId);

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
        $avisId = 999;

        // Configuration du mock
        $this->avisService
            ->expects($this->once())
            ->method('readAvis')
            ->with($avisId)
            ->willThrowException(new \Exception('Avis not found'));

        // Exécution du test
        $response = $this->controller->show($avisId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Avis not found'],
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
            'note' => 5,
            'commentaire' => 'Excellent service',
            'date' => '2024-01-30'
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
        $this->avisService
            ->expects($this->once())
            ->method('createAvis')
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
        $avisId = 1;
        $requestData = [
            'note' => 4,
            'commentaire' => 'Mise à jour du commentaire',
            'date' => '2024-01-30'
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
        $this->avisService
            ->expects($this->once())
            ->method('updateAvis')
            ->with($avisId, $requestData)
            ->willReturn(new JsonResponse(['status' => 'updated']));

        // Exécution du test
        $response = $this->controller->update($avisId, $request);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
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
        $avisId = 1;
        $requestData = [
            'note' => 4,
            'commentaire' => 'Mise à jour du commentaire'
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

        // Configuration du mock pour simuler une erreur
        $this->avisService
            ->expects($this->once())
            ->method('updateAvis')
            ->with($avisId, $requestData)
            ->willThrowException(new \Exception('Update failed'));

        // Exécution du test
        $response = $this->controller->update($avisId, $request);

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
        $avisId = 1;

        // Configuration du mock
        $this->avisService
            ->expects($this->once())
            ->method('deleteAvis')
            ->with($avisId)
            ->willReturn(new JsonResponse(['status' => 'deleted']));

        // Exécution du test
        $response = $this->controller->delete($avisId);

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
        $avisId = 999;

        // Configuration du mock
        $this->avisService
            ->expects($this->once())
            ->method('deleteAvis')
            ->with($avisId)
            ->willThrowException(new \Exception('Delete failed'));

        // Exécution du test
        $response = $this->controller->delete($avisId);

        // Vérifications
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['error' => 'Delete failed'],
            json_decode($response->getContent(), true)
        );
    }
}