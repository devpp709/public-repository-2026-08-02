<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserRequestDTO
{
    #[Assert\NotBlank(message: 'Email обязателен')]
    #[Assert\Email(message: 'Неверный формат email')]
    #[Assert\Length(max: 100)]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Имя обязательно', groups: ['create'])]
    #[Assert\Length(max: 100)]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'Пароль обязателен', groups: ['create'])]
    #[Assert\Length(min: 6, minMessage: 'Пароль должен содержать минимум 6 символов', groups: ['create'])]
    public ?string $password = null;

    #[Assert\Regex(pattern: '/^\+?[0-9\s\-()]+$/', message: 'Неверный формат телефона')]
    public ?string $phone = null;

    public ?string $driverLicense = null;
    public ?string $passportNumber = null;

    #[Assert\Url(message: 'Неверный формат URL')]
    public ?string $avatar = null;

    #[Assert\Choice(choices: ['customer', 'manager', 'admin'])]
    public ?string $role = 'customer';

    #[Assert\Choice(choices: ['active', 'blocked', 'pending'])]
    public ?string $status = 'active';
}
