<?php

namespace RenokiCo\ExplicitArray\Test;

use RenokiCo\ExplicitArray\Arr;

class ExplicitArrayTest extends TestCase
{
    public function test_set_keys_that_contain_dot()
    {
        $array = [
            'a' => [],
            'b' => [],
        ];

        Arr::set($array, '"a.b/c"', '1');
        Arr::set($array, 'a."b.c"', '2');
        Arr::set($array, 'a.b."c"."d"', '3');

        Arr::set($array, 'b.0', '1');
        Arr::set($array, 'b."1"', '2');
        Arr::set($array, 'c', '3');

        $this->assertEquals([
            'a.b/c' => '1',
            'a' => [
                'b.c' => '2',
                'b' => [
                    'c' => ['d' => '3'],
                ],
            ],
            'b' => [
                '1',
                '2',
            ],
            'c' => '3',
        ], $array);

        $this->assertEquals('1', Arr::get($array, '"a.b/c"'));
        $this->assertEquals('2', Arr::get($array, 'a."b.c"'));
        $this->assertEquals('3', Arr::get($array, 'a.b."c"."d"'));

        $this->assertEquals('1', Arr::get($array, 'b.0'));
        $this->assertEquals('2', Arr::get($array, 'b."1"'));
        $this->assertEquals('3', Arr::get($array, 'c'));

        $this->assertTrue(Arr::has($array, '"a.b/c"'));
        $this->assertTrue(Arr::has($array, 'a."b.c"'));
        $this->assertTrue(Arr::has($array, 'a.b."c"."d"'));

        $this->assertTrue(Arr::has($array, 'b.0'));
        $this->assertTrue(Arr::has($array, 'b."1"'));
        $this->assertTrue(Arr::has($array, 'c'));

        Arr::forget($array, '"a.b/c"');
        Arr::forget($array, 'a."b.c"');
        Arr::forget($array, 'a.b."c"."d"');

        Arr::forget($array, 'b.0');
        Arr::forget($array, 'b."1"');
        Arr::forget($array, 'c');

        Arr::forget($array, 'a.b.c');
        Arr::forget($array, 'a.b');

        $this->assertEquals([
            'a' => [],
            'b' => [],
        ], $array);
    }

    public function test_pluck_explicit_keys()
    {
        $array = [
            'developers' => [
                ['a.b.c' => ['name' => 'John']],
            ],
        ];

        $this->assertEquals(['John'], Arr::pluck($array['developers'], '"a.b.c".name'));
    }
}
