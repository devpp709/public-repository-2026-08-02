<?php

namespace App\DTO\User;

class UserStatisticsDTO
{
    public int $total;
    public int $active;
    public int $blocked;
    public int $pending;
    public int $admins;
    public int $managers;
    public int $customers;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->total = (int) $data['total'];
        $dto->active = (int) $data['active'];
        $dto->blocked = (int) $data['blocked'];
        $dto->pending = (int) $data['pending'];
        $dto->admins = (int) $data['admins'];
        $dto->managers = (int) $data['managers'];
        $dto->customers = (int) $data['customers'];

        return $dto;
    }
}
