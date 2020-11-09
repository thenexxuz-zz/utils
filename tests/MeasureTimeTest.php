<?php

use TheNexxuz\Utils\MeasureTime;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AiBUY\Utils\MeasureTime
 * @uses \AiBUY\Utils\MeasureTime
 */
class MeasureTimeTest extends TestCase
{
    /**
     * @return MeasureTime
     */
    public function test__construct()
    {
        $time = new MeasureTime();
        $this->assertContainsOnlyInstancesOf(MeasureTime::class, [$time]);
        return $time;
    }

    /**
     * @param MeasureTime $time
     *
     * @depends test__construct
     *
     * @return MeasureTime
     */
    public function testMarkIntervalWithoutMark(MeasureTime $time)
    {
        $this->assertEquals(8, strlen($time->markInterval()));
        return $time;
    }

    /**
     * @param MeasureTime $time
     *
     * @depends testMarkIntervalWithoutMark
     *
     * @return MeasureTime
     */
    public function testMark(MeasureTime $time)
    {
        $this->assertEquals(8, strlen($time->mark()));
        return $time;
    }

    /**
     * @param MeasureTime $time
     *
     * @depends testMark
     *
     * @return MeasureTime
     */
    public function testMarkInterval(MeasureTime $time)
    {
        $this->assertEquals(8, strlen($time->markInterval()));
        return $time;
    }
}