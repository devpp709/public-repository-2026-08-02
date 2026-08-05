<?php

namespace App\Service;

use App\DTO\User\UserRequestDTO;
use App\DTO\User\UserResponseDTO;
use App\DTO\User\UserStatisticsDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        public readonly UserRepository               $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Получить всех пользователей
     */
    public function getAllUsers(bool $withStats = false): array
    {
        $users = $this->userRepository->findBy(['status' => 'deleted'], ['name' => 'ASC']);
        return UserResponseDTO::fromEntities($users, $withStats);
    }

    /**
     * Получить пользователя по ID
     */
    public function getUserById(int $id, bool $withStats = false): UserResponseDTO
    {
        $user = $this->findUserOrFail($id);
        return UserResponseDTO::fromEntity($user, $withStats);
    }

    /**
     * Получить пользователя по email
     */
    public function getUserByEmail(string $email): ?UserResponseDTO
    {
        $user = $this->userRepository->findByEmail($email);
        return $user ? UserResponseDTO::fromEntity($user) : null;
    }

    /**
     * Создать пользователя
     */
    public function createUser(UserRequestDTO $dto): UserResponseDTO
    {
        // Проверяем уникальность email
        if ($this->userRepository->existsByEmail($dto->email)) {
            throw new \InvalidArgumentException('Пользователь с таким email уже существует');
        }

        $user = new User();
        $this->updateUserFromDto($user, $dto);

        // Хешируем пароль
        if ($dto->password) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
            $user->setPasswordHash($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return UserResponseDTO::fromEntity($user);
    }

    /**
     * Обновить пользователя
     */
    public function updateUser(int $id, UserRequestDTO $dto): UserResponseDTO
    {
        $user = $this->findUserOrFail($id);

        // Проверяем уникальность email (исключая текущего пользователя)
        if ($dto->email && $this->userRepository->existsByEmail($dto->email, $id)) {
            throw new \InvalidArgumentException('Пользователь с таким email уже существует');
        }

        $this->updateUserFromDto($user, $dto);

        // Обновляем пароль, если он указан
        if ($dto->password) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
            $user->setPasswordHash($hashedPassword);
        }

        $this->entityManager->flush();

        return UserResponseDTO::fromEntity($user);
    }

    /**
     * Удалить пользователя (мягкое удаление)
     */
    public function deleteUser(int $id): void
    {
        $user = $this->findUserOrFail($id);

        // Проверяем, есть ли у пользователя активные бронирования
        foreach ($user->getBookings() as $booking) {
            if (in_array($booking->getStatus(), ['pending', 'confirmed', 'in_progress'])) {
                throw new \RuntimeException(
                    sprintf(
                        'Невозможно удалить пользователя "%s", так как у него есть активные бронирования (#%s)',
                        $user->getName(),
                        $booking->getBookingNumber()
                    )
                );
            }
        }

        $user->setStatus('deleted');
        $this->entityManager->flush();
    }

    /**
     * Поиск пользователей
     */
    public function searchUsers(string $searchTerm): array
    {
        $users = $this->userRepository->search($searchTerm);
        return UserResponseDTO::fromEntities($users);
    }

    /**
     * Получить пользователей по роли
     */
    public function getUsersByRole(string $role): array
    {
        $users = $this->userRepository->findByRole($role);
        return UserResponseDTO::fromEntities($users);
    }

    /**
     * Получить пользователей по статусу
     */
    public function getUsersByStatus(string $status): array
    {
        $users = $this->userRepository->findByStatus($status);
        return UserResponseDTO::fromEntities($users);
    }

    /**
     * Получить активных пользователей
     */
    public function getActiveUsers(): array
    {
        $users = $this->userRepository->findActive();
        return UserResponseDTO::fromEntities($users);
    }

    /**
     * Получить пользователей с активными бронированиями
     */
    public function getUsersWithActiveBookings(): array
    {
        $users = $this->userRepository->findWithActiveBookings();
        return UserResponseDTO::fromEntities($users, true);
    }

    /**
     * Получить пользователей без бронирований
     */
    public function getUsersWithoutBookings(): array
    {
        $users = $this->userRepository->findWithoutBookings();
        return UserResponseDTO::fromEntities($users);
    }

    /**
     * Получить последних зарегистрированных пользователей
     */
    public function getLatestUsers(int $limit = 10): array
    {
        $users = $this->userRepository->findLatest($limit);
        return UserResponseDTO::fromEntities($users);
    }

    /**
     * Получить статистику по пользователям
     */
    public function getStatistics(): UserStatisticsDTO
    {
        $statistics = $this->userRepository->getStatistics();
        return UserStatisticsDTO::fromArray($statistics);
    }

    /**
     * Получить статистику по регистрациям
     */
    public function getRegistrationStats(int $days = 30): array
    {
        return $this->userRepository->getRegistrationStats($days);
    }

    /**
     * Обновить статус пользователя
     */
    public function updateStatus(int $id, string $status): UserResponseDTO
    {
        $user = $this->findUserOrFail($id);
        $user->setStatus($status);
        $this->entityManager->flush();

        return UserResponseDTO::fromEntity($user);
    }

    /**
     * Обновить роль пользователя
     */
    public function updateRole(int $id, string $role): UserResponseDTO
    {
        $user = $this->findUserOrFail($id);
        $user->setRole($role);
        $this->entityManager->flush();

        return UserResponseDTO::fromEntity($user);
    }

    /**
     * Обновить пользователя из DTO
     */
    private function updateUserFromDto(User $user, UserRequestDTO $dto): void
    {
        if ($dto->email !== null) {
            $user->setEmail($dto->email);
        }
        if ($dto->name !== null) {
            $user->setName($dto->name);
        }
        if ($dto->phone !== null) {
            $user->setPhone($dto->phone);
        }
        if ($dto->driverLicense !== null) {
            $user->setDriverLicense($dto->driverLicense);
        }
        if ($dto->passportNumber !== null) {
            $user->setPassportNumber($dto->passportNumber);
        }
        if ($dto->avatar !== null) {
            $user->setAvatar($dto->avatar);
        }
        if ($dto->role !== null) {
            $user->setRole($dto->role);
        }
        if ($dto->status !== null) {
            $user->setStatus($dto->status);
        }
    }

    /**
     * Найти пользователя или выбросить исключение
     */
    private function findUserOrFail(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('Пользователь с ID %d не найден', $id));
        }

        return $user;
    }
}
