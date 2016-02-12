<?php

namespace sekjun9878\Juggler;

use function Functional\some as array_any;
use function Functional\every as array_every;
use function Functional\flatten as array_flatten;
use function Functional\first;

final class UnionType extends Type
{
    /** @var Type[] $types UnionType not allowed as defined in __construct() */
    protected $types;

    /**
     * @param Type $type Require at least one type
     * @param Type|Type[] ...$types
     */
    public function __construct(Type $type, Type ...$types)
    {
        $this->types = array_merge([$type], $types);

        if(array_any($this->types, function ($element) {
            return ($element instanceof UnionType);
        }))
        {
            throw new \InvalidArgumentException(
                "Nested UnionTypes are not allowed. Use UnionType::createIfNotDuplicate() instead for automatic flattening.");
        }
    }

    /**
     * Check if all types are the same, in which case just return that type.
     * Otherwise, create the UnionType and return.
     * Also automatically flatten all nested UnionTypes.
     *
     * @param Type $type Require at least one type
     * @param Type|Type[] ...$types
     *
     * @return UnionType|Type
     */
    public static function createIfNotDuplicate(Type $type, Type ...$types)
    {
        $types = array_merge([$type], $types);

        // Flatten nested UnionTypes
        $types = Type::flatten($types);

        if(array_any($types, function (Type $type, $key, array $collection) {
            return $type instanceof UnionType;
        }))
        {
            throw new \LogicException("UnionType::flatten() failed to flatten nested UnionTypes.");
        }

        $types = Type::unique($types);

        if(count($types) <= 1)
        {
            return first($types);
        }

        return new UnionType(array_shift($types), $types);
    }

    public function typeof(Type $type)
    {
        return array_every($this->types, function(Type $element, $key, array $collection) use ($type) {
            return Type::intersect($type, $element);
        });
    }

    /*public function intersect(Type $type)
    {
        $x = array_filter($this->types, function (Type $element) use ($type) {
            return Type::intersect($type, $element);
        });

        if(count($x) > 1)
        {
            return UnionType::createIfNotDuplicate(
                array_shift($x),
                ...$x
            );
        }

        return array_shift($x);
    }*/

    public function juggle(Type $type)
    {
        if(array_filter($this->types, function (Type $element) use ($type) {
            return array_filter($type->getTypes(), function (Type $element) use ($type) {
                return $element->juggle($type);
            });
        }))
        {
            return $type;
        }

        return null;
    }

    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('|', array_map(function (Type $x) { return $x->__toString(); }, $this->types));
    }

    /**
     * Enforce that when a UnionType is cloned, it's subtypes are also deep cloned.
     *
     * They're cheap value-objects anyway so deep cloning is not a problem.
     * Not deep-cloning value-objects can cause a problem as well.
     *
     * Magic method.
     */
    public function __clone()
    {
        // Note that this method is called after the cloning (memory copy) is done.
        // See: PHP manual.

        $this->types = array_map(function (Type $type) {
            return clone $type;
        }, $this->types);
    }
}