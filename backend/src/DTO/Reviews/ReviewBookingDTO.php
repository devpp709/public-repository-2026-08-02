<?php
// src/DTO/Reviews/ReviewBookingDTO.php
namespace App\DTO\Reviews;

class ReviewBookingDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $bookingNumber,
        public readonly string $pickupDate,
        public readonly string $dropoffDate
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            bookingNumber: $data['booking_number'] ?? '',
            pickupDate: $data['pickup_date'] ?? '',
            dropoffDate: $data['dropoff_date'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'bookingNumber' => $this->bookingNumber,
            'pickupDate' => $this->pickupDate,
            'dropoffDate' => $this->dropoffDate,
        ];
    }
}
