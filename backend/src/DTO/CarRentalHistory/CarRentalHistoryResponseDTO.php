<?php

namespace App\DTO\CarRentalHistory;

use App\Entity\CarRentalHistory;
use App\DTO\Car\CarResponseDTO;
use App\DTO\Booking\BookingResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class CarRentalHistoryResponseDTO
{
    #[Groups(['car_rental_history:read', 'car:read'])]
    public int $id;

    #[Groups(['car_rental_history:read'])]
    public CarResponseDTO $car;

    #[Groups(['car_rental_history:read'])]
    public ?BookingResponseDTO $booking;

    #[Groups(['car_rental_history:read'])]
    public ?int $startMileage;

    #[Groups(['car_rental_history:read'])]
    public ?int $endMileage;

    #[Groups(['car_rental_history:read'])]
    public ?int $totalDistance;

    #[Groups(['car_rental_history:read'])]
    public ?string $mileageLabel;

    #[Groups(['car_rental_history:read'])]
    public string $startDate;

    #[Groups(['car_rental_history:read'])]
    public string $endDate;

    #[Groups(['car_rental_history:read'])]
    public ?int $totalDays;

    #[Groups(['car_rental_history:read'])]
    public ?int $totalHours;

    #[Groups(['car_rental_history:read'])]
    public ?string $durationLabel;

    #[Groups(['car_rental_history:read'])]
    public ?string $conditionBefore;

    #[Groups(['car_rental_history:read'])]
    public ?string $conditionBeforeLabel;

    #[Groups(['car_rental_history:read'])]
    public ?string $conditionAfter;

    #[Groups(['car_rental_history:read'])]
    public ?string $conditionAfterLabel;

    #[Groups(['car_rental_history:read'])]
    public ?string $conditionChange;

    #[Groups(['car_rental_history:read'])]
    public bool $isConditionWorse;

    #[Groups(['car_rental_history:read'])]
    public ?string $notes;

    #[Groups(['car_rental_history:read'])]
    public string $createdAt;

    #[Groups(['car_rental_history:read'])]
    public string $updatedAt;

    public static function fromEntity(CarRentalHistory $history): self
    {
        $dto = new self();
        $dto->id = $history->getId();
        $dto->car = CarResponseDTO::fromEntity($history->getCar());
        $dto->booking = $history->getBooking()
            ? BookingResponseDTO::fromEntity($history->getBooking(), false)
            : null;
        $dto->startMileage = $history->getStartMileage();
        $dto->endMileage = $history->getEndMileage();
        $dto->totalDistance = $history->getTotalDistance();
        $dto->mileageLabel = $history->getMileageLabel();
        $dto->startDate = $history->getStartDate()->format('Y-m-d H:i:s');
        $dto->endDate = $history->getEndDate()->format('Y-m-d H:i:s');
        $dto->totalDays = $history->getTotalDays();
        $dto->totalHours = $history->getTotalHours();
        $dto->durationLabel = $history->getDurationLabel();
        $dto->conditionBefore = $history->getConditionBefore();
        $dto->conditionBeforeLabel = $history->getConditionBeforeLabel();
        $dto->conditionAfter = $history->getConditionAfter();
        $dto->conditionAfterLabel = $history->getConditionAfterLabel();
        $dto->conditionChange = $history->getConditionChange();
        $dto->isConditionWorse = $history->isConditionWorse();
        $dto->notes = $history->getNotes();
        $dto->createdAt = $history->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $history->getUpdatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $histories): array
    {
        return array_map(
            fn(CarRentalHistory $history) => self::fromEntity($history),
            $histories
        );
    }
}
