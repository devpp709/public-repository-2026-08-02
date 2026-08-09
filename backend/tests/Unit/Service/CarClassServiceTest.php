<?php

namespace App\Tests\Unit\Service;

use App\DTO\CarClass\CarClassRequestDTO;
use App\Entity\CarClass;
use App\Repository\CarClassRepository;
use App\Service\CarsService;
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

    public function testCreateClass(): void
    {
        $dto = new CarClassRequestDTO();
        $dto->name = 'SUV';
        $dto->description = 'Test description';
        $dto->dailyRate = 100.00;
        $dto->hourlyRate = 15.00;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(CarClass::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->createClass($dto);

        $this->assertEquals('SUV', $result->name);
        $this->assertEquals(100.00, $result->dailyRate);
    }
}
