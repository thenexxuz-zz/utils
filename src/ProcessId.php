<?php

namespace AiBUY\ProcessId;

use Exception;

class ProcessId
{
    /**
     * Path to PID file.
     *
     * @var string
     */
    protected $pidPath = '/tmp/';

    protected $scriptName;

    /**
     * Returns path to PID file
     *
     * @return string
     */
    public function getPidPath(): string
    {
        return $this->pidPath;
    }

    /**
     * Sets PID file path
     *
     * @param string $pidPath
     */
    public function setPidPath(string $pidPath): void
    {
        $this->pidPath = $pidPath;
    }

    /**
     * Returns configurable part of PID file name
     *
     * @return string
     */
    public function getScriptName(): string
    {
        return $this->scriptName;
    }

    /**
     * Sets configurable part of PID file name
     *
     * @param string $scriptName
     */
    public function setScriptName(string $scriptName): void
    {
        $this->scriptName = $scriptName;
    }

    public function __construct($scriptName = 'script')
    {
        $this->setScriptName($scriptName);
    }

    /**
     * Sets lock file to prevent from running multiple times.
     *
     * @return void|Exception
     *
     * @throws Exception
     */
    public function setLock()
    {
        if($this->isRunning()) {
            throw new Exception('This script is already running please stop the current process before starting a new one');
        } else {
            $this->createPid();
        }
    }

    /**
     * .Delete PID file now that the script is done
     *
     * @return void
     */
    public function releaseLock()
    {
        unlink($this->pidPath . getmyuid() . '-' . $this->getScriptName() . '.pid');
    }

    /**
     *  Checks if process ID within PID file is still running.
     *
     * @return boolean
     */
    public function isRunning()
    {
        $pidFile = $this->pidPath . getmyuid() . '-' . $this->getScriptName() . '.pid';
        // Checks if PID file exists
        if(file_exists($pidFile)) {
            $file = fopen($pidFile,'r');
            $currentPid = fread($file, filesize($pidFile));
            fclose($file);
            // Checks if the process in the PID file is still running.
            $awkCmd = 'ps aux | awk "/'.trim($currentPid).'/ && !/awk/"';
            exec($awkCmd,$output);
            if(is_array($output) && !empty($output)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates PID file.
     *
     * @return void
     */
    private function createPid()
    {
        $pidFile = $this->pidPath . getmyuid() . '-' . $this->getScriptName() . '.pid';
        $file = fopen($pidFile,'w');
        fwrite($file,getmypid());
        fclose($file);
    }
}