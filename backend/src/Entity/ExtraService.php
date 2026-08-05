<?php

namespace App\Entity;

use App\Repository\ExtraServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExtraServiceRepository::class)]
#[ORM\Table(name: 'extra_services')]
#[ORM\HasLifecycleCallbacks]
class ExtraService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['extra_service:read', 'car:read', 'booking:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: 'Название услуги обязательно')]
    #[Assert\Length(max: 200, maxMessage: 'Название не может быть длиннее 200 символов')]
    #[Groups(['extra_service:read', 'extra_service:write', 'car:read', 'booking:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['extra_service:read', 'extra_service:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['extra_service:read', 'extra_service:write'])]
    private ?string $icon = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Insurance', 'Equipment', 'Comfort', 'Safety', 'Additional'],
        message: 'Выберите корректную категорию'
    )]
    #[Groups(['extra_service:read', 'extra_service:write'])]
    private ?string $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Цена должна быть положительным числом')]
    #[Groups(['extra_service:read', 'extra_service:write', 'car:read'])]
    private ?string $defaultPrice = null;

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['extra_service:read', 'extra_service:write'])]
    private ?bool $isActive = true;

    #[ORM\Column]
    #[Groups(['extra_service:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['extra_service:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, CarExtraService>
     */
    #[ORM\OneToMany(targetEntity: CarExtraService::class, mappedBy: 'extraService', cascade: ['persist', 'remove'])]
    private Collection $carExtraServices;

    /**
     * @var Collection<int, BookingExtra>
     */
    #[ORM\OneToMany(targetEntity: BookingExtra::class, mappedBy: 'extraService', cascade: ['persist', 'remove'])]
    private Collection $bookingExtras;

    public function __construct()
    {
        $this->carExtraServices = new ArrayCollection();
        $this->bookingExtras = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isActive = true;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getDefaultPrice(): ?string
    {
        return $this->defaultPrice;
    }

    public function setDefaultPrice(?string $defaultPrice): static
    {
        $this->defaultPrice = $defaultPrice;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
     * @return Collection<int, CarExtraService>
     */
    public function getCarExtraServices(): Collection
    {
        return $this->carExtraServices;
    }

    public function addCarExtraService(CarExtraService $carExtraService): static
    {
        if (!$this->carExtraServices->contains($carExtraService)) {
            $this->carExtraServices->add($carExtraService);
            $carExtraService->setExtraService($this);
        }

        return $this;
    }

    public function removeCarExtraService(CarExtraService $carExtraService): static
    {
        if ($this->carExtraServices->removeElement($carExtraService)) {
            if ($carExtraService->getExtraService() === $this) {
                $carExtraService->setExtraService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BookingExtra>
     */
    public function getBookingExtras(): Collection
    {
        return $this->bookingExtras;
    }

    public function addBookingExtra(BookingExtra $bookingExtra): static
    {
        if (!$this->bookingExtras->contains($bookingExtra)) {
            $this->bookingExtras->add($bookingExtra);
            $bookingExtra->setExtraService($this);
        }

        return $this;
    }

    public function removeBookingExtra(BookingExtra $bookingExtra): static
    {
        if ($this->bookingExtras->removeElement($bookingExtra)) {
            if ($bookingExtra->getExtraService() === $this) {
                $bookingExtra->setExtraService(null);
            }
        }

        return $this;
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'Insurance' => 'Страхование',
            'Equipment' => 'Оборудование',
            'Comfort' => 'Комфорт',
            'Safety' => 'Безопасность',
            'Additional' => 'Дополнительно',
            default => $this->category ?? 'Другое'
        };
    }

    public static function getAvailableCategories(): array
    {
        return [
            'Insurance' => 'Страхование',
            'Equipment' => 'Оборудование',
            'Comfort' => 'Комфорт',
            'Safety' => 'Безопасность',
            'Additional' => 'Дополнительно'
        ];
    }

    public function getPriceForCar(?Car $car = null): ?float
    {
        if ($car) {
            foreach ($this->carExtraServices as $carExtraService) {
                if ($carExtraService->getCar() === $car && $carExtraService->getPrice() !== null) {
                    return (float) $carExtraService->getPrice();
                }
            }
        }

        return $this->defaultPrice ? (float) $this->defaultPrice : null;
    }

    public function isRequiredForCar(Car $car): bool
    {
        foreach ($this->carExtraServices as $carExtraService) {
            if ($carExtraService->getCar() === $car && $carExtraService->isRequired()) {
                return true;
            }
        }

        return false;
    }
}
