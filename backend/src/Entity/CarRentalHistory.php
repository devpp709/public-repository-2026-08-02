<?php

namespace App\Entity;

use App\Repository\CarRentalHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CarRentalHistoryRepository::class)]
#[ORM\Table(name: 'car_rental_history')]
#[ORM\HasLifecycleCallbacks]
class CarRentalHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car_rental_history:read', 'car:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rentalHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: Booking::class, inversedBy: 'rentalHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Booking $booking = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?int $startMileage = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?int $endMileage = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['car_rental_history:read'])]
    private ?int $totalDistance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['car_rental_history:read'])]
    private ?int $totalDays = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['car_rental_history:read'])]
    private ?int $totalHours = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(choices: ['Excellent', 'Good', 'Fair', 'Poor', 'Damaged'])]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?string $conditionBefore = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(choices: ['Excellent', 'Good', 'Fair', 'Poor', 'Damaged'])]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?string $conditionAfter = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['car_rental_history:read', 'car_rental_history:write'])]
    private ?string $notes = null;

    #[ORM\Column]
    #[Groups(['car_rental_history:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['car_rental_history:read'])]
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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateTotals(): void
    {
        // Рассчитываем общий пробег
        if ($this->startMileage !== null && $this->endMileage !== null) {
            $this->totalDistance = $this->endMileage - $this->startMileage;
            if ($this->totalDistance < 0) {
                $this->totalDistance = 0;
            }
        }

        // Рассчитываем дни и часы
        if ($this->startDate && $this->endDate) {
            $diff = $this->startDate->diff($this->endDate);
            $this->totalDays = (int) $diff->days;
            $this->totalHours = (int) ($diff->days * 24 + $diff->h);

            // Если общее время меньше суток
            if ($this->totalHours < 24) {
                $this->totalDays = 0;
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getStartMileage(): ?int
    {
        return $this->startMileage;
    }

    public function setStartMileage(?int $startMileage): static
    {
        $this->startMileage = $startMileage;

        return $this;
    }

    public function getEndMileage(): ?int
    {
        return $this->endMileage;
    }

    public function setEndMileage(?int $endMileage): static
    {
        $this->endMileage = $endMileage;

        return $this;
    }

    public function getTotalDistance(): ?int
    {
        return $this->totalDistance;
    }

    public function setTotalDistance(?int $totalDistance): static
    {
        $this->totalDistance = $totalDistance;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getTotalDays(): ?int
    {
        return $this->totalDays;
    }

    public function setTotalDays(?int $totalDays): static
    {
        $this->totalDays = $totalDays;

        return $this;
    }

    public function getTotalHours(): ?int
    {
        return $this->totalHours;
    }

    public function setTotalHours(?int $totalHours): static
    {
        $this->totalHours = $totalHours;

        return $this;
    }

    public function getConditionBefore(): ?string
    {
        return $this->conditionBefore;
    }

    public function setConditionBefore(?string $conditionBefore): static
    {
        $this->conditionBefore = $conditionBefore;

        return $this;
    }

    public function getConditionAfter(): ?string
    {
        return $this->conditionAfter;
    }

    public function setConditionAfter(?string $conditionAfter): static
    {
        $this->conditionAfter = $conditionAfter;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

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

    public function getConditionBeforeLabel(): string
    {
        return match($this->conditionBefore) {
            'Excellent' => 'Отличное',
            'Good' => 'Хорошее',
            'Fair' => 'Удовлетворительное',
            'Poor' => 'Плохое',
            'Damaged' => 'Повреждено',
            default => $this->conditionBefore ?? 'Не указано'
        };
    }

    public function getConditionAfterLabel(): string
    {
        return match($this->conditionAfter) {
            'Excellent' => 'Отличное',
            'Good' => 'Хорошее',
            'Fair' => 'Удовлетворительное',
            'Poor' => 'Плохое',
            'Damaged' => 'Повреждено',
            default => $this->conditionAfter ?? 'Не указано'
        };
    }

    public function getDurationLabel(): string
    {
        if ($this->totalDays > 0) {
            return sprintf('%d дн. %d ч.', $this->totalDays, $this->totalHours % 24);
        }
        return sprintf('%d ч.', $this->totalHours ?? 0);
    }

    public function getMileageLabel(): string
    {
        if ($this->totalDistance !== null) {
            return number_format($this->totalDistance, 0, '.', ' ') . ' км';
        }
        return 'Не указан';
    }

    public function getConditionChange(): ?string
    {
        if ($this->conditionBefore && $this->conditionAfter) {
            if ($this->conditionBefore === $this->conditionAfter) {
                return 'Без изменений';
            }
            return sprintf('%s → %s',
                $this->getConditionBeforeLabel(),
                $this->getConditionAfterLabel()
            );
        }
        return null;
    }

    public function isConditionWorse(): bool
    {
        $order = ['Excellent' => 4, 'Good' => 3, 'Fair' => 2, 'Poor' => 1, 'Damaged' => 0];

        if ($this->conditionBefore && $this->conditionAfter) {
            return ($order[$this->conditionAfter] ?? 0) < ($order[$this->conditionBefore] ?? 0);
        }

        return false;
    }
}
