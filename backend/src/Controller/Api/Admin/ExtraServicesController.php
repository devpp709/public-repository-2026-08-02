<?php

namespace App\Controller\Api\Admin;

use App\DTO\ExtraService\ExtraServiceRequestDTO;
use App\DTO\ExtraService\ExtraServiceResponseDTO;
use App\Service\ExtraServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/extra-services')]
class ExtraServicesController extends AbstractController
{
    public function __construct(
        private readonly ExtraServiceService $extraServiceService,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $extraServices = $this->extraServiceService->getAllServices(true);

        return $this->json([
            'data' => $extraServices,
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] ExtraServiceRequestDTO $dto): JsonResponse
    {
        try {
            $service = $this->extraServiceService->createService($dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно создана',
                'data' => $service
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $service = $this->extraServiceService->getServiceById($id);

            return $this->json([
                'success' => true,
                'data' => $service
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload] ExtraServiceRequestDTO $dto): JsonResponse
    {
        try {
            $service = $this->extraServiceService->updateService($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно обновлена',
                'data' => $service
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->extraServiceService->deleteService($id);

            return $this->json([
                'success' => true,
                'message' => 'Услуга успешно удалена'
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
