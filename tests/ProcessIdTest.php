<?php

use AiBUY\Utils\ProcessId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AiBUY\Utils\ProcessId
 * @uses \AiBUY\Utils\ProcessId
 */
class ProcessIdTest extends TestCase
{
    /**
     * @return ProcessId
     */
    public function test__construct()
    {
        $pid = new ProcessId();
        $this->assertContainsOnlyInstancesOf(ProcessId::class, [$pid]);

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends test__construct
     *
     * @return ProcessId
     */
    public function testGetPidPath(ProcessId $pid)
    {
        $this->assertSame('/tmp/', $pid->getPidPath());
        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testGetPidPath
     *
     * @return ProcessId
     */
    public function testSetPidPath(ProcessId $pid)
    {
        $pid->setPidPath('./');
        $this->assertSame('./', $pid->getPidPath());

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testSetPidPath
     *
     * @return ProcessId
     */
    public function testGetScriptName(ProcessId $pid)
    {
        $this->assertSame('script', $pid->getScriptName());

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testGetScriptName
     *
     * @return ProcessId
     */
    public function testSetScriptName(ProcessId $pid)
    {
        $pid->setScriptName('test-script');
        $this->assertSame('test-script', $pid->getScriptName());

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testSetScriptName
     *
     * @return ProcessId
     *
     * @throws Exception
     */
    public function testSetLockSuccess(ProcessId $pid)
    {
        $pid->setLock();
        $this->assertFileExists($pid->getPidPath() . getmyuid() . '-' . $pid->getScriptName() . '.pid');

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testSetLockSuccess
     *
     * @throws Exception
     */
    public function testSetLockFailure(ProcessId $pid)
    {
        $this->expectException(\Exception::class);
        $pid->setLock();
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testSetLockSuccess
     *
     * @return ProcessId
     */
    public function testIsRunningSuccess(ProcessId $pid)
    {
        $this->assertTrue($pid->isRunning());

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testIsRunningSuccess
     *
     * @return ProcessId
     */
    public function testReleaseLock(ProcessId $pid)
    {
        $pid->releaseLock();
        $this->assertFileNotExists($pid->getPidPath() . getmyuid() . '-' . $pid->getScriptName() . '.pid');

        return $pid;
    }

    /**
     * @param ProcessId $pid
     *
     * @depends testReleaseLock
     *
     * @return ProcessId
     */
    public function testIsRunningFailure(ProcessId $pid)
    {
        $this->assertFalse($pid->isRunning());

        return $pid;
    }
}
