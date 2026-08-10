<?php

namespace App\Tests\Unit\Service;

use App\DTO\CarClass\CarClassRequestDTO;
use App\Entity\CarClass;
use App\Repository\CarClassRepository;
use App\Service\CarService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarClassServiceTest extends TestCase
{
    private CarsService $service;
    private $entityManager;
    private $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(CarClassRepository::class);

        $this->service = new CarsService(
            $this->entityManager,
            $this->repository
        );
    }

    public function testGetClassByIdNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->service->getClassById(999);
    }
}
