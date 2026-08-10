<?php

namespace App\Controller\Api\Admin;

use App\Repository\ExtraServiceRepository;
use App\Repository\FeatureRepository;
use App\Service\CarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/car-classes')]
class CarClassesController extends AbstractController
{
    public function __construct(
        private readonly CarService $carService,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function classes(Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', true);

        return $this->json([
            'data' => $this->carService->getAllClasses($withCarsCount),
        ]);
    }

}
