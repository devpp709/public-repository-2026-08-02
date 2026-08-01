<?php

namespace App\Entity;

use App\Repository\BookingItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Элемент бронирования - автомобиль в бронировании
 *
 * Эта сущность хранит информацию о том, какой автомобиль
 * был забронирован в рамках конкретного бронирования.
 *
 * Отличие от Booking:
 * - Booking: общая информация о бронировании (пользователь, даты, итоговые суммы)
 * - BookingItem: конкретный автомобиль в бронировании (цена на момент бронирования)
 */
#[ORM\Entity(repositoryClass: BookingItemRepository::class)]
#[ORM\Table(name: 'booking_items')]
#[ORM\HasLifecycleCallbacks]
class BookingItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['booking:read', 'booking_item:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookingItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['booking:read', 'booking_item:read'])]
    private ?Booking $booking = null;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: 'bookingItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Car $car = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['booking:read', 'booking_item:read', 'booking_item:write'])]
    private ?string $dailyRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['booking:read', 'booking_item:read', 'booking_item:write'])]
    private ?string $hourlyRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['booking:read', 'booking_item:read'])]
    private ?string $totalPrice = null;

    #[ORM\Column]
    #[Groups(['booking_item:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['booking_item:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Рассчитать общую стоимость
     *
     * @param int $days Количество дней
     * @param int $hours Количество часов
     */
    public function calculateTotalPrice(int $days, int $hours = 0): void
    {
        $total = 0;

        if ($this->dailyRate && $days > 0) {
            $total += (float) $this->dailyRate * $days;
        }

        if ($this->hourlyRate && $hours > 0) {
            $total += (float) $this->hourlyRate * $hours;
        }

        $this->totalPrice = (string) $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getDailyRate(): ?string
    {
        return $this->dailyRate;
    }

    public function setDailyRate(string $dailyRate): static
    {
        $this->dailyRate = $dailyRate;

        return $this;
    }

    public function getHourlyRate(): ?string
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(?string $hourlyRate): static
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

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
     * Получить название автомобиля
     */
    public function getCarName(): string
    {
        return $this->car ? $this->car->getFullName() : 'Неизвестный автомобиль';
    }

    /**
     * Получить VIN автомобиля
     */
    public function getCarVin(): ?string
    {
        return $this->car ? $this->car->getVin() : null;
    }

    /**
     * Получить госномер автомобиля
     */
    public function getCarLicensePlate(): ?string
    {
        return $this->car ? $this->car->getLicensePlate() : null;
    }

    /**
     * Проверить, является ли аренда почасовой
     */
    public function isHourlyRental(): bool
    {
        return $this->hourlyRate !== null;
    }
}
