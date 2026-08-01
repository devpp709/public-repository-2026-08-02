<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'booking:read', 'review:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Email обязателен')]
    #[Assert\Email(message: 'Неверный формат email')]
    #[Assert\Length(max: 100)]
    #[Groups(['user:read', 'user:write', 'booking:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Пароль обязателен', groups: ['create'])]
    #[Assert\Length(min: 6, minMessage: 'Пароль должен содержать минимум 6 символов')]
    private ?string $passwordHash = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Имя обязательно')]
    #[Assert\Length(max: 100)]
    #[Groups(['user:read', 'user:write', 'booking:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-()]+$/', message: 'Неверный формат телефона')]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $driverLicense = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $passportNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'Неверный формат URL')]
    #[Groups(['user:read', 'user:write'])]
    private ?string $avatar = null;

    #[ORM\Column(length: 20, options: ['default' => 'customer'])]
    #[Assert\Choice(choices: ['customer', 'manager', 'admin'])]
    #[Groups(['user:read', 'user:write'])]
    private ?string $role = 'customer';

    #[ORM\Column(length: 20, options: ['default' => 'active'])]
    #[Assert\Choice(choices: ['active', 'blocked', 'pending', 'deleted'])]
    #[Groups(['user:read', 'user:write'])]
    private ?string $status = 'active';

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'user')]
    private ArrayCollection $bookings;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'user')]
    private ArrayCollection $reviews;

    #[ORM\OneToMany(targetEntity: PasswordReset::class, mappedBy: 'user')]
    private ArrayCollection $passwordResets;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->passwordResets = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->role = 'customer';
        $this->status = 'active';
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDriverLicense(): ?string
    {
        return $this->driverLicense;
    }

    public function setDriverLicense(?string $driverLicense): static
    {
        $this->driverLicense = $driverLicense;

        return $this;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): static
    {
        $this->passportNumber = $passportNumber;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PasswordReset>
     */
    public function getPasswordResets(): Collection
    {
        return $this->passwordResets;
    }

    public function addPasswordReset(PasswordReset $passwordReset): static
    {
        if (!$this->passwordResets->contains($passwordReset)) {
            $this->passwordResets->add($passwordReset);
            $passwordReset->setUser($this);
        }

        return $this;
    }

    public function removePasswordReset(PasswordReset $passwordReset): static
    {
        if ($this->passwordResets->removeElement($passwordReset)) {
            if ($passwordReset->getUser() === $this) {
                $passwordReset->setUser(null);
            }
        }

        return $this;
    }

    // === UserInterface implementation ===

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Если есть временные данные, очистить их здесь
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->role === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        } elseif ($this->role === 'manager') {
            $roles[] = 'ROLE_MANAGER';
        }

        return $roles;
    }

    // === Дополнительные методы ===

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin' => 'Администратор',
            'manager' => 'Менеджер',
            'customer' => 'Клиент',
            default => $this->role ?? 'Неизвестно'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'Активен',
            'blocked' => 'Заблокирован',
            'pending' => 'Ожидает подтверждения',
            'deleted' => 'Удален',
            default => $this->status ?? 'Неизвестно'
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager' || $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function getFullName(): string
    {
        return $this->name ?? $this->email ?? 'Пользователь';
    }

    public function getInitials(): string
    {
        if (!$this->name) {
            return 'U';
        }

        $parts = explode(' ', trim($this->name));
        $initials = '';

        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }
        }

        return mb_substr($initials, 0, 2);
    }
}
