<?php

namespace App\DTO\CarExtraService;

use Symfony\Component\Validator\Constraints as Assert;

class CarExtraServiceBulkRequestDTO
{
    /**
     * @var CarExtraServiceRequestDTO[]
     */
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[Assert\Valid]
    public array $services = [];

    public function getServices(): array
    {
        return $this->services;
    }

    public function setServices(array $services): self
    {
        $this->services = $services;

        return $this;
    }
}
