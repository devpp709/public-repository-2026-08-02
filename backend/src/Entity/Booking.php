<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'bookings')]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['booking:read', 'car_rental_history:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'pickupBookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $pickupLocation = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'dropoffBookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $dropoffLocation = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['booking:read'])]
    private ?string $bookingNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?\DateTimeInterface $pickupDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?\DateTimeInterface $pickupTime = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?\DateTimeInterface $dropoffDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?\DateTimeInterface $dropoffTime = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $totalDays = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['booking:read'])]
    private ?int $totalHours = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Groups(['booking:read'])]
    private ?string $subtotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Groups(['booking:read'])]
    private ?string $extrasTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Groups(['booking:read'])]
    private ?string $totalAmount = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Groups(['booking:read'])]
    private ?string $securityDeposit = '0.00';

    #[ORM\Column(length: 20, options: ['default' => 'pending'])]
    #[Assert\Choice(choices: ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $notes = null;

    #[ORM\Column]
    #[Groups(['booking:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['booking:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, BookingExtra>
     */
    #[ORM\OneToMany(targetEntity: BookingExtra::class, mappedBy: 'booking', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['booking:read'])]
    private Collection $bookingExtras;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'booking', cascade: ['persist', 'remove'])]
    #[Groups(['booking:read'])]
    private Collection $payments;
    #[ORM\OneToMany(targetEntity: CarRentalHistory::class, mappedBy: 'booking')]
    private Collection $rentalHistories;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'booking')]
    private Collection $reviews;

    #[ORM\ManyToOne(
        targetEntity: Car::class,
        inversedBy: 'bookings'
    )]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['booking:read', 'booking:write'])]
    private ?Car $car = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $dailyRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $hourlyRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $totalPrice = null;


    public function __construct()
    {
        $this->bookingExtras = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->rentalHistories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = 'pending';
        $this->subtotal = '0.00';
        $this->extrasTotal = '0.00';
        $this->totalAmount = '0.00';
        $this->securityDeposit = '0.00';
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function generateBookingNumber(): void
    {
        if (!$this->bookingNumber) {
            $this->bookingNumber = 'BR-' . date('Y') . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateTotals(): void
    {
        // Рассчитываем количество дней и часов
        if ($this->pickupDate && $this->dropoffDate) {
            $diff = $this->pickupDate->diff($this->dropoffDate);
            $this->totalDays = (int) $diff->days;

            if ($this->pickupTime && $this->dropoffTime) {
                $pickupDateTime = new \DateTime(
                    $this->pickupDate->format('Y-m-d') . ' ' . $this->pickupTime->format('H:i:s')
                );

                $dropoffDateTime = new \DateTime(
                    $this->dropoffDate->format('Y-m-d') . ' ' . $this->dropoffTime->format('H:i:s')
                );

                $diffTime = $pickupDateTime->diff($dropoffDateTime);
                $this->totalHours = $diffTime->days * 24 + $diffTime->h;

                if ($this->totalHours < 24) {
                    $this->totalDays = 0;
                }
            }
        }

        // Автомобиль теперь один, поэтому subtotal = totalPrice
        $this->subtotal = $this->totalPrice ?? '0.00';

        // Дополнительные услуги
        $extrasTotal = 0;

        foreach ($this->bookingExtras as $extra) {
            $extrasTotal += (float) $extra->getTotalPrice();
        }

        $this->extrasTotal = number_format($extrasTotal, 2, '.', '');

        // Общая сумма
        $this->totalAmount = number_format(
            (float) $this->subtotal + $extrasTotal,
            2,
            '.',
            ''
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBookingNumber(): ?string
    {
        return $this->bookingNumber;
    }

    public function setBookingNumber(string $bookingNumber): static
    {
        $this->bookingNumber = $bookingNumber;

        return $this;
    }

    public function getPickupLocation(): ?Location
    {
        return $this->pickupLocation;
    }

    public function setPickupLocation(?Location $pickupLocation): static
    {
        $this->pickupLocation = $pickupLocation;

        return $this;
    }

    public function getDropoffLocation(): ?Location
    {
        return $this->dropoffLocation;
    }

    public function setDropoffLocation(?Location $dropoffLocation): static
    {
        $this->dropoffLocation = $dropoffLocation;

        return $this;
    }

    public function getPickupDate(): ?\DateTimeInterface
    {
        return $this->pickupDate;
    }

    public function setPickupDate(\DateTimeInterface $pickupDate): static
    {
        $this->pickupDate = $pickupDate;

        return $this;
    }

    public function getPickupTime(): ?\DateTimeInterface
    {
        return $this->pickupTime;
    }

    public function setPickupTime(\DateTimeInterface $pickupTime): static
    {
        $this->pickupTime = $pickupTime;

        return $this;
    }

    public function getDropoffDate(): ?\DateTimeInterface
    {
        return $this->dropoffDate;
    }

    public function setDropoffDate(\DateTimeInterface $dropoffDate): static
    {
        $this->dropoffDate = $dropoffDate;

        return $this;
    }

    public function getDropoffTime(): ?\DateTimeInterface
    {
        return $this->dropoffTime;
    }

    public function setDropoffTime(\DateTimeInterface $dropoffTime): static
    {
        $this->dropoffTime = $dropoffTime;

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

    public function getSubtotal(): ?string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): static
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getExtrasTotal(): ?string
    {
        return $this->extrasTotal;
    }

    public function setExtrasTotal(string $extrasTotal): static
    {
        $this->extrasTotal = $extrasTotal;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getSecurityDeposit(): ?string
    {
        return $this->securityDeposit;
    }

    public function setSecurityDeposit(string $securityDeposit): static
    {
        $this->securityDeposit = $securityDeposit;

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

    /**
     * @return Collection<int, BookingExtra>
     */
    public function getBookingExtras(): Collection
    {
        return $this->bookingExtras;
    }

    public function addBookingExtra(BookingExtra $bookingExtra): static
    {
        if (!$this->bookingExtras->contains($bookingExtra)) {
            $this->bookingExtras->add($bookingExtra);
            $bookingExtra->setBooking($this);
        }

        return $this;
    }

    public function removeBookingExtra(BookingExtra $bookingExtra): static
    {
        if ($this->bookingExtras->removeElement($bookingExtra)) {
            if ($bookingExtra->getBooking() === $this) {
                $bookingExtra->setBooking(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setBooking($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            if ($payment->getBooking() === $this) {
                $payment->setBooking(null);
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
            $rentalHistory->setBooking($this);
        }

        return $this;
    }

    public function removeRentalHistory(CarRentalHistory $rentalHistory): static
    {
        if ($this->rentalHistories->removeElement($rentalHistory)) {
            if ($rentalHistory->getBooking() === $this) {
                $rentalHistory->setBooking(null);
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

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setBooking($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getBooking() === $this) {
                $review->setBooking(null);
            }
        }

        return $this;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Ожидает подтверждения',
            'confirmed' => 'Подтверждено',
            'in_progress' => 'В процессе',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
            default => $this->status ?? 'Неизвестно'
        };
    }

    public function getPickupDateTime(): ?\DateTime
    {
        if ($this->pickupDate && $this->pickupTime) {
            return new \DateTime(
                $this->pickupDate->format('Y-m-d') . ' ' . $this->pickupTime->format('H:i:s')
            );
        }
        return null;
    }

    public function getDropoffDateTime(): ?\DateTime
    {
        if ($this->dropoffDate && $this->dropoffTime) {
            return new \DateTime(
                $this->dropoffDate->format('Y-m-d') . ' ' . $this->dropoffTime->format('H:i:s')
            );
        }
        return null;
    }

    public function getDurationInHours(): int
    {
        $pickup = $this->getPickupDateTime();
        $dropoff = $this->getDropoffDateTime();

        if ($pickup && $dropoff) {
            $diff = $pickup->diff($dropoff);
            return (int) ($diff->days * 24 + $diff->h);
        }

        return 0;
    }

    public function getDurationInDays(): float
    {
        $hours = $this->getDurationInHours();
        return $hours / 24;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
