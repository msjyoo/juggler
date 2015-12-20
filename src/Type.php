<?php

namespace sekjun9878\Juggler;

abstract class Type
{
    /**
     * Checks that a variable type is exactly that of the given Type.
     *
     * e.g. $x->typeof(IntType); Check that $x is (strictly) an IntType
     *
     * WHen performed on a UnionType, unless they are both UnionTypes and
     * contain the same types, this operation will return false.
     *
     * Use intersect() for a looser operation.
     *
     * Strict operation.
     *
     * @param Type $type
     *
     * @return bool
     */
    abstract public function typeof(Type $type);

    /**
     * Perform an intersection operation between two Types.
     *
     * If the two types are the same, this will just return that type.
     * If the two types are not the same, this will return NULL.
     *
     * When performed on two UnionTypes, this operation will return another
     * UnionType containing the types that were equal from a many-to-many
     * typeof() operation or NULL if there are no matches.
     *
     * When performed on one UnionType and one normal Type, this operation
     * will return the normal type if the UnionType contains the Type, or
     * NULL otherwise.
     *
     * Strict operation.
     *
     * @param Type $type
     *
     * @return Type|null
     */
    abstract public function intersect(Type $type);

    /**
     * Attempt to juggle a type into the specified Type, or return null otherwise.
     *
     * e.g. $x->juggle(IntType); Attempt to juggle the type to Int.
     *
     * Return the desired Type if possible, return NULL if not possible.
     *
     * When performed on a UnionType, will return the desired type if ANY of the
     * contained types can be juggled, or NULL if none of them can.
     *
     * Weak operation.
     *
     * @param Type $type
     *
     * @return Type|null
     */
    abstract public function juggle(Type $type);

    /**
     * Return the string representation of a Type.
     *
     * Unions will be separated by a '|', and collections appended with '[]' as
     * per PHPDoc standards.
     *
     * @return string
     */
    abstract public function __toString();
}