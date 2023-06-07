<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Order extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public ?string $message = 'The value "{{ value }}" is not valid.';

    public function getTargets(): array|string
    {
        // или можно self::CLASS_CONSTRAINT
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return \get_class($this).'Validator';
    }
}
