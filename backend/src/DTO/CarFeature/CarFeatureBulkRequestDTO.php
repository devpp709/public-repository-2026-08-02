<?php

namespace App\DTO\CarFeature;

use Symfony\Component\Validator\Constraints as Assert;

class CarFeatureBulkRequestDTO
{
    /**
     * @var CarFeatureRequestDTO[]
     */
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[Assert\Valid]
    public array $features = [];

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): self
    {
        $this->features = $features;

        return $this;
    }
}
