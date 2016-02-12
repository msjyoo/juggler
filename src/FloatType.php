<?php

namespace sekjun9878\Juggler;

use function Functional\every as array_every;

final class FloatType extends ScalarTypeBase
{
    /**
     * Return an array of instances of Types that you can be juggled to.
     *
     * @return Type[]
     */
    public function getAllowedJuggleTypes()
    {
        return [
            new FloatType,
            new IntType,
            new StringType
        ];
    }

    public function __toString()
    {
        return "float";
    }
}