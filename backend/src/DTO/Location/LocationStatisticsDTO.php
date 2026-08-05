<?php

namespace App\DTO\Location;

class LocationStatisticsDTO
{
    public int $id;
    public string $name;
    public ?string $city;
    public int $totalCars;
    public int $availableCars;
    public int $pickupBookings;
    public int $dropoffBookings;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'];
        $dto->name = $data['name'];
        $dto->city = $data['city'];
        $dto->totalCars = (int) $data['total_cars'];
        $dto->availableCars = (int) $data['available_cars'];
        $dto->pickupBookings = (int) $data['pickup_bookings'];
        $dto->dropoffBookings = (int) $data['dropoff_bookings'];

        return $dto;
    }

    public static function fromArrayCollection(array $data): array
    {
        return array_map([self::class, 'fromArray'], $data);
    }
}
