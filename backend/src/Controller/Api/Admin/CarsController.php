<?php

namespace App\Controller\Api\Admin;

use App\DTO\CarClass\CarClassRequestDTO;
use App\Repository\FeatureRepository;
use App\Service\CarsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/cars')]
class CarsController extends AbstractController
{
    public function __construct(
        private readonly CarsService $carsService,
        private readonly FeatureRepository $featureRepository,
    ) {
    }

    #[Route('/classes', methods: ['GET'])]
    public function classes(Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', true);

        return $this->json([
            'data' => $this->carsService->getAllClasses($withCarsCount),
        ]);
    }

    #[Route('/features', methods: ['GET'])]
    public function features(): JsonResponse
    {
        $features = $this->featureRepository->findAllOrderedByName();

        return $this->json([
            'data' => array_map(
                static fn ($feature) => [
                    'id' => $feature->getId(),
                    'name' => $feature->getName(),
                    'icon' => $feature->getIcon(),
                    'category' => $feature->getCategory(),
                    'categoryLabel' => $feature->getCategoryLabel(),
                    'categoryCode' => $feature->getCategoryCode(),
                ],
                $features
            ),
        ]);
    }

    #[Route('/search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query->get('q', ''));

        if ($query === '') {
            return $this->json([
                'data' => [],
            ]);
        }

        return $this->json([
            'data' => $this->carsService->searchClasses($query),
        ]);
    }

    #[Route('/available', methods: ['GET'])]
    public function available(): JsonResponse
    {
        return $this->json([
            'data' => $this->carsService->getClassesWithAvailableCars(),
        ]);
    }

    #[Route('/statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        return $this->json([
            'data' => $this->carsService->getClassStatistics(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new CarClassRequestDTO();
        $dto->name = $data['name'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->icon = $data['icon'] ?? null;
        $dto->dailyRate = isset($data['dailyRate'])
            ? (float) $data['dailyRate']
            : null;
        $dto->hourlyRate = isset($data['hourlyRate'])
            ? (float) $data['hourlyRate']
            : null;

        $class = $this->carsService->createClass($dto);

        return $this->json([
            'data' => $class,
        ], 201);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', false);

        return $this->json([
            'data' => $this->carsService->getClassById(
                $id,
                $withCarsCount
            ),
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(
        int $id,
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new CarClassRequestDTO();
        $dto->name = $data['name'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->icon = $data['icon'] ?? null;
        $dto->dailyRate = isset($data['dailyRate'])
            ? (float) $data['dailyRate']
            : null;
        $dto->hourlyRate = isset($data['hourlyRate'])
            ? (float) $data['hourlyRate']
            : null;

        return $this->json([
            'data' => $this->carsService->updateClass($id, $dto),
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->carsService->deleteClass($id);

        return $this->json([
            'success' => true,
        ]);
    }
}
