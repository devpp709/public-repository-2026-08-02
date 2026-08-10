<?php

namespace App\Controller\Api\Admin;

use App\Repository\FeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/cars-features')]
class CarFeaturesController extends AbstractController
{
    public function __construct(
        private readonly FeatureRepository $featureRepository,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function features(): JsonResponse
    {
        $features = $this->featureRepository->findAllOrderedByName();

        return $this->json([
            'data' => array_map(
                static fn ($feature) => [
                    'id' => $feature->getId(),
                    'name' => $feature->getName(),
                    'icon' => $feature->getIcon(),
                    'category' => $feature->getCategory(),
                    'categoryLabel' => $feature->getCategoryLabel(),
                    'categoryCode' => $feature->getCategoryCode(),
                ],
                $features
            ),
        ]);
    }

}
