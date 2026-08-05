<?php

namespace App\Controller\Api\V1;

use App\DTO\CarImage\CarImageRequestDTO;
use App\Service\CarImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/cars/{carId}/images')]
class CarImageController extends AbstractController
{
    public function __construct(
        private readonly CarImageService $carImageService
    ) {
    }

    /**
     * Получить все изображения автомобиля
     */
    #[Route('', name: 'api_car_images_list', methods: ['GET'])]
    public function list(int $carId): JsonResponse
    {
        $images = $this->carImageService->getImagesByCarId($carId);

        return $this->json([
            'success' => true,
            'data' => $images
        ]);
    }

    /**
     * Получить главное изображение автомобиля
     */
    #[Route('/main', name: 'api_car_images_main', methods: ['GET'])]
    public function getMain(int $carId): JsonResponse
    {
        $image = $this->carImageService->getMainImageByCarId($carId);

        return $this->json([
            'success' => true,
            'data' => $image
        ]);
    }

    /**
     * Добавить изображение автомобилю
     */
    #[Route('', name: 'api_car_images_create', methods: ['POST'])]
    public function create(int $carId, #[MapRequestPayload] CarImageRequestDTO $dto): JsonResponse
    {
        try {
            $image = $this->carImageService->addImage($carId, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Изображение успешно добавлено',
                'data' => $image
            ], Response::HTTP_CREATED);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Получить изображение по ID
     */
    #[Route('/{id}', name: 'api_car_images_show', methods: ['GET'])]
    public function show(int $carId, int $id): JsonResponse
    {
        try {
            $image = $this->carImageService->getImageById($id);

            // Проверяем, что изображение принадлежит автомобилю
            if ($image->carId !== $carId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Изображение не принадлежит этому автомобилю'
                ], Response::HTTP_FORBIDDEN);
            }

            return $this->json([
                'success' => true,
                'data' => $image
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить изображение
     */
    #[Route('/{id}', name: 'api_car_images_update', methods: ['PUT', 'PATCH'])]
    public function update(
        int $carId,
        int $id,
        #[MapRequestPayload] CarImageRequestDTO $dto
    ): JsonResponse {
        try {
            $image = $this->carImageService->updateImage($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Изображение успешно обновлено',
                'data' => $image
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Установить главное изображение
     */
    #[Route('/{id}/set-main', name: 'api_car_images_set_main', methods: ['PATCH'])]
    public function setMain(int $carId, int $id): JsonResponse
    {
        try {
            $image = $this->carImageService->setMainImage($carId, $id);

            return $this->json([
                'success' => true,
                'message' => 'Главное изображение обновлено',
                'data' => $image
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Переупорядочить изображения
     */
    #[Route('/reorder', name: 'api_car_images_reorder', methods: ['PATCH'])]
    public function reorder(int $carId, Request $request): JsonResponse
    {
        $imageIds = $request->request->all('image_ids');

        if (!$imageIds) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр image_ids обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $images = $this->carImageService->reorderImages($carId, $imageIds);

            return $this->json([
                'success' => true,
                'message' => 'Порядок изображений обновлен',
                'data' => $images
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Удалить изображение
     */
    #[Route('/{id}', name: 'api_car_images_delete', methods: ['DELETE'])]
    public function delete(int $carId, int $id): JsonResponse
    {
        try {
            $this->carImageService->deleteImage($id);

            return $this->json([
                'success' => true,
                'message' => 'Изображение успешно удалено'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить все изображения автомобиля
     */
    #[Route('', name: 'api_car_images_delete_all', methods: ['DELETE'])]
    public function deleteAll(int $carId): JsonResponse
    {
        $this->carImageService->deleteImagesByCarId($carId);

        return $this->json([
            'success' => true,
            'message' => 'Все изображения автомобиля удалены'
        ]);
    }
}
