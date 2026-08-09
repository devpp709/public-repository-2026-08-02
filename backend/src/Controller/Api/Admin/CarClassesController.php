<?php

namespace App\Controller\Api\Admin;

use App\DTO\CarClass\CarClassRequestDTO;
use App\Service\CarClassService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/car-classes')]
class CarClassesController extends AbstractController
{
    public function __construct(
        private readonly CarClassService $carClassService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', true);

        return $this->json([
            'data' => $this->carClassService->getAllClasses($withCarsCount),
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', false);

        return $this->json([
            'data' => $this->carClassService->getClassById(
                $id,
                $withCarsCount
            ),
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

        $class = $this->carClassService->createClass($dto);

        return $this->json([
            'data' => $class,
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
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
            'data' => $this->carClassService->updateClass($id, $dto),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->carClassService->deleteClass($id);

        return $this->json([
            'success' => true,
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
            'data' => $this->carClassService->searchClasses($query),
        ]);
    }

    #[Route('/available', methods: ['GET'])]
    public function available(): JsonResponse
    {
        return $this->json([
            'data' => $this->carClassService->getClassesWithAvailableCars(),
        ]);
    }

    #[Route('/statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        return $this->json([
            'data' => $this->carClassService->getClassStatistics(),
        ]);
    }
}
