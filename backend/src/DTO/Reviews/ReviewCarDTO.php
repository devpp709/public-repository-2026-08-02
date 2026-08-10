<?php
// src/DTO/Reviews/ReviewCarDTO.php
namespace App\DTO\Reviews;

class ReviewCarDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly ?string $image
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            brand: $data['brand'] ?? '',
            model: $data['model'] ?? '',
            year: $data['year'] ?? 0,
            image: $data['image'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'image' => $this->image,
        ];
    }
}
