<?php

namespace App\Entity;

use App\Repository\CarImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CarImageRepository::class)]
#[ORM\Table(name: 'car_images')]
#[ORM\HasLifecycleCallbacks]
class CarImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read', 'car_image:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['car_image:read'])]
    private ?Car $car = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'URL изображения обязателен')]
    #[Assert\Url(message: 'Неверный формат URL')]
    #[Groups(['car:read', 'car_image:read', 'car_image:write'])]
    private ?string $imageUrl = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['car:read', 'car_image:read', 'car_image:write'])]
    private ?bool $isMain = false;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['car:read', 'car_image:read', 'car_image:write'])]
    private ?int $sortOrder = 0;

    #[ORM\Column]
    #[Groups(['car_image:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['car_image:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isMain = false;
        $this->sortOrder = 0;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function isMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): static
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

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
}
