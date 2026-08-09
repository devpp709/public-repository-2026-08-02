<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ORM\Table(name: 'locations')]
#[ORM\HasLifecycleCallbacks]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['location:read', 'car:read', 'booking:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: 'Название локации обязательно')]
    #[Assert\Length(max: 200, maxMessage: 'Название не может быть длиннее 200 символов')]
    #[Groups(['location:read', 'location:write', 'car:read', 'booking:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $address = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $city = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $state = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $country = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $zipCode = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-()]+$/', message: 'Неверный формат телефона')]
    #[Groups(['location:read', 'location:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Email(message: 'Неверный формат email')]
    #[Groups(['location:read', 'location:write'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8, nullable: true)]
    #[Assert\Range(notInRangeMessage: 'Широта должна быть между -90 и 90 градусами', min: -90, max: 90)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    #[Assert\Range(notInRangeMessage: 'Долгота должна быть между -180 и 180 градусами', min: -180, max: 180)]
    #[Groups(['location:read', 'location:write'])]
    private ?string $longitude = null;

    #[ORM\Column]
    #[Groups(['location:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['location:read'])]
    private ?\DateTimeImmutable $updatedAt = null;
    #[ORM\OneToMany(targetEntity: Car::class, mappedBy: 'location')]
    private Collection $cars;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'pickupLocation')]
    private Collection $pickupBookings;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'dropoffLocation')]
    private Collection $dropoffBookings;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'locations')]
    #[ORM\JoinColumn(name: 'region_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['location:read', 'location:write', 'booking:read'])]
    private ?Region $region = null;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->pickupBookings = new ArrayCollection();
        $this->dropoffBookings = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->country = 'Россия';
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

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
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setLocation($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            if ($car->getLocation() === $this) {
                $car->setLocation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getPickupBookings(): Collection
    {
        return $this->pickupBookings;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function addPickupBooking(Booking $booking): static
    {
        if (!$this->pickupBookings->contains($booking)) {
            $this->pickupBookings->add($booking);
            $booking->setPickupLocation($this);
        }

        return $this;
    }

    public function removePickupBooking(Booking $booking): static
    {
        if ($this->pickupBookings->removeElement($booking)) {
            if ($booking->getPickupLocation() === $this) {
                $booking->setPickupLocation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getDropoffBookings(): Collection
    {
        return $this->dropoffBookings;
    }

    public function addDropoffBooking(Booking $booking): static
    {
        if (!$this->dropoffBookings->contains($booking)) {
            $this->dropoffBookings->add($booking);
            $booking->setDropoffLocation($this);
        }

        return $this;
    }

    public function removeDropoffBooking(Booking $booking): static
    {
        if ($this->dropoffBookings->removeElement($booking)) {
            if ($booking->getDropoffLocation() === $this) {
                $booking->setDropoffLocation(null);
            }
        }

        return $this;
    }

    public function getFullAddress(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zipCode,
            $this->country
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }
}
