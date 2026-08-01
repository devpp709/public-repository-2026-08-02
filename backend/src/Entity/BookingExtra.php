<?php

namespace App\Entity;

use App\Repository\BookingExtraRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Дополнительные услуги в бронировании
 *
 * Эта сущность хранит информацию о том, какие дополнительные услуги
 * были выбраны пользователем в конкретном бронировании.
 *
 * Отличие от CarExtraService:
 * - CarExtraService: какие услуги ДОСТУПНЫ для автомобиля (настройка)
 * - BookingExtra: какие услуги ВЫБРАНЫ пользователем в бронировании (факт)
 */
#[ORM\Entity(repositoryClass: BookingExtraRepository::class)]
#[ORM\Table(name: 'booking_extras')]
#[ORM\HasLifecycleCallbacks]
class BookingExtra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['booking:read', 'booking_extra:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookingExtras')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['booking:read', 'booking_extra:read'])]
    private ?Booking $booking = null;

    #[ORM\ManyToOne(targetEntity: ExtraService::class, inversedBy: 'bookingExtras')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ExtraService $extraService = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups(['booking:read', 'booking_extra:read', 'booking_extra:write'])]
    private ?int $quantity = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Positive]
    #[Groups(['booking:read', 'booking_extra:read', 'booking_extra:write'])]
    private ?string $pricePerUnit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Positive]
    #[Groups(['booking:read', 'booking_extra:read'])]
    private ?string $totalPrice = null;

    #[ORM\Column]
    #[Groups(['booking_extra:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['booking_extra:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->quantity = 1;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateTotalPrice(): void
    {
        if ($this->quantity !== null && $this->pricePerUnit !== null) {
            $this->totalPrice = (string) ((float) $this->pricePerUnit * $this->quantity);
        }
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

    public function getExtraService(): ?ExtraService
    {
        return $this->extraService;
    }

    public function setExtraService(?ExtraService $extraService): static
    {
        $this->extraService = $extraService;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPricePerUnit(): ?string
    {
        return $this->pricePerUnit;
    }

    public function setPricePerUnit(string $pricePerUnit): static
    {
        $this->pricePerUnit = $pricePerUnit;

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
     * Получить название услуги
     */
    public function getServiceName(): string
    {
        return $this->extraService ? $this->extraService->getName() : 'Неизвестная услуга';
    }

    /**
     * Получить иконку услуги
     */
    public function getServiceIcon(): ?string
    {
        return $this->extraService ? $this->extraService->getIcon() : null;
    }

    /**
     * Получить категорию услуги
     */
    public function getServiceCategory(): ?string
    {
        return $this->extraService ? $this->extraService->getCategory() : null;
    }
}
