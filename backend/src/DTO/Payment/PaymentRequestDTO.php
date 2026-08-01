<?php

namespace App\DTO\Payment;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $amount = null;

    #[Assert\Choice(choices: ['credit_card', 'paypal', 'cash', 'bank_transfer', 'crypto'])]
    public ?string $paymentMethod = null;

    #[Assert\Choice(choices: ['pending', 'paid', 'failed', 'refunded', 'cancelled'])]
    public ?string $status = 'pending';

    public ?string $transactionId = null;

    public ?string $paymentDate = null;
}
