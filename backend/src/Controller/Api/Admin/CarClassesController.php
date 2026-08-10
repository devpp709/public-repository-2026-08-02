<?php

namespace App\Controller\Api\Admin;

use App\DTO\CarClass\CarClassRequestDTO;
use App\Service\CarClassService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/car-classes')]
class CarClassesController extends AbstractController
{
    public function __construct(
        private readonly CarClassService $carClassService,
    ) {
    }

    /**
     * Получить все классы
     */
    #[Route('', methods: ['GET'])]
    public function classes(Request $request): JsonResponse
    {
        $withCarsCount = $request->query->getBoolean('withCarsCount', true);

        return $this->json([
            'data' => $this->carClassService->getAllClasses($withCarsCount),
        ]);
    }

    /**
     * Получить класс по ID
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $class = $this->carClassService->getClassById($id);

            return $this->json([
                'success' => true,
                'data' => $class
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Создать новый класс
     */
    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] CarClassRequestDTO $dto): JsonResponse
    {
        try {
            // Проверяем, существует ли класс с таким именем
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
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Обновить класс
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload] CarClassRequestDTO $dto): JsonResponse
    {
        try {
            // Проверяем, существует ли класс с таким именем (исключая текущий)
            if ($this->carClassService->isNameExists($dto->name, $id)) {
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
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Удалить класс
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->carClassService->deleteClass($id);

            return $this->json([
                'success' => true,
                'message' => 'Класс успешно удален'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Поиск классов
     */
    #[Route('/search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $classes = $this->carClassService->searchClasses($query);

        return $this->json([
            'data' => $classes
        ]);
    }

    /**
     * Получить классы с доступными автомобилями
     */
    #[Route('/available', methods: ['GET'])]
    public function getAvailable(): JsonResponse
    {
        $classes = $this->carClassService->getClassesWithAvailableCars();

        return $this->json([
            'data' => $classes
        ]);
    }

    /**
     * Получить статистику по классам
     */
    #[Route('/statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->carClassService->getClassStatistics();

        return $this->json([
            'data' => $statistics
        ]);
    }
}
