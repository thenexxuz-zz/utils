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

    if (!isCommandLine()) {
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

        echo "<style>
            div.ddd-output {
                background-color: $background;
                color: $textColor;
                border-radius: 10px;
                border: solid silver;
                display: block;
                font-size: 1em;
            }
            div.ddd-header {
                background-color: $textColor;
                color: $background;
                border-bottom: solid silver;
                border-top-left-radius: 7px;
                border-top-right-radius: 7px;
                padding: 10px;
                font-weight: bolder;
            }
            div.ddd-body {
                display: block;
                padding: 10px;
                font-family: monospace;
                overflow: auto;
            }
            .ddd-args {
                clear: both;
                float:left;
                display: block;
                margin-bottom: 10px;
            }
            .ddd-item {
                color: $textColor;
                float:left;
            }
            .ddd-type {
                color: $textColor;
                font-weight: bold;
                float: left;
                margin-right: 10px;
            }
            .ddd-type-string {
                color: red;
                float: left;
                margin-right: 10px;
                float:left;
            }
            .ddd-anchor {
                color: red;
            }
            .ddd-type-integer {
                color: green;
                float:left;
            }
            .ddd-type-double {
                color: brown;
                float:left;
            }
            .ddd-type-boolean {
                color: purple;
                float:left;
            }
            .ddd-type-null {
                color: $textColor;
                float:left;
            }
            .ddd-type-array {
                clear: both;
                float: left;
            }
            .ddd-type-object {
                clear: both;
                float: left;
            }
            .ddd-array-member {
                color: $textColor;
                margin-left: 15px;
                clear: both;
                float:left;
            }
            .ddd-array-key {
                color: $textColor;
                float:left;
                margin-right: 10px;
            }
            .ddd-arrow {
                color: $textColor;
                float:left;
                margin-right: 10px;
            }
            .ddd-arrow:after {
                content: ' \\2192';
            }
            .ddd-info {
                float: left;
            }
            .ddd-array-empty {
                color: red;
            }
            .ddd-public {
                color: green;
            }
            .ddd-protected {
                color: orange;
            }
            .ddd-private {
                color: red;
            }
            .ddd-object-properties, .ddd-object-methods {
                clear: both;
                float: left;
                margin-left: 15px;
            }
            .ddd-object-property, .ddd-object-method {
                clear: both;
                float: left;
                margin-left: 15px;
            }
            .ddd-object-property-visibility, .ddd-object-method-visibility {
                float: left;
                margin-right: 10px;
            }
            .ddd-object-property-name, .ddd-object-method-name {
                float: left;
                margin-right: 10px;
            }
            </style>";
        echo '<div class="ddd-output"><div class="ddd-header">';
    }

    echo "Posted from: " . $file . " line:" . $source['line'] . PHP_EOL . PHP_EOL;

    if (!isCommandLine()) {
        echo '</div><div class="ddd-body">';
    }

    foreach ($args as $X) {
        if (isCommandLine()) {
            print_r($X);
        } else {
            echo "<div class='ddd-args'>" . prettyPrint($X) . "</div>";
        }
    }
    if (!isCommandLine()) {
        echo "</div></div>";
        echo "<script>
            let nodeArray = Array.prototype.slice.call(document.querySelectorAll('.ddd-collapsible .ddd-type-header'));
            
            nodeArray.forEach(_node => {
              // Go up to parent and find UL
              let elUL = _node.parentNode.querySelector('ul');
            
              _node.addEventListener('click', ev => {
                ev.preventDefault();
                ev.stopPropagation();
                elUL.classList.toggle('ddd-hidden');
              })
            });
            </script>";
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
    $result = '';
    if (count(debug_backtrace()) > 253) {
        $result = '<strong><span style="color: black;">Max recursion reached.</span></strong>';
        return $result;
    }
    switch (gettype($X)) {
        case 'string':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(string)</div><div class="ddd-type-string">' . turnUrlIntoAnchor($X) . '</div><div class="ddd-info">(length='.strlen($X).')</div></div></div>';
            break;
        case 'integer':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(int)</div><div class="ddd-type-integer">' . $X . '</div></div></div>';
            break;
        case 'double':
        case 'float':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(double)</div><div class="ddd-type-double">' . $X . '</div></div></div>';
            break;
        case 'boolean':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(boolean)</div><div class="ddd-type-boolean">' . ($X?'true':'false') . '</div></div></div>';
            break;
        case 'NULL':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type-null">NULL</div></div></div>';
            break;
        case 'array':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(array)</div><div class="ddd-info">(size=' . count($X) . ')</div><div class="ddd-type-array">';
            if (count($X) > 0) {
                foreach ($X as $key => $val) {
                    $result .= "<div class='ddd-array-member'><div class='ddd-array-key'>$key</div><div class='ddd-arrow'></div>" . prettyPrint($val) . "</div>";
                }
            } else {
                $result .= "<div class='ddd-array-member'><div class='ddd-array-empty'>Empty Array</div></div>";
            }
            $result .= '</div></div></div>';
            break;
        case 'object':
            $reflect = new ReflectionClass(get_class($X));
            $hoverText = "Name: $reflect->name</br>".($reflect->isInternal() ? '</br>Internal PHP Class':'').($reflect->getExtensionName() ? '</br>Extends: '.$reflect->getExtensionName():'').($reflect->getFileName() ? '</br>Defined: '.$reflect->getFileName():'');

            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(object)</div><div class="ddd-info">' . get_class($X) . '()</div><div class="ddd-type-object">';

            $result .= '<div class="ddd-object-properties"><div class="ddd-object-title">Properties:</div>';
            if (count($reflect->getProperties()) > 0) {
                foreach ($reflect->getProperties() as $property) {
                    $propertyHoverText = str_replace(PHP_EOL, '</br>', $property->getDocComment());
                    $result .= '<div class="ddd-object-property"><div class="ddd-object-property-visibility">';
                    if ($property->isPublic()) {
                        $result .= '<div class="ddd-public">public</div>';
                    } else if ($property->isProtected()) {
                        $result .= '<div class="ddd-protected">protected</div>';
                    } else {
                        $result .= '<div class="ddd-private">private</div>';
                    }
                    $result .= '</div>';
                    $value = null;
                    foreach ((array)$X as $key => $val) {
                        if (($property->name === $key) || (chr(0) . '*' . chr(0) . $property->name === $key)) {
                            $value = $val;
                            break;
                        }
                    }
                    $result .= "<div class='ddd-object-property-name'>$property->name</div><div class='ddd-arrow'></div>" . prettyPrint($value);
                    $result .= '</div>';
                }
            } else {
                $result .= '<div class="ddd-object-property">none</div>';
            }
            $result .= '</div>';

            $result .= '<div class="ddd-object-methods"><div class="ddd-object-title">Methods:</div>';
            foreach ($reflect->getMethods() as $method) {
                if ($method->isPublic() && ($method->name !== ''))  {
                    $result .= "<div class='ddd-object-method'><div class='ddd-object-method-visibility'><div class='ddd-public'>public</div></div><div class='ddd-object-method-name'>$method->name</div><div class='ddd-arrow'></div><div class='ddd-object-method-name'>Method</div></div>";
                }
            }
            foreach ($reflect->getMethods() as $method) {
                if ($method->isProtected() && ($method->name !== ''))  {
                    $result .= "<div class='ddd-object-method'><div class='ddd-object-method-visibility'><div class='ddd-protected'>protected</div></div><div class='ddd-object-method-name'>$method->name</div><div class='ddd-arrow'></div><div class='ddd-object-method-name'>Method</div></div>";
                }
            }
            foreach ($reflect->getMethods() as $method) {
                if ($method->isPrivate() && ($method->name !== ''))  {
                    $result .= "<div class='ddd-object-method'><div class='ddd-object-method-visibility'><div class='ddd-private'>private</div></div><div class='ddd-object-method-name'>$method->name</div><div class='ddd-arrow'></div><div class='ddd-object-method-name'>Method</div></div>";
                }
            }

            $result .= '</div></div></div></div>';
            break;
        default:
            $result .= '';
    }

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

function turnUrlIntoAnchor($text)
{
    // The regex filter for URLs
    $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";

    // Check if there is a url in the text
    if (preg_match($reg_exUrl, $text, $url)) {
        $link = $url[0];
        if (strpos( $url[0], ":" ) === false) {
            $link = 'http://'.$url[0];
        }
        // make the urls anchor tags
        return preg_replace($reg_exUrl, '<a class="ddd-anchor" href="'.$link.'" title="'.$url[0].'" target="_blank">'.$url[0].'</a>', $text);
    }
    // if no urls in the text just return the text
    return $text;
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
