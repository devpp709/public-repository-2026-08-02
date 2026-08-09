<?php

namespace App\DTO\Booking;

use App\Entity\Booking;

class LatestBookingDTO
{
    public function __construct(
        public readonly int $id,
        public readonly array $car,
        public readonly string $orderDate,
        public readonly int $days,
        public readonly string $status,
        public readonly string $totalPrice,
    ) {
    }

    public static function fromEntity(Booking $booking): self
    {
        $car = $booking->getCar();

        $image = null;

        foreach ($car->getImages() as $carImage) {
            if ($carImage->isMain()) {
                $image = $carImage->getImageUrl();
                break;
            }
        }

        return new self(
            id: $booking->getId(),
            car: [
                'id' => $car->getId(),
                'name' => $car->getBrand() . ' ' . $car->getModel(),
                'licensePlate' => $car->getLicensePlate(),
                'image' => $image,
            ],
            orderDate: $booking->getCreatedAt()->format('Y-m-d H:i:s'),
            days: $booking->getTotalDays() ?? 0,
            status: $booking->getStatus(),
            totalPrice: (string) $booking->getTotalPrice(),
        );
    }

    /**
     * @param Booking[] $bookings
     */
    public static function fromEntities(array $bookings): array
    {
        return array_map(
            fn(Booking $booking) => self::fromEntity($booking),
            $bookings
        );
    }
}
