<?php

namespace App\DTO\Car;

use Symfony\Component\Validator\Constraints as Assert;

class CarRequestDTO
{
    #[Assert\NotBlank(message: 'Марка автомобиля обязательна')]
    #[Assert\Length(max: 100)]
    public ?string $brand = null;

    #[Assert\NotBlank(message: 'Модель автомобиля обязательна')]
    #[Assert\Length(max: 100)]
    public ?string $model = null;

    #[Assert\NotBlank]
    #[Assert\Range(min: 1900, max: 2030)]
    public ?int $year = null;

    #[Assert\Length(max: 50)]
    public ?string $color = null;

    #[Assert\NotBlank(message: 'Госномер обязателен')]
    #[Assert\Length(max: 20)]
    public ?string $licensePlate = null;

    #[Assert\NotBlank(message: 'VIN-код обязателен')]
    #[Assert\Length(exactly: 17)]
    public ?string $vin = null;

    #[Assert\PositiveOrZero]
    public ?int $mileage = 0;

    #[Assert\Choice(choices: ['Petrol', 'Diesel', 'Electric', 'Hybrid', 'Gas'])]
    public ?string $fuelType = null;

    #[Assert\Choice(choices: ['Automatic', 'Manual', 'CVT', 'Robot'])]
    public ?string $transmission = null;

    #[Assert\Range(min: 1, max: 20)]
    public ?int $seats = 5;

    #[Assert\Range(min: 2, max: 6)]
    public ?int $doors = 4;

    #[Assert\PositiveOrZero]
    public ?int $bags = 3;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $dailyPrice = null;

    #[Assert\PositiveOrZero]
    public ?float $hourlyPrice = null;

    #[Assert\PositiveOrZero]
    public ?float $securityDeposit = 500.00;

    public ?bool $isAvailable = true;

    #[Assert\Choice(choices: ['available', 'rented', 'maintenance', 'reserved'])]
    public ?string $status = 'available';

    public ?string $description = null;

    public ?int $classId = null;
    public ?int $locationId = null;
}
