<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Factories;

use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;

class AssertableFactory
{
    protected static array $instances = [];

    public static function make(string $assertable, array $args = []): Assertable
    {
        if (array_key_exists($assertable, static::$instances)) {
            return static::$instances[$assertable];
        }

        return new $assertable(...$args);
    }

    public static function register(string $assertable, Assertable $concrete): void
    {
        static::$instances[$assertable] = $concrete;
    }

    public static function clear(): void
    {
        static::$instances = [];
    }
}
