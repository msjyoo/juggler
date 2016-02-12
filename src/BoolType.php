<?php

namespace sekjun9878\Juggler;

use function Functional\every as array_every;

final class BoolType extends ScalarTypeBase
{
    /**
     * Return an array of instances of Types that you can be juggled to.
     *
     * @return Type[]
     */
    public function getAllowedJuggleTypes()
    {
        return [
            new BoolType,
            new NullType // Everything can be casted to NULL by (unset)
        ];
    }

    public function __toString()
    {
        return "bool";
    }
}