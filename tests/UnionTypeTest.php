<?php

namespace sekjun9878\Juggler\Test;

use sekjun9878\Juggler\FloatType;
use sekjun9878\Juggler\IntType;
use sekjun9878\Juggler\NullType;
use sekjun9878\Juggler\StringType;
use sekjun9878\Juggler\UnionType;

class UnionTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testTypeOfScalar()
    {
        $type = new StringType;
        $x = new UnionType(
            new StringType,
            new IntType
        );

        $this->assertFalse($type->typeof($x));
        $this->assertFalse($x->typeof($type));
    }

    public function testTypeOfUnion()
    {
        $type = new UnionType(
            new StringType,
            new IntType
        );

        $x = new UnionType(
            new StringType,
            new IntType
        );

        $y = new UnionType(
            new StringType,
            new NullType
        );

        $this->assertTrue($x->typeof($type));
        $this->assertFalse($y->typeof($type));
    }

    public function testJuggleScalar()
    {
        $x = new UnionType(
            new StringType,
            new IntType
        );

        $this->assertInstanceOf(FloatType::class, $x->juggle(new FloatType)); // e.g. not NULL
    }

    public function testJuggle()
    {
        $x = new IntType;

        $this->assertInstanceOf(FloatType::class, $x->juggle(new FloatType));
    }
}
