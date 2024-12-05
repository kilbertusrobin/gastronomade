<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AvisService;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Entity\Avis;

#[Route('/api/avis', name: 'app_avis-')]
class AvisController extends AbstractController
{
    private $avisService;

    public function __construct(AvisService $avisService)
    {
        $this->avisService = $avisService;
    }

    #[OA\Get(
        path: '/api/avis',
        summary: 'Get all avis',
        tags: ['Avis'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the list of all avis',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Avis::class, groups: ['list_avis'])))
            )
        ]
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->avisService->getAvis();
    }

    #[OA\Get(
        path: '/api/avis/{id}',
        summary: 'Get an avis by ID',
        tags: ['Avis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the avis',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the avis details',
                content: new OA\JsonContent(ref: new Model(type: Avis::class, groups: ['detail_avis']))
            ),
            new OA\Response(
                response: 404,
                description: 'Avis not found'
            )
        ]
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->avisService->readAvis($id);
    }

    #[OA\Post(
        path: '/api/avis/create',
        summary: 'Create a new avis',
        tags: ['Avis'],
        requestBody: new OA\RequestBody(
            description: 'Avis data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Avis::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Avis created successfully'
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
        return $this->avisService->createAvis($data);
    }

    #[OA\Put(
        path: '/api/avis/update/{id}',
        summary: 'Update an avis',
        tags: ['Avis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the avis',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated avis data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Avis::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Avis updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Avis not found'
            )
        ]
    )]
    #[Route('/update/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->avisService->updateAvis($id, $data);
    }

    #[OA\Delete(
        path: '/api/avis/delete/{id}',
        summary: 'Delete an avis',
        tags: ['Avis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the avis',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Avis deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Avis not found'
            )
        ]
    )]
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->avisService->deleteAvis($id);
    }
}