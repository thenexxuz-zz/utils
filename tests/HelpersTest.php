<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class HelpersTest extends TestCase
{

    public function testDdd()
    {
        $expected = "Posted from: " . pathinfo(__FILE__, PATHINFO_DIRNAME) . '/' . pathinfo(__FILE__, PATHINFO_FILENAME) . '.' . pathinfo(__FILE__, PATHINFO_EXTENSION) . <<<TEXT
 line:19

test
TEXT;
        $this->expectOutputString($expected);
        ddd('test');
    }

    public function testPrettyPrintString()
    {
        $expected = '<span><strong>(string)</strong> <span style="color:red;">test</span> <i>(length=4)</i></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint('test')));
    }

    public function testPrettyPrintInteger()
    {
        $expected = '<span><strong>(int)</strong> <span style="color: green;">1</span></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(1)));
    }

    public function testPrettyPrintDoubleFloat()
    {
        $expected = '<span><strong>(double)</strong> <span style="color: brown;">1</span></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(1.0)));
    }

    public function testPrettyPrintBoolean()
    {
        $expected = '<span><strong>(boolean)</strong> <span style="color: purple;">true</span></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(true)));
    }

    public function testPrettyPrintNull()
    {
        $expected = '<span><strong><span style="color: black;">NULL</span></strong></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(null)));
    }

    public function testPrettyPrintArray()
    {
        $expected = '<span><strong>(array)</strong> (size=0) <ul style="list-style: none;"></ul></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint([])));
    }

    public function testPrettyPrintObject()
    {
        $expected = '<span><strong>(object)</strong> <i>stdClass()</i> <ul style="list-style: none;"></ul></span>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(new StdClass())));
    }

    public function testIsCommandLine()
    {
        $this->assertTrue(isCommandLine());
    }

    public function testLineNo()
    {
        $this->assertEquals('71', lineNo());
    }

    public function testvalidGuidSuccess()
    {
        $this->assertEquals(1, validGuid('11111111-2222-4333-8444-555555555555'));
    }

    public function testvalidGuidFailure()
    {
        $this->assertEquals(0, validGuid('11111111-2222-3333-4444-555555555555'));
    }

    public function testTodayInDatesSuccess()
    {
        $today = date('Y-m-d');
        $dates = [
            '1970-01-01',
            '1999-12-31',
            '2000-01-01',
            $today
        ];
        $this->assertTrue(todayInDates($dates));
    }

    public function testTodayInDatesFailure()
    {
        $dates = [
            '1970-01-01',
            '1999-12-31',
            '2000-01-01'
        ];
        $this->assertFalse(todayInDates($dates));
    }

    public function testRemoveTodayDate()
    {

        $today = date('Y-m-d');
        $datesWithoutToday = [
            '1970-01-01',
            '1999-12-31',
            '2000-01-01'
        ];
        $datesWithToday = [
            '1970-01-01',
            '1999-12-31',
            $today,
            '2000-01-01'
        ];

        $response = removeTodayDate($datesWithToday);

        $this->assertArraySubset($datesWithoutToday, $response);
    }

    public function testGuidToHexInSql()
    {
        $expected = "unhex(replace('11111111-2222-4333-8444-555555555555','-',''))";
        $this->assertEquals(0, strcmp($expected, guidToHexInSql('11111111-2222-4333-8444-555555555555')));
    }
}