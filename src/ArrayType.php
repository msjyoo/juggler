<?php

namespace sekjun9878\Juggler;

final class ArrayType extends ScalarTypeBase
{
    public function getAllowedJuggleTypes()
    {
        return [
            new ArrayType,
            new StringType,
            new IntType,
            new FloatType,
            new NullType
        ];
    }

    public function __toString()
    {
        return "array";
    }
}