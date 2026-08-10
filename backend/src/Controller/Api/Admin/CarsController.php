<?php

namespace App\Controller\Api\Admin;

use App\DTO\CarClass\CarClassRequestDTO;
use App\DTO\ExtraService\ExtraServiceResponseDTO;
use App\Repository\FeatureRepository;
use App\Repository\ExtraServiceRepository;
use App\Service\CarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/cars')]
class CarsController extends AbstractController
{
    public function __construct(
        private readonly CarService $carService,
        private readonly FeatureRepository $featureRepository,
        private readonly ExtraServiceRepository $extraServiceRepository,
    ) {
    }

    #[Route('/classes', methods: ['GET'])]
    public function classes(Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', true);

        return $this->json([
            'data' => $this->carService->getAllClasses($withCarsCount),
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

    #[Route('/extraService', methods: ['GET'])]
    public function extraService(): JsonResponse
    {
        $extraServices = $this->extraServiceRepository->findAllOrdered();

        return $this->json([
            'data' => ExtraServiceResponseDTO::fromEntities(
                $extraServices,
                true
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
            'data' => $this->carService->searchClasses($query),
        ]);
    }

    #[Route('/available', methods: ['GET'])]
    public function available(): JsonResponse
    {
        return $this->json([
            'data' => $this->carService->getClassesWithAvailableCars(),
        ]);
    }

    #[Route('/statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        return $this->json([
            'data' => $this->carService->getClassStatistics(),
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

        $class = $this->carService->createClass($dto);

        return $this->json([
            'data' => $class,
        ], 201);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', false);

        return $this->json([
            'data' => $this->carService->getClassById(
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
            'data' => $this->carService->updateClass($id, $dto),
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->carService->deleteClass($id);

        return $this->json([
            'success' => true,
        ]);
    }
}
