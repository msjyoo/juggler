<?php

namespace sekjun9878\Juggler\Test;

use sekjun9878\Juggler\ArrayType;
use sekjun9878\Juggler\FloatType;
use sekjun9878\Juggler\IntType;
use sekjun9878\Juggler\UnionType;

class IntTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testJuggle()
    {
        $x = new IntType;

        $this->assertInstanceOf(FloatType::class, $x->juggle(new FloatType));
    }

    public function testJuggleUnion()
    {
        $x = new IntType;

        $this->assertInstanceOf(FloatType::class, $x->juggle(new UnionType(
            new FloatType,
            new ArrayType
        )));
    }
}
