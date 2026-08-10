<?php

namespace App\Controller\Api\Admin;

use App\DTO\ExtraService\ExtraServiceResponseDTO;
use App\Repository\ExtraServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/extra-services')]
class ExtraServicesController extends AbstractController
{
    public function __construct(
        private readonly ExtraServiceRepository $extraServiceRepository,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function extraService(): JsonResponse
    {
        $extraServices = $this->extraServiceRepository->findAllOrdered();

        return $this->json([
            'data' => ExtraServiceResponseDTO::fromEntities(
                $extraServices,
                true
            ),
        ]);
    }

}
