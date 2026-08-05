<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\Table(name: 'cars')]
#[ORM\HasLifecycleCallbacks]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read', 'booking:read', 'car_rental_history:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CarClass::class, inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarClass $carClass = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Марка автомобиля обязательна')]
    #[Assert\Length(max: 100)]
    #[Groups(['car:read', 'car:write', 'booking:read'])]
    private ?string $brand = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Модель автомобиля обязательна')]
    #[Assert\Length(max: 100)]
    #[Groups(['car:read', 'car:write', 'booking:read'])]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(notInRangeMessage: 'Год выпуска должен быть между 1900 и 2030', min: 1900, max: 2030)]
    #[Groups(['car:read', 'car:write'])]
    private ?int $year = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['car:read', 'car:write'])]
    private ?string $color = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank(message: 'Госномер обязателен')]
    #[Assert\Length(max: 20)]
    #[Groups(['car:read', 'car:write'])]
    private ?string $licensePlate = null;

    #[ORM\Column(length: 17, unique: true)]
    #[Assert\NotBlank(message: 'VIN-код обязателен')]
    #[Assert\Length(exactly: 17, exactMessage: 'VIN-код должен содержать ровно 17 символов')]
    #[Groups(['car:read', 'car:write'])]
    private ?string $vin = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\PositiveOrZero]
    #[Groups(['car:read', 'car:write'])]
    private ?int $mileage = 0;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(choices: ['Petrol', 'Diesel', 'Electric', 'Hybrid', 'Gas'], message: 'Выберите корректный тип топлива')]
    #[Groups(['car:read', 'car:write'])]
    private ?string $fuelType = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(choices: ['Automatic', 'Manual', 'CVT', 'Robot'], message: 'Выберите корректную коробку передач')]
    #[Groups(['car:read', 'car:write'])]
    private ?string $transmission = null;

    #[ORM\Column(options: ['default' => 5])]
    #[Assert\Range(min: 1, max: 20)]
    #[Groups(['car:read', 'car:write'])]
    private ?int $seats = 5;

    #[ORM\Column(options: ['default' => 4])]
    #[Assert\Range(min: 2, max: 6)]
    #[Groups(['car:read', 'car:write'])]
    private ?int $doors = 4;

    #[ORM\Column(options: ['default' => 3])]
    #[Assert\PositiveOrZero]
    #[Groups(['car:read', 'car:write'])]
    private ?int $bags = 3;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'Цена должна быть положительной')]
    #[Groups(['car:read', 'car:write', 'booking:read'])]
    private ?string $dailyPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['car:read', 'car:write'])]
    private ?string $hourlyPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '500.00'])]
    #[Assert\PositiveOrZero]
    #[Groups(['car:read', 'car:write'])]
    private ?string $securityDeposit = '500.00';

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['car:read', 'car:write'])]
    private ?bool $isAvailable = true;

    #[ORM\Column(length: 20, options: ['default' => 'available'])]
    #[Assert\Choice(choices: ['available', 'rented', 'maintenance', 'reserved', 'deleted'])]
    #[Groups(['car:read', 'car:write'])]
    private ?string $status = 'available';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['car:read', 'car:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['car:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['car:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, CarImage>
     */
    #[ORM\OneToMany(targetEntity: CarImage::class, mappedBy: 'car', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['car:read'])]
    private Collection $images;

    /**
     * @var Collection<int, CarFeature>
     */
    #[ORM\OneToMany(targetEntity: CarFeature::class, mappedBy: 'car', cascade: ['persist', 'remove'])]
    private Collection $carFeatures;

    /**
     * @var Collection<int, CarExtraService>
     */
    #[ORM\OneToMany(targetEntity: CarExtraService::class, mappedBy: 'car', cascade: ['persist', 'remove'])]
    private Collection $carExtraServices;

    /**
     * @var Collection<int, BookingItem>
     */
    #[ORM\OneToMany(targetEntity: BookingItem::class, mappedBy: 'car')]
    private Collection $bookingItems;

    /**
     * @var Collection<int, CarRentalHistory>
     */
    #[ORM\OneToMany(targetEntity: CarRentalHistory::class, mappedBy: 'car')]
    private Collection $rentalHistories;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'car')]
    private Collection $reviews;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->carFeatures = new ArrayCollection();
        $this->carExtraServices = new ArrayCollection();
        $this->bookingItems = new ArrayCollection();
        $this->rentalHistories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isAvailable = true;
        $this->status = 'available';
        $this->seats = 5;
        $this->doors = 4;
        $this->bags = 3;
        $this->securityDeposit = '500.00';
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarClass(): ?CarClass
    {
        return $this->carClass;
    }

    public function setCarClass(?CarClass $carClass): static
    {
        $this->carClass = $carClass;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): static
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): static
    {
        $this->vin = $vin;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): static
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(?string $fuelType): static
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(?string $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): static
    {
        $this->seats = $seats;

        return $this;
    }

    public function getDoors(): ?int
    {
        return $this->doors;
    }

    public function setDoors(int $doors): static
    {
        $this->doors = $doors;

        return $this;
    }

    public function getBags(): ?int
    {
        return $this->bags;
    }

    public function setBags(int $bags): static
    {
        $this->bags = $bags;

        return $this;
    }

    public function getDailyPrice(): ?string
    {
        return $this->dailyPrice;
    }

    public function setDailyPrice(string $dailyPrice): static
    {
        $this->dailyPrice = $dailyPrice;

        return $this;
    }

    public function getHourlyPrice(): ?string
    {
        return $this->hourlyPrice;
    }

    public function setHourlyPrice(?string $hourlyPrice): static
    {
        $this->hourlyPrice = $hourlyPrice;

        return $this;
    }

    public function getSecurityDeposit(): ?string
    {
        return $this->securityDeposit;
    }

    public function setSecurityDeposit(?string $securityDeposit): static
    {
        $this->securityDeposit = $securityDeposit;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, CarImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(CarImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setCar($this);
        }

        return $this;
    }

    public function removeImage(CarImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
        }

        return $this;
    }

    public function getMainImage(): ?CarImage
    {
        foreach ($this->images as $image) {
            if ($image->isMain()) {
                return $image;
            }
        }

        return $this->images->first() ?: null;
    }

    /**
     * @return Collection<int, CarFeature>
     */
    public function getCarFeatures(): Collection
    {
        return $this->carFeatures;
    }

    public function addCarFeature(CarFeature $carFeature): static
    {
        if (!$this->carFeatures->contains($carFeature)) {
            $this->carFeatures->add($carFeature);
            $carFeature->setCar($this);
        }

        return $this;
    }

    public function removeCarFeature(CarFeature $carFeature): static
    {
        if ($this->carFeatures->removeElement($carFeature)) {
            if ($carFeature->getCar() === $this) {
                $carFeature->setCar(null);
            }
        }

        return $this;
    }

    public function getFeatures(): Collection
    {
        $features = new ArrayCollection();
        foreach ($this->carFeatures as $carFeature) {
            $features->add($carFeature->getFeature());
        }
        return $features;
    }

    /**
     * @return Collection<int, CarExtraService>
     */
    public function getCarExtraServices(): Collection
    {
        return $this->carExtraServices;
    }

    public function addCarExtraService(CarExtraService $carExtraService): static
    {
        if (!$this->carExtraServices->contains($carExtraService)) {
            $this->carExtraServices->add($carExtraService);
            $carExtraService->setCar($this);
        }

        return $this;
    }

    public function removeCarExtraService(CarExtraService $carExtraService): static
    {
        if ($this->carExtraServices->removeElement($carExtraService)) {
            if ($carExtraService->getCar() === $this) {
                $carExtraService->setCar(null);
            }
        }

        return $this;
    }

    public function getExtraServices(): Collection
    {
        $services = new ArrayCollection();
        foreach ($this->carExtraServices as $carExtraService) {
            $services->add($carExtraService->getExtraService());
        }
        return $services;
    }

    /**
     * @return Collection<int, BookingItem>
     */
    public function getBookingItems(): Collection
    {
        return $this->bookingItems;
    }

    public function addBookingItem(BookingItem $bookingItem): static
    {
        if (!$this->bookingItems->contains($bookingItem)) {
            $this->bookingItems->add($bookingItem);
            $bookingItem->setCar($this);
        }

        return $this;
    }

    public function removeBookingItem(BookingItem $bookingItem): static
    {
        if ($this->bookingItems->removeElement($bookingItem)) {
            if ($bookingItem->getCar() === $this) {
                $bookingItem->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CarRentalHistory>
     */
    public function getRentalHistories(): Collection
    {
        return $this->rentalHistories;
    }

    public function addRentalHistory(CarRentalHistory $rentalHistory): static
    {
        if (!$this->rentalHistories->contains($rentalHistory)) {
            $this->rentalHistories->add($rentalHistory);
            $rentalHistory->setCar($this);
        }

        return $this;
    }

    public function removeRentalHistory(CarRentalHistory $rentalHistory): static
    {
        if ($this->rentalHistories->removeElement($rentalHistory)) {
            if ($rentalHistory->getCar() === $this) {
                $rentalHistory->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCar($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getCar() === $this) {
                $review->setCar(null);
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return $this->brand . ' ' . $this->model;
    }

    public function getFuelTypeLabel(): string
    {
        return match($this->fuelType) {
            'Petrol' => 'Бензин',
            'Diesel' => 'Дизель',
            'Electric' => 'Электрический',
            'Hybrid' => 'Гибрид',
            'Gas' => 'Газ',
            default => $this->fuelType ?? 'Не указан'
        };
    }

    public function getTransmissionLabel(): string
    {
        return match($this->transmission) {
            'Automatic' => 'Автоматическая',
            'Manual' => 'Механическая',
            'CVT' => 'Вариатор',
            'Robot' => 'Роботизированная',
            default => $this->transmission ?? 'Не указана'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'available' => 'Доступен',
            'rented' => 'В аренде',
            'maintenance' => 'На обслуживании',
            'reserved' => 'Забронирован',
            'deleted' => 'Удален',
            default => $this->status ?? 'Неизвестно'
        };
    }

    public function getAverageRating(): float
    {
        if ($this->reviews->isEmpty()) {
            return 0.0;
        }

        $total = 0;
        foreach ($this->reviews as $review) {
            $total += $review->getRating();
        }

        return round($total / $this->reviews->count(), 1);
    }

    public function getTotalBookings(): int
    {
        return $this->bookingItems->count();
    }

    public function getTotalRentalDays(): int
    {
        $days = 0;
        foreach ($this->rentalHistories as $history) {
            $days += $history->getTotalDays() ?? 0;
        }
        return $days;
    }

    public function getPriceForPeriod(int $days, int $hours = 0): float
    {
        $total = 0;

        if ($days > 0 && $this->dailyPrice) {
            $total += (float) $this->dailyPrice * $days;
        }

        if ($hours > 0 && $this->hourlyPrice) {
            $total += (float) $this->hourlyPrice * $hours;
        }

        return $total;
    }
}
