<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\ApiController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiControllerTest extends TestCase
{
    private ApiController $controller;
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->controller = new ApiController();
        $this->controller->setContainer($this->container);
    }

    public function testIndexReturnsSuccessResponse(): void
    {
        $response = $this->controller->index();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            ['message' => 'Api fonctionnelle'],
            json_decode($response->getContent(), true)
        );
    }
}