<?php

namespace App\DTO\Booking;

use App\Entity\BookingExtra;
use App\DTO\ExtraService\ExtraServiceResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class BookingExtraResponseDTO
{
    #[Groups(['booking:read', 'booking_extra:read'])]
    public int $id;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public int $bookingId;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public ExtraServiceResponseDTO $extraService;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public int $quantity;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public float $pricePerUnit;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public float $totalPrice;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public string $serviceName;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public ?string $serviceIcon;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public ?string $serviceCategory;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public string $createdAt;

    #[Groups(['booking:read', 'booking_extra:read'])]
    public string $updatedAt;

    public static function fromEntity(BookingExtra $bookingExtra): self
    {
        $dto = new self();
        $dto->id = $bookingExtra->getId();
        $dto->bookingId = $bookingExtra->getBooking() ? $bookingExtra->getBooking()->getId() : 0;
        $dto->extraService = ExtraServiceResponseDTO::fromEntity($bookingExtra->getExtraService());
        $dto->quantity = $bookingExtra->getQuantity();
        $dto->pricePerUnit = (float) $bookingExtra->getPricePerUnit();
        $dto->totalPrice = (float) $bookingExtra->getTotalPrice();
        $dto->serviceName = $bookingExtra->getServiceName();
        $dto->serviceIcon = $bookingExtra->getServiceIcon();
        $dto->serviceCategory = $bookingExtra->getServiceCategory();
        $dto->createdAt = $bookingExtra->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $bookingExtra->getUpdatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $bookingExtras): array
    {
        return array_map(
            fn(BookingExtra $bookingExtra) => self::fromEntity($bookingExtra),
            $bookingExtras
        );
    }
}
