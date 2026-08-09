<?php

namespace App\DTO\Booking;

use App\Entity\Booking;

class BookingListItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $bookingNumber,
        public readonly string $status,

        public readonly string $pickupDate,
        public readonly string $pickupTime,
        public readonly string $dropoffDate,
        public readonly string $dropoffTime,

        public readonly int $totalDays,
        public readonly string $totalPrice,

        public readonly array $car,
        public readonly array $user,
        public readonly array $pickupLocation,
        public readonly array $dropoffLocation,
        public readonly array $extras,

        public readonly string $createdAt,
    ) {
    }

    public static function fromEntity(Booking $booking): self
    {
        $car = $booking->getCar();

        $extras = [];

        foreach ($booking->getBookingExtras() as $bookingExtra) {
            $extraService = $bookingExtra->getExtraService();

            $extras[] = [
                'id' => $bookingExtra->getId(),
                'quantity' => $bookingExtra->getQuantity(),
                'pricePerUnit' => $bookingExtra->getPricePerUnit(),
                'totalPrice' => $bookingExtra->getTotalPrice(),

                'service' => [
                    'id' => $extraService->getId(),
                    'name' => $extraService->getName(),
                    'description' => $extraService->getDescription(),
                    'icon' => $extraService->getIcon(),
                    'category' => $extraService->getCategory(),
                ],
            ];
        }

        return new self(
            id: $booking->getId(),
            bookingNumber: $booking->getBookingNumber(),
            status: $booking->getStatus(),

            pickupDate: $booking->getPickupDate()->format('Y-m-d'),
            pickupTime: $booking->getPickupTime()->format('H:i'),
            dropoffDate: $booking->getDropoffDate()->format('Y-m-d'),
            dropoffTime: $booking->getDropoffTime()->format('H:i'),

            totalDays: $booking->getTotalDays() ?? 0,
            totalPrice: $booking->getTotalPrice(),

            car: [
                'id' => $car->getId(),
                'brand' => $car->getBrand(),
                'model' => $car->getModel(),
                'name' => $car->getFullName(),
                'licensePlate' => $car->getLicensePlate(),
                'year' => $car->getYear(),
            ],

            user: [
                'id' => $booking->getUser()->getId(),
                'name' => $booking->getUser()->getFullName(),
            ],

            pickupLocation: [
                'id' => $booking->getPickupLocation()->getId(),
                'name' => $booking->getPickupLocation()->getName(),
                'address' => $booking->getPickupLocation()->getAddress(),
                'city' => $booking->getPickupLocation()->getCity(),
            ],

            dropoffLocation: [
                'id' => $booking->getDropoffLocation()->getId(),
                'name' => $booking->getDropoffLocation()->getName(),
                'address' => $booking->getDropoffLocation()->getAddress(),
                'city' => $booking->getDropoffLocation()->getCity(),
            ],

            extras: $extras,

            createdAt: $booking->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @param Booking[] $bookings
     */
    public static function fromEntities(array $bookings): array
    {
        return array_map(
            fn (Booking $booking) => self::fromEntity($booking),
            $bookings
        );
    }
}
