<?php

namespace App\Controller\Api\V1;

use App\DTO\CarClass\CarClassRequestDTO;
use App\Service\CarsService;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/car-classes')]
class CarClassController extends AbstractController
{
    public function __construct(
        private readonly CarsService         $carClassService,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Получить все классы автомобилей
     */
    #[Route('', name: 'api_car_classes_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('with_cars_count', false);
        $search = $request->query->get('search');

        if ($search) {
            $classes = $this->carClassService->searchClasses($search);
        } else {
            $classes = $this->carClassService->getAllClasses($withCarsCount);
        }

        return $this->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Получить классы с доступными автомобилями
     */
    #[Route('/available', name: 'api_car_classes_available', methods: ['GET'])]
    public function getAvailable(): JsonResponse
    {
        $classes = $this->carClassService->getClassesWithAvailableCars();

        return $this->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Получить статистику по классам
     */
    #[Route('/statistics', name: 'api_car_classes_statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        $statistics = $this->carClassService->getClassStatistics();

        return $this->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Получить класс по ID
     */
    #[Route('/{id}', name: 'api_car_classes_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('with_cars_count', true);
        $class = $this->carClassService->getClassById($id, $withCarsCount);

        return $this->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * Создать новый класс
     */
    #[Route('', name: 'api_car_classes_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CarClassRequestDTO $dto): JsonResponse
    {
        // Проверяем уникальность имени
        if ($this->carClassService->isNameExists($dto->name)) {
            return $this->json([
                'success' => false,
                'message' => 'Класс с таким названием уже существует'
            ], Response::HTTP_CONFLICT);
        }

        $class = $this->carClassService->createClass($dto);

        return $this->json([
            'success' => true,
            'message' => 'Класс успешно создан',
            'data' => $class
        ], Response::HTTP_CREATED);
    }

    /**
     * Обновить класс
     */
    #[Route('/{id}', name: 'api_car_classes_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] CarClassRequestDTO $dto): JsonResponse
    {
        // Проверяем уникальность имени (исключая текущий класс)
        if ($dto->name && $this->carClassService->isNameExists($dto->name, $id)) {
            return $this->json([
                'success' => false,
                'message' => 'Класс с таким названием уже существует'
            ], Response::HTTP_CONFLICT);
        }

        $class = $this->carClassService->updateClass($id, $dto);

        return $this->json([
            'success' => true,
            'message' => 'Класс успешно обновлен',
            'data' => $class
        ]);
    }

    /**
     * Удалить класс
     */
    #[Route('/{id}', name: 'api_car_classes_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->carClassService->deleteClass($id);

            return $this->json([
                'success' => true,
                'message' => 'Класс успешно удален'
            ]);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Проверить существование класса
     */
    #[Route('/check-name', name: 'api_car_classes_check_name', methods: ['GET'])]
    public function checkName(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $excludeId = $request->query->getInt('exclude_id', null);

        if (!$name) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр "name" обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        $exists = $this->carClassService->isNameExists($name, $excludeId);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }
}
