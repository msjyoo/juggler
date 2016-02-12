<?php

namespace sekjun9878\Juggler;

use function Functional\every as array_every;
use function Functional\first;
use function Functional\some as array_any;

abstract class ScalarTypeBase extends Type
{
    /**
     * Return an array of instances of Types that you can be juggled to.
     *
     * @return Type[]
     */
    abstract public function getAllowedJuggleTypes();

    public function juggle(Type $type)
    {
        /*
         * This is a predicate to determine whether the CURRENT type (the child class)
         * can be juggled into the Type $type.
         */
        $predicate = function (Type $element) {
            return array_any($this->getAllowedJuggleTypes(),
                function (Type $value, $index, $collection) use ($element) {
                    return (bool) Type::intersect($element, $value);
                }
            );
        };

        $x = array_filter($type->getTypes(), $predicate);

        if(!$x)
        {
            return null;
        }

        if(count($x) === 1)
        {
            return first($x);
        }

        return UnionType::createIfNotDuplicate(array_shift($x), ...$x);
    }

    public function typeof(Type $type)
    {
        return $this instanceof $type;
    }

    public function getTypes()
    {
        return [$this];
    }
}