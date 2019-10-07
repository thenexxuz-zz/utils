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
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(string)</div><div class="ddd-type-string">test</div><div class="ddd-info">(length=4)</div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint('test', '0', $parent)));
    }

    public function testPrettyPrintInteger()
    {
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(int)</div><div class="ddd-type-integer">1</div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(1, '0', $parent)));
    }

    public function testPrettyPrintDoubleFloat()
    {
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(double)</div><div class="ddd-type-double">1</div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(1.0, '0', $parent)));
    }

    public function testPrettyPrintBoolean()
    {
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(boolean)</div><div class="ddd-type-boolean">true</div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(true, '0', $parent)));
    }

    public function testPrettyPrintNull()
    {
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type-null">NULL</div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(null, '0', $parent)));
    }

    public function testPrettyPrintArray()
    {
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-item-header"><div class="ddd-type">(array)</div><div class="ddd-info">(size=0)</div></div><div class="ddd-type-array ddd-collapsible ddd-hidden"><div class=\'ddd-array-member\'><div class=\'ddd-array-empty\'>Empty Array</div></div></div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint([], '0', $parent)));
    }

    public function testPrettyPrintObject()
    {
        $parent = [];
        $expected = '<div class="ddd-item"><div class="ddd-value"><div class="ddd-item-header"><div class="ddd-type">(object)</div><div class="ddd-info">stdClass()</div></div><div class="ddd-type-object ddd-collapsible ddd-hidden"><div class="ddd-object-properties"><div class="ddd-object-title">Properties:</div></div><div class="ddd-object-methods"><div class="ddd-object-title">Methods:</div><div class="ddd-object-method ddd-collapsible ddd-hidden">none</div></div></div></div></div>';
        $this->assertEquals(0, strcmp($expected, prettyPrint(new StdClass(), '0', $parent)));
    }

    public function testIsCommandLine()
    {
        $this->assertTrue(isCommandLine());
    }

    public function testLineNo()
    {
        $this->assertEquals('78', lineNo());
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