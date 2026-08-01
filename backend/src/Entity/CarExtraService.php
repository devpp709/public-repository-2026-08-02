<?php

namespace App\Entity;

use App\Repository\CarExtraServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarExtraServiceRepository::class)]
#[ORM\Table(name: 'car_extra_services')]
#[ORM\UniqueConstraint(name: 'idx_car_extra_services_unique', columns: ['car_id', 'extra_service_id'])]
#[ORM\HasLifecycleCallbacks]
class CarExtraService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car_extra_service:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carExtraServices')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['car_extra_service:read', 'car:read'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: ExtraService::class, inversedBy: 'carExtraServices')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['car_extra_service:read', 'car_extra_service:write', 'extra_service:read'])]
    private ?ExtraService $extraService = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Цена должна быть положительным числом')]
    #[Groups(['car_extra_service:read', 'car_extra_service:write', 'car:read'])]
    private ?string $price = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['car_extra_service:read', 'car_extra_service:write', 'car:read'])]
    private ?bool $isRequired = false;

    #[ORM\Column]
    #[Groups(['car_extra_service:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['car_extra_service:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isRequired = false;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getExtraService(): ?ExtraService
    {
        return $this->extraService;
    }

    public function setExtraService(?ExtraService $extraService): static
    {
        $this->extraService = $extraService;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): static
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Получить актуальную цену (индивидуальную или дефолтную)
     */
    public function getEffectivePrice(): ?float
    {
        if ($this->price !== null) {
            return (float) $this->price;
        }

        return $this->extraService ? (float) $this->extraService->getDefaultPrice() : null;
    }

    /**
     * Проверить, есть ли индивидуальная цена
     */
    public function hasCustomPrice(): bool
    {
        return $this->price !== null;
    }
}
