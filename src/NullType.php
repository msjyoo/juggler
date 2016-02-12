<?php

namespace sekjun9878\Juggler;

final class NullType extends ScalarTypeBase
{
    /**
     * Return an array of instances of Types that you can be juggled to.
     *
     * @return Type[]
     */
    public function getAllowedJuggleTypes()
    {
        return [
            new NullType,
            new BoolType // e.g. if(null) is comparing a boolean value
        ];
    }

    public function __toString()
    {
        return "null";
    }
}