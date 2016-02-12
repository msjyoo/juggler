Juggler
=======

Juggler is a library for performing "calculations" on abstract PHP types, inside PHP.

Not ready for real world usage, yet.

## Union Types

Union types are basically two types in one object. Because PHP supports multiple return types e.g. Object or null, I
found it necessary to have a UnionType built in. Union types are the same as any other Type object, instead
of having an array of Types. This makes the operations below much more seamless between scalar types and union types.

## Operations

Operations are divided into two categories: strict operations and weak operations. You may be familiar with these
terms if you use PHP. Basically, a strict operation will operate on the type as-is, whereas a weak operation will
attempt to juggle it to an acceptable type.

### Typeof (strict)

The typeof() method is very much like the PHP's is_type() functions.

```php
$x = new IntType;
$x->typeof(new StringType);

// Returns bool(false)
```

Please note that a typeof() operation is essentially a glorified string check and / or an instanceof check.
For example, it will not function as expected on UnionTypes.

In most cases you will find the below intersect() much more useful.

### Intersect (strict)

The intersect() function takes two arguments: $a and $b, and calculates the types that intersect between them.

If you provide it with two scalar types, it will be the same as the typeof().

However, the intersect() function performs a many-to-many match if a UnionType is encountered.

```php
$a = new UnionType(
    new IntType,
    new NullType
);

$b = new IntType;

Type::intersect($a, $b);

// Returns Object(IntType)
```

### Juggle (weak)

The juggle() method attempts to juggle a type into another type, or return null if it is not possible.

If UnionTypes are used, and only some types can be converted into the specified type, it will return another
UnionType of the types that could be juggled to. See example for... the example.

Remember that the best case scenario for juggle() is to return the type as you specified it, or in the worst case
it will return null.

```php
$a = new ArrayType;
$a->juggle(new BoolType);

// Returns Object(BoolType)

$a = new UnionType(
    new ArrayType,
    new StringType
);
$a->juggle(new BoolType);

// Returns Object(BoolType) since both types can be juggled to BoolType.

$a = new StringType;
$b = new UnionType(
    new IntType,
    new ArrayType
);

// In this case, IntType will be returned but ArrayType will not be, because StringType can be converted into
// IntType but not ArrayType.

$a = new UnionType(
    new NullType,
    new BoolType,
    new ArrayType
);
$b = new UnionType(
    new IntType,
    new ArrayType
);

// In this case, juggle() will see if ANY of $a can be converted into ANY of $b.
// e.g.
//    check NullType -> IntType
//    OR
//    check BoolType -> IntType,
//    ...
//    
//    check NullType -> ArrayType
//    OR
//    check BoolType -> ArrayType,
//    ...
```

## Design Considerations

I spent a long time trying to come up with an intuitive system in which I could present PHP's type system
as a consumable API. But if you have any suggestions, please let me know!

## Uses

    - Abstract interpreters
    - Static analysers to warn of type mismatches

## License

    The MIT License; 
    Please see LICENSE for more details including contributions.
