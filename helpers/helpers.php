<?php

/**
 * Adds file name and line number to dd output
 * @param mixed ...$args
 */
function ddd(...$args)
{
    $trace = debug_backtrace();
    $source = $trace[0];
    $file = $source['file'];

    echo "Posted from: " . $file . " line:" . $source['line'] . PHP_EOL . PHP_EOL;

    foreach ($args as $X) {
        if (isCommandLine()) {
            print_r($X);
        } else {
            echo "<ul style='font-family: monospace;'>" . prettyPrint($X) . "</ul>";
        }
    }

    terminate(1);
}

function terminate($code)
{
    if (getenv('API_ENV') !== 'testing') {
        die($code);
    }
}

function prettyPrint($X)
{
    $result = '<span>';
    switch (gettype($X)) {
        case 'string':
            $result .= '<strong>(string)</strong> <span style="color:red;">' . $X . '</span> <i>(length='.strlen($X).')</i>';
            break;
        case 'integer':
            $result .= '<strong>(int)</strong> <span style="color: green;">' . $X . '</span>';
            break;
        case 'double':
        case 'float':
            $result .= '<strong>(double)</strong> <span style="color: brown;">' . $X . '</span>';
            break;
        case 'boolean':
            $result .= '<strong>(boolean)</strong> <span style="color: purple;">' . ($X?'true':'false') . '</span>';
            break;
        case 'NULL':
            $result .= '<strong><span style="color: black;">NULL</span></strong>';
            break;
        case 'array':
            $result .= '<strong>(array)</strong> (size=' . count($X) . ') <ul style="list-style: none;">';
            foreach ($X as $key => $val) {
                $result .= "<li>$key => " . prettyPrint($val) . "</li>";
            }
            $result .= '</ul>';
            break;
        case 'object':
            $result .= '<strong>(object)</strong> <i>' . get_class($X) . '()</i> <ul style="list-style: none;">';
            foreach ((array) $X as $key => $val) {
                $result .= '<li><i>';
                if (gettype($key) === 'string' && (strcmp(substr($key, 0, 3), chr(0).'*'.chr(0)) === 0)) {
                    $result .= 'protected ';
                    $key = substr($key, 3);
                } else {
                    $result .= 'public';
                }
                $result .= "</i> '$key' => " . prettyPrint($val) . "</li>";
            }
            $result .= '</ul>';
            break;
        default:
            $result .= '';
    }
    $result .= '</span>';

    return $result;
}

/**
 * Determines if script is command line or not
 *
 * @return bool
 */
function isCommandLine()
{
    return php_sapi_name() === 'cli';
}

/**
 * Returns current line number
 * @return mixed
 */
function lineNo()
{
    $trace = debug_backtrace();
    $source = $trace[0];
    return $source['line'];
}

/**
 * Validates de value is a string containing a guid
 * @param $value
 * @return false|int
 */
function validGuid($value)
{
    $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
    return preg_match($UUIDv4, $value);
}

/**
 * Receives an array with dates in format yyyy-mm-dd and validate includes today date
 * @param $dates
 * @return bool
 */
function todayInDates($dates)
{
    $today = date("Y-m-d");
    return in_array($today, $dates);
}

/**
 * Receives an array with dates in format yyyy-mm-dd and returns same array without today's date
 *
 * @param $dates
 * @return array
 */
function removeTodayDate($dates)
{
    $today = date("Y-m-d");
    $newDates = [];
    foreach ($dates as $date) {
        if ($date !== $today) {
            $newDates[] = $date;
        }
    }
    return $newDates;
}

/**
 * Return a string to be used in a query where guid is in human readable format and need to be
 * used in a filter as an binary guid
 *
 * @param $guid
 * @return string
 */
function guidToHexInSql($guid)
{
    return "unhex(replace('$guid','-',''))";
}
