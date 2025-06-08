<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Factories;

use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;

class AssertableFactory
{
    /** @var array<class-string, Assertable> */
    protected static array $instances = [];

    /**
     * @template T of Assertable
     * @param class-string<T> $assertable
     * @param array<mixed> $args
     * @return T
     */
    public static function make(string $assertable, array $args = []): Assertable
    {
        if (array_key_exists($assertable, static::$instances)) {
            return static::$instances[$assertable];
        }

        return new $assertable(...$args);
    }

    /**
     * @template T of Assertable
     * @param class-string<T> $assertable
     * @param T $concrete
     */
    public static function register(string $assertable, Assertable $concrete): void
    {
        static::$instances[$assertable] = $concrete;
    }

    public static function clear(): void
    {
        static::$instances = [];
    }
}
