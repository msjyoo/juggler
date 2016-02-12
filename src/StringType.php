<?php

namespace sekjun9878\Juggler;

use function Functional\every as array_every;

final class StringType extends ScalarTypeBase
{
    /**
     * Return an array of instances of Types that you can be juggled to.
     *
     * @return Type[]
     */
    public function getAllowedJuggleTypes()
    {
        return [
            new StringType,
            new IntType,
            new FloatType
        ];
    }

    public function __toString()
    {
        return "string";
    }
}