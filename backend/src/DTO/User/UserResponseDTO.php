<?php

namespace App\DTO\User;

use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class UserResponseDTO
{
    #[Groups(['user:read'])]
    public int $id;

    #[Groups(['user:read'])]
    public string $email;

    #[Groups(['user:read'])]
    public string $name;

    #[Groups(['user:read'])]
    public ?string $fullName;

    #[Groups(['user:read'])]
    public ?string $initials;

    #[Groups(['user:read'])]
    public ?string $phone;

    #[Groups(['user:read'])]
    public ?string $driverLicense;

    #[Groups(['user:read'])]
    public ?string $passportNumber;

    #[Groups(['user:read'])]
    public ?string $avatar;

    #[Groups(['user:read'])]
    public ?string $role;

    #[Groups(['user:read'])]
    public ?string $roleLabel;

    #[Groups(['user:read'])]
    public ?string $status;

    #[Groups(['user:read'])]
    public ?string $statusLabel;

    #[Groups(['user:read'])]
    public bool $isActive;

    #[Groups(['user:read'])]
    public bool $isAdmin;

    #[Groups(['user:read'])]
    public bool $isManager;

    #[Groups(['user:read'])]
    public bool $isCustomer;

    #[Groups(['user:read'])]
    public string $createdAt;

    #[Groups(['user:read'])]
    public string $updatedAt;

    #[Groups(['user:read'])]
    public ?int $bookingsCount = 0;

    #[Groups(['user:read'])]
    public ?int $reviewsCount = 0;

    public static function fromEntity(User $user, bool $withStats = false): self
    {
        $dto = new self();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();
        $dto->name = $user->getName();
        $dto->fullName = $user->getFullName();
        $dto->initials = $user->getInitials();
        $dto->phone = $user->getPhone();
        $dto->driverLicense = $user->getDriverLicense();
        $dto->passportNumber = $user->getPassportNumber();
        $dto->avatar = $user->getAvatar();
        $dto->role = $user->getRole()?->getCode();
        $dto->status = $user->getStatus();
        $dto->statusLabel = $user->getStatusLabel();
        $dto->isActive = $user->isActive();
        $dto->roleLabel = $user->getRoleLabel();
        $dto->isAdmin = $user->isAdmin();
        $dto->isManager = $user->isManager();
        $dto->isCustomer = $user->isCustomer();
        $dto->createdAt = $user->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $user->getUpdatedAt()->format('Y-m-d H:i:s');

        if ($withStats) {
            $dto->bookingsCount = $user->getBookings()->count();
            $dto->reviewsCount = $user->getReviews()->count();
        }

        return $dto;
    }

    public static function fromEntities(array $users, bool $withStats = false): array
    {
        return array_map(
            fn(User $user) => self::fromEntity($user, $withStats),
            $users
        );
    }

    // Добавьте этот метод
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'fullName' => $this->fullName,
            'initials' => $this->initials,
            'phone' => $this->phone,
            'driverLicense' => $this->driverLicense,
            'passportNumber' => $this->passportNumber,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'roleLabel' => $this->roleLabel,
            'status' => $this->status,
            'statusLabel' => $this->statusLabel,
            'isActive' => $this->isActive,
            'isAdmin' => $this->isAdmin,
            'isManager' => $this->isManager,
            'isCustomer' => $this->isCustomer,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'bookingsCount' => $this->bookingsCount,
            'reviewsCount' => $this->reviewsCount,
        ];
    }
}
