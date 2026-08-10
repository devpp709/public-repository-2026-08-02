<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
#[ORM\Table(name: 'features')]
#[ORM\HasLifecycleCallbacks]
class Feature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['feature:read', 'car:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Название характеристики обязательно')]
    #[Assert\Length(max: 100, maxMessage: 'Название не может быть длиннее 100 символов')]
    #[Groups(['feature:read', 'feature:write', 'car:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['feature:read', 'feature:write'])]
    private ?string $icon = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Безопасность', 'Комфорт', 'Технологии', 'Экстерьер', 'Интерьер', 'Производительность'],
        message: 'Выберите корректную категорию'
    )]
    #[Groups(['feature:read', 'feature:write'])]
    private ?string $category = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['safety', 'comfort', 'technology', 'exterior', 'interior', 'performance', 'media'],
        message: 'Выберите корректный код категории'
    )]
    #[Groups(['feature:read', 'feature:write'])]
    private ?string $categoryCode = null;

    #[ORM\Column]
    #[Groups(['feature:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['feature:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, CarFeature>
     */
    #[ORM\OneToMany(targetEntity: CarFeature::class, mappedBy: 'feature', cascade: ['persist', 'remove'])]
    private Collection $carFeatures; // Изменено с ArrayCollection на Collection

    public function __construct()
    {
        $this->carFeatures = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCategoryCode(): ?string
    {
        return $this->categoryCode;
    }

    public function setCategoryCode(?string $categoryCode): static
    {
        $this->categoryCode = $categoryCode;

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
     * @return Collection<int, CarFeature>
     */
    public function getCarFeatures(): Collection
    {
        return $this->carFeatures;
    }

    public function addCarFeature(CarFeature $carFeature): static
    {
        if (!$this->carFeatures->contains($carFeature)) {
            $this->carFeatures->add($carFeature);
            $carFeature->setFeature($this);
        }

        return $this;
    }

    public function removeCarFeature(CarFeature $carFeature): static
    {
        if ($this->carFeatures->removeElement($carFeature)) {
            if ($carFeature->getFeature() === $this) {
                $carFeature->setFeature(null);
            }
        }

        return $this;
    }

    public static function getCategoryConfig(): array
    {
        return [
            'safety' => ['label' => 'Безопасность', 'icon' => '🛡️'],
            'comfort' => ['label' => 'Комфорт', 'icon' => '💺'],
            'technology' => ['label' => 'Технологии', 'icon' => '💻'],
            'exterior' => ['label' => 'Экстерьер', 'icon' => '🚗'],
            'interior' => ['label' => 'Интерьер', 'icon' => '🪑'],
            'performance' => ['label' => 'Производительность', 'icon' => '⚡'],
            'media' => ['label' => 'Мультимедиа', 'icon' => '🎵'], // Добавьте эту строку
        ];
    }

    public function getCategoryLabel(): string
    {
        $config = self::getCategoryConfig();
        return $config[$this->categoryCode]['label'] ?? $this->category ?? 'Другое';
    }

    public function getCategoryIcon(): string
    {
        $config = self::getCategoryConfig();
        return $config[$this->categoryCode]['icon'] ?? '📋';
    }

    public static function getAvailableCategories(): array
    {
        return array_keys(self::getCategoryConfig());
    }

    public static function getCategoryLabels(): array
    {
        $config = self::getCategoryConfig();

        $result = [];

        foreach ($config as $code => $item) {
            $result[$code] = $item['label'];
        }

        return $result;
    }
}
