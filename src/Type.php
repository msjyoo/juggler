<?php

namespace sekjun9878\Juggler;

use function Functional\first;
use function Functional\flatten as array_flatten;

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
     * Note that by returning true to a typeof interrogation, you guarantee
     * that you are semantically and functionally equivalent to the parameter
     * $type. e.g. In a unique() operation, if the $type exists you are considered
     * a duplicate.
     *
     * This can happen if you consider yourself a subtype of $type. e.g. Int extends Float.
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
     * Type or UnionType containing the types that were equal from a many-to-many
     * typeof() operation or NULL if there are no matches.
     *
     * UnionType->intersect(NormalType)
     * When performed on a UnionType and one normal Type, this operation
     * will return the normal type if the UnionType contains the Type, or
     * NULL otherwise.
     *
     * NormalType->intersect(UnionType)
     * When performed on a Type and one UnionType, this operation will
     * return the Type if the UnionType contains it. NULL otherwise.
     *
     * Strict operation.
     *
     * @param Type $a
     * @param Type $b
     *
     * @return Type|null
     */
    final public static function intersect(Type $a, Type $b)
    {
        // Intersecting between $a and $b
        $output = [];

        $a = $a->getTypes();
        $b = $b->getTypes();

        // Check all elements of $a
        foreach($a as $x)
        {
            foreach($b as $y)
            {
                if($x->typeof($y))
                {
                    $output[] = $x;
                }
            }
        }

        // Check all elements of $b
        foreach($b as $x)
        {
            foreach($a as $y)
            {
                if($x->typeof($y))
                {
                    $output[] = $x;
                }
            }
        }

        // Combine results
        return Type::unique(...$output) ?: null;
    }

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
     * When asked to juggle to a UnionType, it will return a Type/UnionType
     * of the types that can be juggled to any of the UnionType many-to-many and any.
     *
     * e.g. convert to either given in UnionType
     *      string juggled to int|array returns int (string -> int)
     *
     * When deciding whether a type can be juggled to X, if it is possible in PHP,
     * then it is allowed. Leave suspicious cases for user-land to handle.
     *
     * e.g. Array -> String causes weird behaviour with a PHP notice, but we
     * should still return the juggled type because it is possible.
     *
     * Weak operation.
     *
     * @param Type $type
     *
     * @return Type|null
     */
    abstract public function juggle(Type $type);

    /**
     * Get an array of the types contained within.
     *
     * For most types, simply returns [Type] (simple array)
     *
     * For UnionTypes, return the types that are contained within.
     *
     * Treating every type as an implicit UnionType / array is beneficial
     * because putting in special tricks and checks for UnionTypes is difficult
     * to track over time. Better to just put this in here, which works
     * same whether it is normal type or union type (as in, returns array).
     *
     * @return Type[]
     */
    abstract public function getTypes();

    /**
     * Return the string representation of a Type.
     *
     * Unions will be separated by a '|', and collections appended with '[]' as
     * per PHPDoc standards.
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Given a variadic array (expanded) of Type[], return a Type[] containing
     * functionally unique elements (from a typeof() operation).
     *
     * Everything that is entered into this function are automatically flattened to their
     * most basic form e.g. UnionType->getTypes(), StringType->getTypes()
     *
     * @param Type[] ...$types
     *
     * @return Type[]
     */
    final public static function unique(Type ...$types)
    {
        // Can't just use array_unique, because of how UnionType is represented in string
        // Therefore, flatten everything first and then perform unique() by string

        $types = Type::flatten(...$types);

        return array_unique($types, SORT_STRING); // TODO: Types may have equal string representation but still different
    }

    /**
     * Flatten Types, e.g. flatten UnionTypes.
     *
     * This function won't have much of an effect on most types, unless they contain subtypes.
     *
     * Because this function preserves duplicate entries, usually it may be preferable to use Type::unique()
     * which also flattens and removes duplicate entries.
     *
     * @param Type|Type[] ...$types Use array dereferencing to pass an array into this function.
     *
     * @return array
     */
    public static function flatten(Type ...$types)
    {
        // Flatten types with subtypes e.g. UnionTypes
        $walker = function (Type $x) {
            // If the types $x contains is not just itself, go deeper!
            if(count($x->getTypes()) !== 1 or !first($x->getTypes())->typeof($x))
            {
                return Type::flatten($x);
            }

            return $x->getTypes();
        };

        $x = array_map($walker, $types);
        return array_flatten($x);
    }
}
