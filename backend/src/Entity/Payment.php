<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'payments')]
#[ORM\HasLifecycleCallbacks]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['payment:read', 'booking:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['payment:read', 'payment:write'])]
    private ?Booking $booking = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['payment:read', 'payment:write', 'booking:read'])]
    private ?string $amount = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(choices: ['credit_card', 'paypal', 'cash', 'bank_transfer', 'crypto'])]
    #[Groups(['payment:read', 'payment:write'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 20, options: ['default' => 'pending'])]
    #[Assert\Choice(choices: ['pending', 'paid', 'failed', 'refunded', 'cancelled'])]
    #[Groups(['payment:read', 'payment:write'])]
    private ?string $status = 'pending';

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $transactionId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['payment:read'])]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\Column]
    #[Groups(['payment:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['payment:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = 'pending';
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setPaymentDateOnPaid(): void
    {
        if ($this->status === 'paid' && $this->paymentDate === null) {
            $this->paymentDate = new \DateTime();
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

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

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): static
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

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

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Ожидает оплаты',
            'paid' => 'Оплачено',
            'failed' => 'Ошибка оплаты',
            'refunded' => 'Возврат',
            'cancelled' => 'Отменено',
            default => $this->status ?? 'Неизвестно'
        };
    }

    public function getPaymentMethodLabel(): string
    {
        return match($this->paymentMethod) {
            'credit_card' => 'Банковская карта',
            'paypal' => 'PayPal',
            'cash' => 'Наличные',
            'bank_transfer' => 'Банковский перевод',
            'crypto' => 'Криптовалюта',
            default => $this->paymentMethod ?? 'Не указан'
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'paid';
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }
}
