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
                padding: 10px;
                font-family: monospace;
            }
            .ddd-item {
                border: 1px solid silver;
                color: $textColor;
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
            }
            .ddd-anchor {
                color: red;
            }
            .ddd-type-integer {
                color: green;
            }
            .ddd-type-double {
                color: brown;
            }
            .ddd-type-boolean {
                color: purple;
            }
            .ddd-type-null {
                color: $textColor;
            }
            .ddd-type-array {
//                clear: both;
//                float: left;
            }
            .ddd-array-member {
                color: $textColor;
                margin-left: 15px;
            }
            .ddd-array-key {
                color: $textColor;
                float:left;
                margin-right: 10px;
            }
            .ddd-array-arrow {
                color: $textColor;
                float:left;
                margin-right: 10px;
            }
            .ddd-array-arrow:after {
                content: ' \\2192';
            }
            .ddd-info {
            }
            .ddd-array-empty {
                color: red;
            }
            
            
            
            .ddd-collapsible {
                float: left;
                display: contents;
            }
            .ddd-collapsible .ddd-type-header:hover {
                text-decoration: underline;
                cursor: pointer;
            }
            .ddd-list > li {
                margin-left: 15px;
            }
            .ddd-hidden {
                display: none;
            }
            h4.ddd-type-header {
                margin-block-start: 0;
                margin-block-end: 0;
                margin-inline-start: 0;
                margin-inline-end: 0;
                font-weight: normal;
                position: relative;
                display: inline-block;
            }
            .ddd-tooltip {
                position: relative;
                display: inline-block;
            }
                
            .ddd-tooltip .ddd-tooltiptext {
                visibility: hidden;
                background-color: #222222;
                color: #fffbf5;
                border-radius: 6px;
                padding: 5px;
                
                /* Position the tooltip */
                position: absolute;
                z-index: 1;
                top: -5px;
                left: 100%; 
            }
            .ddd-tooltip .ddd-tooltiptext::after {
                content: ' ';
                position: absolute;
                top: 50%;
                right: 100%; /* To the left of the tooltip */
                margin-top: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: transparent black transparent transparent;
            }    
            .ddd-tooltip:hover .ddd-tooltiptext {
                visibility: visible;
            }
            .ddd-list {
                list-style: none;
            }
            .ddd-collapsible > .ddd-list {
                padding-inline-start: 0;
            }
            .ddd-list {
                padding-inline-start: 15px;
            }
            .ddd-public, .ddd-protected, .ddd-private, .ddd-object-property, .ddd-object-method {
                float: left;
            }
            .ddd-object-property {
                margin-left: 0;
            }
            .ddd-object-method {
                margin-left: 15px;
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
            echo "<ul class='ddd-list'>" . prettyPrint($X) . "</ul>";
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
                    $result .= "<div class='ddd-array-member'><div class='ddd-array-key'>$key</div><div class='ddd-array-arrow'></div>" . prettyPrint($val) . "</div>";
                }
            } else {
                $result .= "<div class='ddd-array-member'><div class='ddd-array-empty'>Empty Array</div></div>";
            }
            $result .= '</div></div></div>';
            break;
        case 'object':
            $reflect = new ReflectionClass(get_class($X));
            $hoverText = "Name: $reflect->name</br>".($reflect->isInternal() ? '</br>Internal PHP Class':'').($reflect->getExtensionName() ? '</br>Extends: '.$reflect->getExtensionName():'').($reflect->getFileName() ? '</br>Defined: '.$reflect->getFileName():'');

            $result .= '<div class="ddd-collapsible"><ul class="ddd-list"><h4 class="ddd-type-header ddd-tooltip"><strong>(object)&nbsp;</strong><i>' . get_class($X) . '()</i><div class="ddd-tooltiptext">'.$hoverText.'</div></h4>';
            $result .= '<ul class="ddd-hidden ddd-list"><div class="ddd-collapsible"><h4 class="ddd-hidden ddd-type-header"><strong>Properties:</strong></h4><li><ul class="ddd-hidden ddd-list">';

            if (count($reflect->getProperties()) > 0) {
                foreach ($reflect->getProperties() as $property) {
                    $propertyHoverText = str_replace(PHP_EOL, '</br>', $property->getDocComment());
                    $result .= '<li><div class="ddd-object-property ' . (($propertyHoverText !== '') ? 'ddd-tooltip' : '') . '"><i>';
                    if ($property->isProtected()) {
                        $result .= '<div class="ddd-protected">protected&nbsp;</div>';
                    } else if ($property->isPrivate()) {
                        $result .= '<div class="ddd-private">private&nbsp;</div>';
                    } else {
                        $result .= '<div class="ddd-public">public&nbsp;</div>';
                    }
                    $value = null;
                    foreach ((array)$X as $key => $val) {
                        if (($property->name === $key) || (chr(0) . '*' . chr(0) . $property->name === $key)) {
                            $value = $val;
                            break;
                        }
                    }
                    $result .= "</i> '$property->name' =>&nbsp; " . (($propertyHoverText !== '') ? "<div class='ddd-tooltiptext'>" . $propertyHoverText . "</div>" : '') . "</div>" . prettyPrint($value) . " </li>";
                }
            } else {
                $result .= '<li><div class="ddd-object-property"><i><div class="ddd-private">none </div> </i> </div> </li>';
            }
            $result .= '</ul></li><div class="ddd-collapsible"><h4 class="ddd-hidden ddd-type-header"><strong>Methods:</strong></h4><ul class="ddd-hidden ddd-list">';
            foreach ($reflect->getMethods() as $method) {
                if ($method->isPublic() && ($method->name !== ''))  {
                    $result .= '<li><div class="ddd-object-method"><i>';
                    $result .= '<div class="ddd-public">public&nbsp;</div>';
                    $result .= "</i>'$method->name' =>&nbsp;</div>Method</li>";
                }
            }
            foreach ($reflect->getMethods() as $method) {
                if ($method->isProtected() && ($method->name !== ''))  {
                    $result .= '<li><div class="ddd-object-method"><i>';
                    $result .= '<div class="ddd-protected">protected&nbsp;</div> ';
                    $result .= "</i>'$method->name' =>&nbsp;</div>Method</li>";
                }
            }
            foreach ($reflect->getMethods() as $method) {
                if ($method->isPrivate() && ($method->name !== ''))  {
                    $result .= '<li><div class="ddd-object-method"><i>';
                    $result .= '<div class="ddd-private">private&nbsp;</div> ';
                    $result .= "</i>'$method->name' =>&nbsp;</div>Method</li>";
                }
            }
            $result .= '</ul></ul></ul></div>';
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
