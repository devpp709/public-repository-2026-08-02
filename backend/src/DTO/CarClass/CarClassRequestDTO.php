<?php

namespace App\DTO\CarClass;

use Symfony\Component\Validator\Constraints as Assert;

class CarClassRequestDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Название обязательно')]
        #[Assert\Length(max: 100, maxMessage: 'Название не должно превышать 100 символов')]
        public readonly string $name,

        #[Assert\Length(max: 500, maxMessage: 'Описание не должно превышать 500 символов')]
        public readonly ?string $description = null,

        #[Assert\Length(max: 50)]
        public readonly ?string $icon = null,
    ) {
    }
}
