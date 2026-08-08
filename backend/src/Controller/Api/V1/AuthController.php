<?php
// src/Controller/Api/V1/AuthController.php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

#[Route('/api/v1/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly LoggerInterface $logger,
    ) {}

    // src/Controller/Api/V1/AuthController.php

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'name' => $user->getName(),
            'role' => $user->getRole(),
            'roles' => $user->getRoles(),
            'status' => $user->getStatus(),
            'avatar' => $user->getAvatar(),
            'driverLicense' => $user->getDriverLicense(),
            'passportNumber' => $user->getPassportNumber(),
            'createdAt' => $user->getCreatedAt()?->format('c'),
            'updatedAt' => $user->getUpdatedAt()?->format('c'),
        ]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
            $name = $data['name'] ?? null;

            // Валидация
            if (!$email || !$password) {
                return $this->json(['error' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
            }

            if (strlen($password) < 6) {
                return $this->json(['error' => 'Password must be at least 6 characters'], Response::HTTP_BAD_REQUEST);
            }

            // Регистрация
            $response = $this->authService->register($email, $password, $name);

            if (!$response) {
                return $this->json(['error' => 'User already exists'], Response::HTTP_CONFLICT);
            }

            return $this->json($response->toArray(), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->error('Register error: ' . $e->getMessage());
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $phone = $data['phone'] ?? null;
            $password = $data['password'] ?? null;

            if (!$phone || !$password) {
                return $this->json(['error' => 'Phone and password required'], Response::HTTP_BAD_REQUEST);
            }

            // Валидация формата телефона
            if (!$this->isValidPhone($phone)) {
                return $this->json(['error' => 'Invalid phone format'], Response::HTTP_BAD_REQUEST);
            }

            $response = $this->authService->login($phone, $password);

            if (!$response) {
                return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            return $this->json($response->toArray());
        } catch (\Exception $e) {
            $this->logger->error('Login error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isValidPhone(string $phone): bool
    {
        $clean = preg_replace('/[^0-9+]/', '', $phone);
        return !empty($clean) && preg_match('/^\+?[0-9]{10,15}$/', $clean);
    }
    #[Route('/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $refreshToken = $data['refreshToken'] ?? null;

        if (!$refreshToken) {
            return $this->json(['error' => 'Refresh token required'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->authService->refreshToken($refreshToken);

        if (!$result) {
            return $this->json(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($result);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $refreshToken = $data['refreshToken'] ?? null;

        if ($refreshToken) {
            $this->authService->logout($refreshToken);
        }

        return $this->json(['message' => 'Logged out successfully']);
    }

    private function validatePhone(string $phone): ?JsonResponse
    {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

        if (empty($cleanPhone)) {
            return $this->json(['error' => 'Invalid phone number format'], Response::HTTP_BAD_REQUEST);
        }

        $patterns = [
            '/^\+?[0-9]{10,15}$/',
            '/^\+7[0-9]{10}$/',
            '/^8[0-9]{10}$/',
            '/^[0-9]{10}$/',
        ];

        $isValid = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanPhone)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            return $this->json(
                ['error' => 'Invalid phone number format. Supported: +7XXXXXXXXXX, 8XXXXXXXXXX'],
                Response::HTTP_BAD_REQUEST
            );
        }

        return null;
    }
}
