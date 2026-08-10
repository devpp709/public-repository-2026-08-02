<?php
// src/Service/AuthService.php

namespace App\Service;

use App\Entity\User;
use App\DTO\Auth\AuthResponseDTO;
use App\Repository\UserRepository;
use App\Repository\RefreshTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly LoggerInterface $logger,
    ) {}

    public function register(string $email, string $password, ?string $name = null): ?AuthResponseDTO
    {
        try {
            if ($this->userRepository->existsByEmail($email)) {
                $this->logger->warning('Registration failed: user already exists', ['email' => $email]);
                return null;
            }

            $user = new User();
            $user->setEmail($email);
            $user->setName($name ?? explode('@', $email)[0]);
            $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));
            // createdAt, updatedAt, role и status уже установлены в конструкторе

            // Сохраняем пользователя
            $this->userRepository->save($user);

            $this->logger->info('User registered successfully', ['email' => $email]);

            return $this->generateAuthResponse($user);
        } catch (\Exception $e) {
            $this->logger->error('Registration error: ' . $e->getMessage(), [
                'email' => $email,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function login(string $phone, string $password): ?AuthResponseDTO
    {
        try {
            // Дополнительная проверка телефона (на случай, если контроллер не проверил)
            $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
            if (empty($cleanPhone) || !preg_match('/^\+?[0-9]{10,15}$/', $cleanPhone)) {
                $this->logger->warning('Login failed: invalid phone format', ['phone' => $phone]);
                return null;
            }

            $user = $this->userRepository->findByPhone($phone);

            if (!$user) {
                $this->logger->warning('Login failed: user not found', ['phone' => $phone]);
                return null;
            }

            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                $this->logger->warning('Login failed: invalid password', ['phone' => $phone]);
                return null;
            }

            // Проверка, что пользователь активен
            if (!$user->isActive()) {
                $this->logger->warning('Login failed: user blocked', ['phone' => $phone]);
                return null;
            }

            // Удаляем старые refresh токены
            $this->refreshTokenRepository->deleteUserTokens($user);

            return $this->generateAuthResponse($user);
        } catch (\Exception $e) {
            dump([
                'class' => $e::class,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->logger->error('Login service error: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }

    public function loginByEmail(string $email, string $password): ?AuthResponseDTO
    {
        try {
            $user = $this->userRepository->findByEmail($email);

            if (!$user) {
                $this->logger->warning('Login failed: user not found', ['email' => $email]);
                return null;
            }

            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                $this->logger->warning('Login failed: invalid password', ['email' => $email]);
                return null;
            }

            if (!$user->isActive()) {
                $this->logger->warning('Login failed: user is not active', [
                    'email' => $email,
                    'status' => $user->getStatus()
                ]);
                return null;
            }

            $this->refreshTokenRepository->deleteUserTokens($user);

            $this->logger->info('User logged in successfully', ['email' => $email]);

            return $this->generateAuthResponse($user);
        } catch (\Exception $e) {
            $this->logger->error('Login error: ' . $e->getMessage(), [
                'email' => $email,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function refreshToken(string $refreshToken): ?array
    {
        try {
            $tokenEntity = $this->refreshTokenRepository->findValidToken($refreshToken);

            if (!$tokenEntity) {
                $this->logger->warning('Refresh token failed: invalid token');
                return null;
            }

            $user = $tokenEntity->getUser();
            if (!$user) {
                $this->logger->warning('Refresh token failed: user not found');
                return null;
            }

            // Удаляем старый refresh токен (одноразовый)
            $this->refreshTokenRepository->deleteToken($refreshToken);

            // Создаем новый refresh токен
            $newRefreshToken = $this->refreshTokenRepository->createRefreshToken($user);

            // Создаем новый access token
            $newAccessToken = $this->jwtManager->create($user);

            return [
                'accessToken' => $newAccessToken,
                'refreshToken' => $newRefreshToken->getToken()
            ];
        } catch (\Exception $e) {
            $this->logger->error('Refresh token error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function logout(string $refreshToken): void
    {
        try {
            $this->refreshTokenRepository->deleteToken($refreshToken);
            $this->logger->info('User logged out', ['token' => substr($refreshToken, 0, 10) . '...']);
        } catch (\Exception $e) {
            $this->logger->error('Logout error: ' . $e->getMessage());
        }
    }

    private function generateAuthResponse(User $user): AuthResponseDTO
    {
        $accessToken = $this->jwtManager->create($user);
        $refreshToken = $this->refreshTokenRepository->createRefreshToken($user);

        return AuthResponseDTO::fromUserAndTokens($user, $accessToken, $refreshToken->getToken());
    }
}
