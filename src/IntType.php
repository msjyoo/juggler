<?php

namespace sekjun9878\Juggler;

use function Functional\every as array_every;

final class IntType extends ScalarTypeBase
{
    /**
     * Return an array of instances of Types that you can be juggled to.
     *
     * @return Type[]
     */
    public function getAllowedJuggleTypes()
    {
        return [
            new IntType,
            new FloatType,
            new StringType
        ];
    }

    public function __toString()
    {
        return "float";
    }
}