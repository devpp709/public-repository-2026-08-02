<?php

namespace App\Service;

use App\DTO\CarImage\CarImageRequestDTO;
use App\DTO\CarImage\CarImageResponseDTO;
use App\Entity\Car;
use App\Entity\CarImage;
use App\Repository\CarImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarImageService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CarImageRepository $carImageRepository
    ) {
    }

    /**
     * Получить все изображения автомобиля
     */
    public function getImagesByCarId(int $carId): array
    {
        $images = $this->carImageRepository->findByCarId($carId);
        return CarImageResponseDTO::fromEntities($images);
    }

    /**
     * Получить изображение по ID
     */
    public function getImageById(int $id): CarImageResponseDTO
    {
        $image = $this->findImageOrFail($id);
        return CarImageResponseDTO::fromEntity($image);
    }

    /**
     * Получить главное изображение автомобиля
     */
    public function getMainImageByCarId(int $carId): ?CarImageResponseDTO
    {
        $image = $this->carImageRepository->findMainImageByCarId($carId);
        return $image ? CarImageResponseDTO::fromEntity($image) : null;
    }

    /**
     * Добавить изображение автомобилю
     */
    public function addImage(int $carId, CarImageRequestDTO $dto): CarImageResponseDTO
    {
        $car = $this->findCarOrFail($carId);

        // Если изображение помечено как главное, сбрасываем флаг у остальных
        if ($dto->isMain) {
            $this->carImageRepository->resetMainFlag($carId);
        }

        // Если sortOrder не указан, ставим следующий по порядку
        if ($dto->sortOrder === null) {
            $maxSortOrder = $this->carImageRepository->getMaxSortOrder($carId);
            $dto->sortOrder = $maxSortOrder + 1;
        }

        $image = new CarImage();
        $image->setCar($car);
        $image->setImageUrl($dto->imageUrl);
        $image->setIsMain($dto->isMain ?? false);
        $image->setSortOrder($dto->sortOrder ?? 0);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return CarImageResponseDTO::fromEntity($image);
    }

    /**
     * Обновить изображение
     */
    public function updateImage(int $id, CarImageRequestDTO $dto): CarImageResponseDTO
    {
        $image = $this->findImageOrFail($id);

        // Если изображение становится главным, сбрасываем флаг у остальных
        if ($dto->isMain && !$image->isMain()) {
            $this->carImageRepository->resetMainFlag($image->getCar()->getId());
        }

        if ($dto->imageUrl !== null) {
            $image->setImageUrl($dto->imageUrl);
        }
        if ($dto->isMain !== null) {
            $image->setIsMain($dto->isMain);
        }
        if ($dto->sortOrder !== null) {
            $image->setSortOrder($dto->sortOrder);
        }

        $this->entityManager->flush();

        return CarImageResponseDTO::fromEntity($image);
    }

    /**
     * Удалить изображение
     */
    public function deleteImage(int $id): void
    {
        $image = $this->findImageOrFail($id);
        $carId = $image->getCar()->getId();
        $wasMain = $image->isMain();

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        // Если удалили главное изображение, назначаем новое главное
        if ($wasMain) {
            $images = $this->carImageRepository->findByCarId($carId);
            if (!empty($images)) {
                $firstImage = $images[0];
                $firstImage->setIsMain(true);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * Удалить все изображения автомобиля
     */
    public function deleteImagesByCarId(int $carId): void
    {
        $this->carImageRepository->deleteByCarId($carId);
    }

    /**
     * Переупорядочить изображения автомобиля
     */
    public function reorderImages(int $carId, array $imageIds): array
    {
        $car = $this->findCarOrFail($carId);
        $images = $this->carImageRepository->findByCarId($carId);

        if (count($images) !== count($imageIds)) {
            throw new \InvalidArgumentException('Количество изображений не совпадает');
        }

        foreach ($images as $image) {
            $index = array_search($image->getId(), $imageIds);
            if ($index !== false) {
                $image->setSortOrder($index + 1);
            }
        }

        $this->entityManager->flush();

        return $this->getImagesByCarId($carId);
    }

    /**
     * Установить главное изображение
     */
    public function setMainImage(int $carId, int $imageId): CarImageResponseDTO
    {
        $car = $this->findCarOrFail($carId);
        $image = $this->findImageOrFail($imageId);

        if ($image->getCar()->getId() !== $carId) {
            throw new \InvalidArgumentException('Изображение не принадлежит этому автомобилю');
        }

        // Сбрасываем флаг у всех изображений
        $this->carImageRepository->resetMainFlag($carId);

        // Устанавливаем флаг у выбранного изображения
        $image->setIsMain(true);
        $this->entityManager->flush();

        return CarImageResponseDTO::fromEntity($image);
    }

    /**
     * Найти изображение или выбросить исключение
     */
    private function findImageOrFail(int $id): CarImage
    {
        $image = $this->carImageRepository->find($id);
        if (!$image) {
            throw new NotFoundHttpException(sprintf('Изображение с ID %d не найдено', $id));
        }

        return $image;
    }

    /**
     * Найти автомобиль или выбросить исключение
     */
    private function findCarOrFail(int $id): Car
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);
        if (!$car) {
            throw new NotFoundHttpException(sprintf('Автомобиль с ID %d не найден', $id));
        }

        return $car;
    }
}
