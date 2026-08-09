<?php

namespace App\DTO\Booking;

use App\DTO\Car\CarResponseDTO;
use App\DTO\Location\LocationResponseDTO;
use App\DTO\User\UserResponseDTO;
use App\Entity\Booking;
use Symfony\Component\Serializer\Annotation\Groups;

class BookingResponseDTO
{
    #[Groups(['booking:read'])]
    public int $id;

    #[Groups(['booking:read'])]
    public UserResponseDTO $user;

    #[Groups(['booking:read'])]
    public string $bookingNumber;

    #[Groups(['booking:read'])]
    public ?LocationResponseDTO $pickupLocation;

    #[Groups(['booking:read'])]
    public ?LocationResponseDTO $dropoffLocation;

    #[Groups(['booking:read'])]
    public string $pickupDate;

    #[Groups(['booking:read'])]
    public string $pickupTime;

    #[Groups(['booking:read'])]
    public string $dropoffDate;

    #[Groups(['booking:read'])]
    public string $dropoffTime;

    #[Groups(['booking:read'])]
    public ?int $totalDays;

    #[Groups(['booking:read'])]
    public ?int $totalHours;

    #[Groups(['booking:read'])]
    public float $subtotal;

    #[Groups(['booking:read'])]
    public float $extrasTotal;

    #[Groups(['booking:read'])]
    public float $totalAmount;

    #[Groups(['booking:read'])]
    public float $securityDeposit;

    #[Groups(['booking:read'])]
    public string $status;

    #[Groups(['booking:read'])]
    public string $statusLabel;

    #[Groups(['booking:read'])]
    public ?string $notes;

    #[Groups(['booking:read'])]
    public string $createdAt;

    #[Groups(['booking:read'])]
    public string $updatedAt;

    #[Groups(['booking:read'])]
    public ?CarResponseDTO $car = null;

    #[Groups(['booking:read'])]
    public array $extras = [];

    #[Groups(['booking:read'])]
    public array $payments = [];

    #[Groups(['booking:read'])]
    public ?string $dailyRate = null;

    #[Groups(['booking:read'])]
    public ?string $hourlyRate = null;

    #[Groups(['booking:read'])]
    public ?string $totalPrice = null;

    #[Groups(['booking:read'])]
    public ?string $duration = null;

    public static function fromEntity(Booking $booking, bool $withDetails = false): self
    {
        $dto = new self();

        $dto->id = $booking->getId();
        $dto->user = UserResponseDTO::fromEntity($booking->getUser());
        $dto->bookingNumber = $booking->getBookingNumber();

        $dto->pickupLocation = $booking->getPickupLocation()
            ? LocationResponseDTO::fromEntity($booking->getPickupLocation())
            : null;

        $dto->dropoffLocation = $booking->getDropoffLocation()
            ? LocationResponseDTO::fromEntity($booking->getDropoffLocation())
            : null;

        $dto->pickupDate = $booking->getPickupDate()->format('Y-m-d');
        $dto->pickupTime = $booking->getPickupTime()->format('H:i:s');
        $dto->dropoffDate = $booking->getDropoffDate()->format('Y-m-d');
        $dto->dropoffTime = $booking->getDropoffTime()->format('H:i:s');

        $dto->totalDays = $booking->getTotalDays();
        $dto->totalHours = $booking->getTotalHours();

        $dto->subtotal = (float) $booking->getSubtotal();
        $dto->extrasTotal = (float) $booking->getExtrasTotal();
        $dto->totalAmount = (float) $booking->getTotalAmount();
        $dto->securityDeposit = (float) $booking->getSecurityDeposit();

        $dto->status = $booking->getStatus();
        $dto->statusLabel = $booking->getStatusLabel();
        $dto->notes = $booking->getNotes();

        $dto->createdAt = $booking->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $booking->getUpdatedAt()->format('Y-m-d H:i:s');

        $dto->car = $booking->getCar()
            ? CarResponseDTO::fromEntity($booking->getCar())
            : null;

        $dto->dailyRate = $booking->getDailyRate();
        $dto->hourlyRate = $booking->getHourlyRate();
        $dto->totalPrice = $booking->getTotalPrice();

        $durationHours = $booking->getDurationInHours();

        if ($durationHours > 0) {
            $days = floor($durationHours / 24);
            $hours = $durationHours % 24;

            $dto->duration = $days > 0
                ? sprintf('%d дн. %d ч.', $days, $hours)
                : sprintf('%d ч.', $hours);
        }

        if ($withDetails) {
            $dto->extras = array_map(
                fn($extra) => BookingExtraResponseDTO::fromEntity($extra),
                $booking->getBookingExtras()->toArray()
            );

            $dto->payments = array_map(
                fn($payment) => PaymentResponseDTO::fromEntity($payment),
                $booking->getPayments()->toArray()
            );
        }

        return $dto;
    }

    public static function fromEntities(array $bookings, bool $withDetails = false): array
    {
        return array_map(
            fn(Booking $booking) => self::fromEntity($booking, $withDetails),
            $bookings
        );
    }
}
