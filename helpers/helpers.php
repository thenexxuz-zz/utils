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
    if (!isCommandLine()) {
        echo <<<JAVASCRIPT
<script>
let nodeArray = Array.prototype.slice.call(document.querySelectorAll("span > div.collapsible > h4"));

nodeArray.forEach(_node => {
  // Go up to parent and find UL
  let elUL = _node.parentNode.querySelector('ul');

  _node.addEventListener('click', ev => {
    ev.preventDefault();
    ev.stopPropagation();
    elUL.classList.toggle('hidden')
    // Toggle Code
  })
});

</script>
JAVASCRIPT;

        // Configurable light/dark modes
        $mode = getenv('DEBUG_COLOR_MODE') ? getenv('DEBUG_COLOR_MODE') : 'light';
        if (array_key_exists('debug_color_mode', $_GET)) {
            $mode = $_GET['debug_color_mode'];
        }

        switch ($mode) {
            case 'light':
                $background = '#ffffff';
                $textColor = '#0e0e0e';
                break;
            case 'dark':
                $background = '#0e0e0e';
                $textColor = '#ffffff';
                break;
            default:
                $background = ($mode === 'light')? '#ffffff' : '#0e0e0e';
                $textColor = ($mode === 'light')? '#0e0e0e' : '#ffffff';
                break;
        }

        echo <<<STYLE
<style>
html {
    background-color: $background;
    color: $textColor;
}
.type {
    font-weight: bold;
    float: left;
}
.collapsible > h4:hover {
    text-decoration: underline;
}
.hidden {
    display: none;
}
h4 {
    display: block;
    margin-block-start: 0;
    margin-block-end: 0;
    margin-inline-start: 0;
    margin-inline-end: 0;
    font-weight: normal;
}
</style>
STYLE;
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
    if (count(debug_backtrace()) > 253) {
        $result = '<strong><span style="color: black;">Max recursion reached.</span></strong>';
        return $result;
    }
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
            $result .= '<strong><span style="color: inherit;">NULL</span></strong>';
            break;
        case 'array':
            $result .= '<div class="collapsible"><h4><strong>(array)</strong> (size=' . count($X) . ')</h4> <ul class="hidden" style="list-style: none;">';
            foreach ($X as $key => $val) {
                $result .= "<li>$key => " . prettyPrint($val) . "</li>";
            }
            $result .= '</ul></div>';
            break;
        case 'object':
            $result .= '<div class="collapsible"><h4><strong>(object)</strong> <i>' . get_class($X) . '()</i></h4> <ul class="hidden" style="list-style: none;">';
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
            $result .= '</ul></div>';
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
