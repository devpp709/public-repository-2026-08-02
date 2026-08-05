<?php

namespace App\Validator;

use App\Service\LocationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueLocationNameValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LocationService $locationService
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $exists = $this->locationService->locationRepository->existsByName($value);
        if ($exists) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $value)
                ->addViolation();
        }
    }
}
