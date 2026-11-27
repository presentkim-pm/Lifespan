<?php

declare(strict_types=1);

namespace kim\present\lifespan\tests;

use InvalidArgumentException;
use kim\present\lifespan\Main;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SecondsToTicksTest extends TestCase{

    /** @return iterable<string, array{mixed, int}> */
    public static function validProvider() : iterable{
        yield "int" => [300, 6000];
        yield "float" => [2.5, 50];
        yield "whole float" => [60.0, 1200]; // used to be a TypeError at the int setter under strict_types
        yield "numeric string" => ["10", 200];
        yield "zero" => [0, 0];
    }

    #[DataProvider('validProvider')]
    public function testSecondsToTicks(mixed $seconds, int $expected) : void{
        self::assertSame($expected, Main::secondsToTicks($seconds));
    }

    /** @return iterable<string, array{mixed}> */
    public static function invalidProvider() : iterable{
        yield "string" => ["abc"];
        yield "bool" => [true];
        yield "null" => [null];
        yield "array" => [[1]];
        yield "infinity" => [INF];
        yield "nan" => [NAN];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidValue(mixed $seconds) : void{
        $this->expectException(InvalidArgumentException::class);
        Main::secondsToTicks($seconds);
    }
}
