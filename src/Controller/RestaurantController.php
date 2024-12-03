<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RestaurantService;

#[Route('/api/restaurant', name: 'app_restaurant-')]
class RestaurantController extends AbstractController
{
    private $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    #[Route('/list', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->restaurantService->getRestaurants();
    }
}