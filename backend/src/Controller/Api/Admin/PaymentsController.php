<?php

namespace App\Controller\Api\Admin;

use App\Service\PaymentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/payments')]
class PaymentsController
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
    }

    #[Route('', name: 'admin_payments_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->paymentService->getAllPayments(),
        ]);
    }
}
