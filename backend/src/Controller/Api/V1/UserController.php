<?php

namespace App\Controller\Api\V1;

use App\DTO\User\UserRequestDTO;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Получить всех пользователей
     */
    #[Route('', name: 'api_users_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', false);
        $role = $request->query->get('role');
        $status = $request->query->get('status');
        $search = $request->query->get('search');

        if ($search) {
            $users = $this->userService->searchUsers($search);
        } elseif ($role) {
            $users = $this->userService->getUsersByRole($role);
        } elseif ($status) {
            $users = $this->userService->getUsersByStatus($status);
        } else {
            $users = $this->userService->getAllUsers($withStats);
        }

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Получить активных пользователей
     */
    #[Route('/active', name: 'api_users_active', methods: ['GET'])]
    public function getActive(): JsonResponse
    {
        $users = $this->userService->getActiveUsers();

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Получить пользователей с активными бронированиями
     */
    #[Route('/with-active-bookings', name: 'api_users_with_active_bookings', methods: ['GET'])]
    public function getWithActiveBookings(): JsonResponse
    {
        $users = $this->userService->getUsersWithActiveBookings();

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Получить пользователей без бронирований
     */
    #[Route('/without-bookings', name: 'api_users_without_bookings', methods: ['GET'])]
    public function getWithoutBookings(): JsonResponse
    {
        $users = $this->userService->getUsersWithoutBookings();

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Получить последних зарегистрированных пользователей
     */
    #[Route('/latest', name: 'api_users_latest', methods: ['GET'])]
    public function getLatest(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $users = $this->userService->getLatestUsers($limit);

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Получить статистику по пользователям
     */
    #[Route('/statistics', name: 'api_users_statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->userService->getStatistics();
        $registrationStats = $this->userService->getRegistrationStats();

        return $this->json([
            'success' => true,
            'data' => [
                'general' => $statistics,
                'registrations' => $registrationStats
            ]
        ]);
    }

    /**
     * Получить пользователя по ID
     */
    #[Route('/{id}', name: 'api_users_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('with_stats', true);
        $user = $this->userService->getUserById($id, $withStats);

        return $this->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Получить пользователя по email
     */
    #[Route('/by-email/{email}', name: 'api_users_by_email', methods: ['GET'])]
    public function getByEmail(string $email): JsonResponse
    {
        $user = $this->userService->getUserByEmail($email);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Пользователь не найден'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Создать пользователя
     */
    #[Route('', name: 'api_users_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] UserRequestDTO $dto): JsonResponse
    {
        try {
            $user = $this->userService->createUser($dto);

            return $this->json([
                'success' => true,
                'message' => 'Пользователь успешно создан',
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Обновить пользователя
     */
    #[Route('/{id}', name: 'api_users_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, #[MapRequestPayload] UserRequestDTO $dto): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Пользователь успешно обновлен',
                'data' => $user
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить статус пользователя
     */
    #[Route('/{id}/status', name: 'api_users_update_status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $status = $request->request->get('status');

        if (!$status) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр status обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->userService->updateStatus($id, $status);

            return $this->json([
                'success' => true,
                'message' => 'Статус пользователя обновлен',
                'data' => $user
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Обновить роль пользователя
     */
    #[Route('/{id}/role', name: 'api_users_update_role', methods: ['PATCH'])]
    public function updateRole(int $id, Request $request): JsonResponse
    {
        $role = $request->request->get('role');

        if (!$role) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр role обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->userService->updateRole($id, $role);

            return $this->json([
                'success' => true,
                'message' => 'Роль пользователя обновлена',
                'data' => $user
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Удалить пользователя
     */
    #[Route('/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return $this->json([
                'success' => true,
                'message' => 'Пользователь успешно удален'
            ]);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Проверить существование пользователя по email
     */
    #[Route('/check-email', name: 'api_users_check_email', methods: ['GET'])]
    public function checkEmail(Request $request): JsonResponse
    {
        $email = $request->query->get('email');
        $excludeId = $request->query->getInt('exclude_id', 0);

        if (!$email) {
            return $this->json([
                'success' => false,
                'message' => 'Параметр email обязателен'
            ], Response::HTTP_BAD_REQUEST);
        }

        $exists = $this->userService->userRepository->existsByEmail($email, $excludeId ?: null);

        return $this->json([
            'success' => true,
            'exists' => $exists
        ]);
    }
}
