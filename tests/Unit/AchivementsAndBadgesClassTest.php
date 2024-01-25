<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\Achbad;

class AchivementsAndBadgesClassTest extends TestCase
{
    
    public function test_not_exact_function(): void
    {
        list($exact, $current, $before, $after, $remainToNext)  = Achbad::calculate([1,5,10], 2);

        $this->assertEquals(false, $exact);
        $this->assertEquals(1, $current);
        $this->assertEquals([1], $before);
        $this->assertEquals([5,10], $after);
        $this->assertEquals(3, $remainToNext);
    }

    public function test_exact_function(): void
    {
        list($exact, $current, $before, $after, $remainToNext)  = Achbad::calculate([1,3,5,7,10,11,50], 3);

        $this->assertEquals(3, $exact);
        $this->assertEquals(3, $current);
        $this->assertEquals([1], $before);
        $this->assertEquals([5,7,10,11,50], $after);
        $this->assertEquals(2, $remainToNext);
    }

    public function test_random_numbers_function(): void
    {
        list($exact, $current, $before, $after, $remainToNext)  = Achbad::calculate([1,3,4,7,11,19,50], 15);

        $this->assertEquals(false, $exact);
        $this->assertEquals(11, $current);
        $this->assertEquals([1,3,4,7,11], $before);
        $this->assertEquals([19,50], $after);
        $this->assertEquals(4, $remainToNext);
    }

    public function test_stringify(): void
    {
        $text = Achbad::stringify(1,'Piece', 'Pieces','bought');
        $this->assertSame('1 Piece bought', $text);

        $text = Achbad::stringify(2,'Piece', 'Pieces','bought');
        $this->assertSame('2 Pieces bought', $text);
    }
}
