<?php

namespace App\Entity;

use App\Repository\CarFeatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarFeatureRepository::class)]
#[ORM\Table(name: 'car_features')]
#[ORM\UniqueConstraint(name: 'idx_car_features_unique', columns: ['car_id', 'feature_id'])]
class CarFeature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car_feature:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carFeatures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['car_feature:read', 'car:read'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: Feature::class, inversedBy: 'carFeatures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Feature $feature = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['car_feature:read', 'car_feature:write', 'car:read'])]
    private ?string $value = null;

    #[ORM\Column]
    #[Groups(['car_feature:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getFeature(): ?Feature
    {
        return $this->feature;
    }

    public function setFeature(?Feature $feature): static
    {
        $this->feature = $feature;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
