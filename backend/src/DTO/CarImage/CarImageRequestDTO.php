<?php

namespace App\DTO\CarImage;

use Symfony\Component\Validator\Constraints as Assert;

class CarImageRequestDTO
{
    #[Assert\NotBlank(message: 'URL изображения обязателен')]
    #[Assert\Url(message: 'Неверный формат URL')]
    public ?string $imageUrl = null;

    public ?bool $isMain = false;

    #[Assert\PositiveOrZero]
    public ?int $sortOrder = 0;
}
