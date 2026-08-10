<?php

namespace App\Controller\Api\Admin;

use App\DTO\Feature\FeatureRequestDTO;
use App\Service\FeatureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/features')]
class FeaturesController extends AbstractController
{
    public function __construct(
        private readonly FeatureService $featureService,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function features(): JsonResponse
    {
        $features = $this->featureService->getAllFeatures();

        return $this->json([
            'data' => $features
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] FeatureRequestDTO $dto): JsonResponse
    {
        try {
            $feature = $this->featureService->createFeature($dto);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно создана',
                'data' => $feature
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

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload] FeatureRequestDTO $dto): JsonResponse
    {
        try {
            $feature = $this->featureService->updateFeature($id, $dto);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно обновлена',
                'data' => $feature
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
            $this->featureService->deleteFeature($id);

            return $this->json([
                'success' => true,
                'message' => 'Характеристика успешно удалена'
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

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $feature = $this->featureService->getFeatureById($id);

            return $this->json([
                'success' => true,
                'data' => $feature
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
