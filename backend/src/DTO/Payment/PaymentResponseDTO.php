<?php

namespace App\DTO\Payment;

use App\Entity\Payment;
use Symfony\Component\Serializer\Annotation\Groups;

class PaymentResponseDTO
{
    #[Groups(['payment:read', 'booking:read'])]
    public int $id;

    #[Groups(['payment:read', 'booking:read'])]
    public int $bookingId;

    #[Groups(['payment:read', 'booking:read'])]
    public float $amount;

    #[Groups(['payment:read'])]
    public ?string $paymentMethod;

    #[Groups(['payment:read'])]
    public ?string $paymentMethodLabel;

    #[Groups(['payment:read'])]
    public string $status;

    #[Groups(['payment:read'])]
    public string $statusLabel;

    #[Groups(['payment:read'])]
    public ?string $transactionId;

    #[Groups(['payment:read'])]
    public ?string $paymentDate;

    #[Groups(['payment:read'])]
    public string $createdAt;

    #[Groups(['payment:read'])]
    public string $updatedAt;

    #[Groups(['payment:read'])]
    public bool $isPaid;

    #[Groups(['payment:read'])]
    public bool $isPending;

    #[Groups(['payment:read'])]
    public bool $isRefunded;

    public static function fromEntity(Payment $payment): self
    {
        $dto = new self();
        $dto->id = $payment->getId();
        $dto->bookingId = $payment->getBooking() ? $payment->getBooking()->getId() : 0;
        $dto->amount = (float) $payment->getAmount();
        $dto->paymentMethod = $payment->getPaymentMethod();
        $dto->paymentMethodLabel = $payment->getPaymentMethodLabel();
        $dto->status = $payment->getStatus();
        $dto->statusLabel = $payment->getStatusLabel();
        $dto->transactionId = $payment->getTransactionId();
        $dto->paymentDate = $payment->getPaymentDate() ? $payment->getPaymentDate()->format('Y-m-d H:i:s') : null;
        $dto->createdAt = $payment->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $payment->getUpdatedAt()->format('Y-m-d H:i:s');
        $dto->isPaid = $payment->isPaid();
        $dto->isPending = $payment->isPending();
        $dto->isRefunded = $payment->isRefunded();

        return $dto;
    }

    public static function fromEntities(array $payments): array
    {
        return array_map(
            fn(Payment $payment) => self::fromEntity($payment),
            $payments
        );
    }
}
