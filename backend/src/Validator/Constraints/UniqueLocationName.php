<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueLocationName extends Constraint
{
    public string $message = 'Локация с названием "{{ name }}" уже существует.';
}
