<?php

namespace App\src\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueCarClassName extends Constraint
{
    public string $message = 'Класс с названием "{{ name }}" уже существует.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
