<?php

namespace App\Controller\Api\Admin;

use App\DTO\Location\LocationRequestDTO;
use App\DTO\Location\LocationResponseDTO;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/admin/locations')]
class LocationsController extends AbstractController
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('withStats', true);

        $locations = $this->locationRepository->findAllOrdered();

        return $this->json([
            'data' => LocationResponseDTO::fromEntities(
                $locations,
                $withStats
            ),
        ]);
    }

    #[Route('/search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query->get('q', ''));

        if ($query === '') {
            return $this->json([
                'data' => [],
            ]);
        }

        $locations = $this->locationRepository->search($query);

        return $this->json([
            'data' => LocationResponseDTO::fromEntities($locations),
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $withStats = $request->query->getBoolean('withStats', true);

        $location = $this->locationRepository->find($id);

        if (!$location) {
            return $this->json([
                'message' => 'Локация не найдена',
            ], 404);
        }

        return $this->json([
            'data' => LocationResponseDTO::fromEntity(
                $location,
                $withStats
            ),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'message' => 'Некорректный JSON',
            ], 400);
        }

        $dto = $this->createRequestDTO($data);

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Ошибка валидации',
                'errors' => $this->formatValidationErrors($errors),
            ], 422);
        }

        $location = new Location();

        $this->fillLocation($location, $dto);

        $this->entityManager->persist($location);
        $this->entityManager->flush();

        return $this->json([
            'data' => LocationResponseDTO::fromEntity($location, true),
        ], 201);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(
        int $id,
        Request $request
    ): JsonResponse {
        $location = $this->locationRepository->find($id);

        if (!$location) {
            return $this->json([
                'message' => 'Локация не найдена',
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'message' => 'Некорректный JSON',
            ], 400);
        }

        $dto = $this->createRequestDTO($data);

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Ошибка валидации',
                'errors' => $this->formatValidationErrors($errors),
            ], 422);
        }

        $this->fillLocation($location, $dto);

        $this->entityManager->flush();

        return $this->json([
            'data' => LocationResponseDTO::fromEntity($location, true),
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $location = $this->locationRepository->find($id);

        if (!$location) {
            return $this->json([
                'message' => 'Локация не найдена',
            ], 404);
        }

        if (!$location->getCars()->isEmpty()) {
            return $this->json([
                'message' => 'Нельзя удалить локацию, в которой находятся автомобили',
            ], 409);
        }

        $this->entityManager->remove($location);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
        ]);
    }

    private function createRequestDTO(array $data): LocationRequestDTO
    {
        $dto = new LocationRequestDTO();

        $dto->name = $data['name'] ?? null;
        $dto->address = $data['address'] ?? null;
        $dto->city = $data['city'] ?? null;
        $dto->state = $data['state'] ?? null;
        $dto->country = $data['country'] ?? null;
        $dto->zipCode = $data['zipCode'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->email = $data['email'] ?? null;

        $dto->latitude = isset($data['latitude'])
            ? (float) $data['latitude']
            : null;

        $dto->longitude = isset($data['longitude'])
            ? (float) $data['longitude']
            : null;

        return $dto;
    }

    private function fillLocation(
        Location $location,
        LocationRequestDTO $dto
    ): void {
        $location
            ->setName($dto->name)
            ->setAddress($dto->address)
            ->setCity($dto->city)
            ->setState($dto->state)
            ->setCountry($dto->country)
            ->setZipCode($dto->zipCode)
            ->setPhone($dto->phone)
            ->setEmail($dto->email)
            ->setLatitude(
                $dto->latitude !== null
                    ? (string) $dto->latitude
                    : null
            )
            ->setLongitude(
                $dto->longitude !== null
                    ? (string) $dto->longitude
                    : null
            );
    }

    private function formatValidationErrors(iterable $errors): array
    {
        $result = [];

        foreach ($errors as $error) {
            $result[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $result;
    }
}
