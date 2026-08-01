<?php

namespace App\Controller;

use App\DTO\UserProfileDTO;
use App\Entity\User;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // ------------------------------------------------------------
    // 1. Пополнение баланса (имитация)
    // ------------------------------------------------------------
    #[Route('/user/deposit', name: 'deposit', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deposit(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();

        $amount = $data['amount'] ?? 0;

        if ($amount <= 0) {
            return $this->json(['error' => 'Amount must be greater than 0'], Response::HTTP_BAD_REQUEST);
        }

        // Транзакция: обновляем баланс + создаём транзакцию
        $this->em->beginTransaction();

        try {
            // Обновляем баланс
            $user->setBalance($user->getBalance() + $amount);
            $this->em->persist($user);

            // Создаём запись транзакции
            $transaction = new Transaction();
            $transaction->setUser($user);
            $transaction->setType('deposit');
            $transaction->setAmount($amount);
            $transaction->setOrderId(null);
            $this->em->persist($transaction);

            $this->em->flush();
            $this->em->commit();

            return $this->json([
                'success' => true,
                'newBalance' => $user->getBalance()
            ]);

        } catch (\Exception $e) {
            $this->em->rollback();
            return $this->json(['error' => 'Transaction failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ------------------------------------------------------------
    // 2. Получить баланс текущего пользователя
    // ------------------------------------------------------------
    #[Route('/user/balance', name: 'balance', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function balance(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'balance' => $user->getBalance(),
            'userId' => $user->getId(),
            'username' => $user->getUsername()
        ]);
    }

    // ------------------------------------------------------------
    // 3. Получить баланс по ID (как в вашем коде)
    // ------------------------------------------------------------
    #[Route('/users/{id}/balance', name: 'balance_by_id', methods: ['GET'])]
    public function balanceById(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['balance' => 0], Response::HTTP_OK);
        }

        return $this->json([
            'balance' => $user->getBalance(),
            'userId' => $user->getId()
        ]);
    }

    // ------------------------------------------------------------
    // 4. Получить историю транзакций
    // ------------------------------------------------------------
    #[Route('/user/transactions', name: 'transactions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function transactions(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $transactions = $this->em->getRepository(Transaction::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC'],
            limit: 50
        );

        $result = array_map(function (Transaction $t) {
            return [
                'id' => $t->getId(),
                'type' => $t->getType(),
                'amount' => $t->getAmount(),
                'orderId' => $t->getOrderId(),
                'createdAt' => $t->getCreatedAt()->format(\DateTimeInterface::ISO8601)
            ];
        }, $transactions);

        return $this->json($result);
    }

    #[Route('/user/profile', name: 'user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function profile(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $userProfile = UserProfileDTO::fromUser($user);

        return $this->json($userProfile->toArray());
    }
}
