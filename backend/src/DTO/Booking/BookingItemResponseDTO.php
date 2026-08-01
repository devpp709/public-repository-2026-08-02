<?php

namespace App\DTO\Booking;

use App\Entity\BookingItem;
use App\DTO\Car\CarResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class BookingItemResponseDTO
{
    #[Groups(['booking:read', 'booking_item:read'])]
    public int $id;

    #[Groups(['booking:read', 'booking_item:read'])]
    public int $bookingId;

    #[Groups(['booking:read', 'booking_item:read'])]
    public CarResponseDTO $car;

    #[Groups(['booking:read', 'booking_item:read'])]
    public float $dailyRate;

    #[Groups(['booking:read', 'booking_item:read'])]
    public ?float $hourlyRate;

    #[Groups(['booking:read', 'booking_item:read'])]
    public float $totalPrice;

    #[Groups(['booking:read', 'booking_item:read'])]
    public string $carName;

    #[Groups(['booking:read', 'booking_item:read'])]
    public ?string $carVin;

    #[Groups(['booking:read', 'booking_item:read'])]
    public ?string $carLicensePlate;

    #[Groups(['booking:read', 'booking_item:read'])]
    public bool $isHourlyRental;

    #[Groups(['booking:read', 'booking_item:read'])]
    public string $createdAt;

    #[Groups(['booking:read', 'booking_item:read'])]
    public string $updatedAt;

    public static function fromEntity(BookingItem $bookingItem): self
    {
        $dto = new self();
        $dto->id = $bookingItem->getId();
        $dto->bookingId = $bookingItem->getBooking() ? $bookingItem->getBooking()->getId() : 0;
        $dto->car = CarResponseDTO::fromEntity($bookingItem->getCar());
        $dto->dailyRate = (float) $bookingItem->getDailyRate();
        $dto->hourlyRate = $bookingItem->getHourlyRate() ? (float) $bookingItem->getHourlyRate() : null;
        $dto->totalPrice = (float) $bookingItem->getTotalPrice();
        $dto->carName = $bookingItem->getCarName();
        $dto->carVin = $bookingItem->getCarVin();
        $dto->carLicensePlate = $bookingItem->getCarLicensePlate();
        $dto->isHourlyRental = $bookingItem->isHourlyRental();
        $dto->createdAt = $bookingItem->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $bookingItem->getUpdatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $bookingItems): array
    {
        return array_map(
            fn(BookingItem $bookingItem) => self::fromEntity($bookingItem),
            $bookingItems
        );
    }
}
