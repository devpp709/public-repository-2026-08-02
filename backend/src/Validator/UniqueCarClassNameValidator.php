<?php

namespace App\Validator;

use App\Service\CarsService;
use App\src\Validator\Constraints\UniqueCarClassName;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueCarClassNameValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CarsService $carClassService
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueCarClassName) {
            throw new UnexpectedTypeException($constraint, UniqueCarClassName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // Проверяем, существует ли класс с таким именем
        $exists = $this->carClassService->isNameExists($value);
        if ($exists) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $value)
                ->addViolation();
        }
    }
}
