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
        $mode = getenv('DEBUG_COLOR_MODE') ? getenv('DEBUG_COLOR_MODE') : 'dark';
        if (array_key_exists('debug_color_mode', $_GET)) {
            $mode = $_GET['debug_color_mode'];
        }
        $white = '#ffffff';
        $black = '#000000';
        $veryLightGray = '#f2f2f2';
        $lightGray = '#908e8f';
        $gray = '#5d5d5d';
        $darkGray = '#0e0e0e';
        $red = '#ff0000';
        $green = '#00ff00';
        $blue = '#0000ff';
        $brown = '#a52a2a';
        $purple = '#800080';
        $orange = '#fc9867';
        $yellow = '#ffd866';
        $pink = '#ff6188';
        $lime = '#a9dc76';
        $lightBlue = '#78dce8';
        $lavender = '#ab9df2';
        $animatedHotdog = '#ff0000';

        $animatedHotdogKeyframes = "
            @keyframes animatedHotdog {
                0% {
                    background: $red;
                }
                100% {
                    background: $yellow;
                }
            }";

        switch ($mode) {
            case 'custom':
                $background = getenv('DDD_COLOR_BACKGROUND');
                $textColor = getenv('DDD_COLOR_TEXT_COLOR');
                $typeString = getenv('DDD_COLOR_TYPE_STRING');
                $typeInteger = getenv('DDD_COLOR_TYPE_INTEGER');
                $typeDouble = getenv('DDD_COLOR_TYPE_DOUBLE');
                $typeBoolean = getenv('DDD_COLOR_TYPE_BOOLEAN');
                $arrayEmpty = getenv('DDD_COLOR_ARRAY_EMPTY');
                $visibilityPublic = getenv('DDD_COLOR_VISIBILITY_PUBLIC');
                $visibilityProtected = getenv('DDD_COLOR_VISIBILITY_PROTECTED');
                $visibilityPrivate = getenv('DDD_COLOR_VISIBILITY_PRIVATE');
                $info = getenv('DDD_COLOR_INFO');
                break;
            case 'light':
                $background = $veryLightGray;
                $textColor = $darkGray;
                $typeString = $red;
                $typeInteger = $blue;
                $typeDouble = $brown;
                $typeBoolean = $purple;
                $arrayEmpty = $red;
                $visibilityPublic = $blue;
                $visibilityProtected = $orange;
                $visibilityPrivate = $red;
                $info = $textColor;
                break;
            case 'hotdogStand':
                $background = $animatedHotdog;
                $textColor = $white;
                $typeString = $black;
                $typeInteger = $green;
                $typeDouble = $orange;
                $typeBoolean = $purple;
                $arrayEmpty = $red;
                $visibilityPublic = $green;
                $visibilityProtected = $orange;
                $visibilityPrivate = $black;
                $info = $yellow;
                break;
            default:
            case 'dark':
                $background = $darkGray;
                $textColor = $white;
                $typeString = $red;
                $typeInteger = $green;
                $typeDouble = $brown;
                $typeBoolean = $purple;
                $arrayEmpty = $red;
                $visibilityPublic = $green;
                $visibilityProtected = $orange;
                $visibilityPrivate = $red;
                $info = $lightGray;
                break;
            case 'monokai':
                $background = $darkGray;
                $textColor = $white;
                $typeString = $green;
                $typeInteger = $yellow;
                $typeDouble = $lavender;
                $typeBoolean = $purple;
                $arrayEmpty = $red;
                $visibilityPublic = $green;
                $visibilityProtected = $orange;
                $visibilityPrivate = $red;
                $info = $lavender;
                break;
        }

        echo "<style>
            .ddd-info,
            .ddd-value,
            .ddd-item,
            .ddd-arrow,
            .ddd-method-name,
            .ddd-public,
            .ddd-protected,
            .ddd-private,
            .ddd-array-key,
            .ddd-object-method-name,
            .ddd-object-method-params,
            .ddd-object-method-visibility,
            .ddd-object-property-name,
            .ddd-object-property-visibility,
            .ddd-type,
            .ddd-type-integer,
            .ddd-type-string,
            .ddd-type-double,
            .ddd-type-object,
            .ddd-type-array,
            .ddd-type-boolean,
            .ddd-type-null,
            .ddd-type-member,
            .ddd-type-key {
                display: inline-block;
                vertical-align: top;
            }
            .ddd-output {
                background-color: $background;
                color: $textColor;
                border-radius: 10px;
                border: solid silver;
                display: block;
                font-size: 1em;
                " . (($mode === 'hotdogStand')? 'animation: animatedHotdog 3s infinite alternate;': '') . "
            }
            " . (($mode === 'hotdogStand')? $animatedHotdogKeyframes: '') . "
            .ddd-header {
                background-color: $textColor;
                color: $background;
                border-bottom: solid silver;
                border-top-left-radius: 7px;
                border-top-right-radius: 7px;
                padding: 10px;
                font-weight: bolder;
            }
            .ddd-body {
                display: block;
                padding: 10px;
                font-family: monospace;
                overflow: auto;
                background-color: inherit;
            }
            .ddd-args {
                display: block;
                margin-bottom: 10px;
                background-color: inherit;
            }
            .ddd-item {
                color: $textColor;
                background-color: inherit;
            }
            .ddd-value {
                background-color: inherit;
            }
            .ddd-type {
                color: $textColor;
                font-weight: bold;
            }
            .ddd-type-string {
                color: $typeString;
            }
            .ddd-anchor {
                color: $typeString;
            }
            .ddd-type-integer {
                color: $typeInteger;
            }
            .ddd-type-double {
                color: $typeDouble;
            }
            .ddd-type-boolean {
                color: $typeBoolean;
            }
            .ddd-type-null {
                color: $textColor;
            }
            .ddd-type-array {
                display: block;
            }
            .ddd-type-object {
                display: block;
            }
            .ddd-array-member {
                color: $textColor;
                margin-left: 15px;
                display: block;
            }
            .ddd-array-key {
                color: $textColor;
            }
            .ddd-arrow {
                color: $textColor;
            }
            .ddd-arrow:after {
                content: ' \\2192';
            }
            .ddd-info {
                color: $info;
            }
            .ddd-array-empty {
                color: $arrayEmpty;
            }
            .ddd-public {
                color: $visibilityPublic;
            }
            .ddd-protected {
                color: $visibilityProtected;
            }
            .ddd-private {
                color: $visibilityPrivate;
            }
            .ddd-abstract {
                color: $visibilityProtected;
            }
            .ddd-static {
                color: $visibilityPrivate;
            }
            .ddd-object-properties, .ddd-object-methods {
                display: block;
                margin-left: 15px;
            }
            .ddd-object-property, .ddd-object-method {
                display: block;
                margin-left: 15px;
            }
            .ddd-type,
            .ddd-info,
            .ddd-type-integer,
            .ddd-type-string,
            .ddd-type-double,
            .ddd-type-object,
            .ddd-type-array,
            .ddd-type-boolean,
            .ddd-type-null,
            .ddd-arrow,
            .ddd-array-key,
            .ddd-object-property-visibility,
            .ddd-object-method-visibility,
            .ddd-object-property-name {
                margin-right: .5rem;
            }
            .ddd-item-header {
                text-decoration: none;
                background-color: inherit;
            }
            .ddd-object-title:hover,  .ddd-item-header:hover {
                text-decoration: underline;
                cursor: pointer;
            }
            .ddd-type, .ddd-info {
                text-decoration: inherit;
                background-color: inherit;
            }
            .ddd-hidden {
                display: none;
            }
            .ddd-max-recursion {
                color: $textColor;
            }
            </style>";
        echo "<script>
            window.ddd = {
                args: [],
                schema: [],
                file: '$file',
                lineNo: '{$source['line']}'
            };
        </script>";
        echo '<div class="ddd-output"><div class="ddd-header">';
    }

    echo "Posted from: " . $file . " line:" . $source['line'] . PHP_EOL . PHP_EOL;

    if (!isCommandLine()) {
        echo '</div><div class="ddd-body">';
    }

    $schema = (object)[];
    foreach ($args as $key => $X) {
        if (isCommandLine()) {
            print_r($X);
        } else {
            echo "<script>window.ddd.args[$key] = " . json_encode($X) . ";</script>" . PHP_EOL;
            echo "<div class='ddd-args'>" . prettyPrint($X, $key, $schema) . "</div>" . PHP_EOL;
            echo "<script>
                window.ddd.schema = " . json_encode($schema) . ";
            </script>" . PHP_EOL;
        }
    }
    if (!isCommandLine()) {
        echo "</div></div>";
        echo "
            <script>
                let _itemHeaders = document.querySelectorAll('.ddd-item-header, .ddd-object-title');
                _itemHeaders.forEach(_itemHeader => {
                    _itemHeader.addEventListener('click', () => {
                        let cssSelector = '.' + _itemHeader.classList.value.split(' ').join('.');
                        let _collapsibles = [].slice.call(_itemHeader.parentNode.querySelectorAll(`.ddd-collapsible:not(\${cssSelector})`)).filter((_item) => {
                            return _item.parentNode === _itemHeader.parentNode;
                        });
                        if (_collapsibles && _collapsibles.length) {
                            _collapsibles.forEach(_collapsible => {
                                if (_collapsible && _collapsible.classList) {
                                    if (_collapsible.classList.contains('ddd-collapsible')) {
                                        if (_collapsible.classList.contains('ddd-hidden')) {
                                            _collapsible.classList.remove('ddd-hidden');
                                        } else {
                                            _collapsible.classList.add('ddd-hidden');
                                        }
                                    }
                                }
                            });
                        }
                    });
                });

                console.log('%c ! window.ddd will give you console access to the data ! ', 'background: #222; color: #bada55');
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

function prettyPrint($X, $parentKey, &$parent)
{
    $self = '';
    $result = '';
    if (count(debug_backtrace()) > 253) {
        $result = '<div class="ddd-max-recursion">Max recursion reached.</div>';
        return $result;
    }
    switch (gettype($X)) {
        case 'string':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(string)</div><div class="ddd-type-string">' . turnUrlIntoAnchor($X) . '</div><div class="ddd-info">(length='.strlen($X).')</div></div></div>';
            $self = 'String';
            break;
        case 'integer':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(int)</div><div class="ddd-type-integer">' . $X . '</div></div></div>';
            $self = 'Integer';
            break;
        case 'double':
        case 'float':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(double)</div><div class="ddd-type-double">' . $X . '</div></div></div>';
            $self = 'Double';
            break;
        case 'boolean':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type">(boolean)</div><div class="ddd-type-boolean">' . ($X?'true':'false') . '</div></div></div>';
            $self = 'Boolean';
            break;
        case 'NULL':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-type-null">NULL</div></div></div>';
            $self = 'NULL';
            break;
        case 'array':
            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-item-header"><div class="ddd-type">(array)</div><div class="ddd-info">(size=' . count($X) . ')</div></div><div class="ddd-type-array ddd-collapsible ddd-hidden">';
            $self = [];
            if (count($X) > 0) {
                foreach ($X as $key => $val) {
                    $result .= "<div class='ddd-array-member ddd-collapsible'><div class='ddd-array-key'>$key</div><div class='ddd-arrow'></div>" . prettyPrint($val, $key, $self) . "</div>";
                }
            } else {
                $result .= "<div class='ddd-array-member'><div class='ddd-array-empty'>Empty Array</div></div>";
            }
            $result .= '</div></div></div>';
            break;
        case 'object':
            $reflect = new ReflectionClass(get_class($X));
            $hoverText = "Name: $reflect->name</br>".($reflect->isInternal() ? '</br>Internal PHP Class':'').($reflect->getExtensionName() ? '</br>Extends: '.$reflect->getExtensionName():'').($reflect->getFileName() ? '</br>Defined: '.$reflect->getFileName():'');

            $className = explode(chr(0), get_class($X))[0];

            $result .= '<div class="ddd-item"><div class="ddd-value"><div class="ddd-item-header"><div class="ddd-type">(object)</div><div class="ddd-info">' . $className . '()</div></div><div class="ddd-type-object ddd-collapsible ddd-hidden">';
            $self = (object) [];

            $result .= '<div class="ddd-object-properties"><div class="ddd-object-title">Properties:</div>';

            if (get_class($X) === 'stdClass') {
                foreach (get_object_vars($X) as $key => $value) {
                    $result .= "<div class='ddd-object-property ddd-collapsible ddd-hidden'><div class='ddd-object-property-visibility'><div class='ddd-public'>public</div></div><div class='ddd-object-property-name'>$key</div><div class='ddd-arrow'></div>" . prettyPrint($value, $key, $self) . "</div>";
                }
            } else {
                if (count($reflect->getProperties()) > 0) {
                    foreach ($reflect->getProperties() as $property) {
                        //$propertyHoverText = str_replace(PHP_EOL, '</br>', $property->getDocComment());
                        $result .= '<div class="ddd-object-property ddd-collapsible ddd-hidden"><div class="ddd-object-property-visibility">';
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
                        $result .= "<div class='ddd-object-property-name'>$property->name</div><div class='ddd-arrow'></div>" . prettyPrint($value, $property->name, $self);
                        $result .= '</div>';
                    }
                } else {
                    $result .= '<div class="ddd-object-property ddd-collapsible ddd-hidden">none</div>';
                }
            }
            $result .= '</div>';

            $result .= '<div class="ddd-object-methods"><div class="ddd-object-title">Methods:</div>';
            if (count($reflect->getMethods()) > 0) {
                foreach ($reflect->getMethods() as $method) {
                    $params = $method->getParameters();
                    usort($params, function($a, $b) {
                        return $a->getPosition() > $b->getPosition();
                    });
                    $parameters = [];
                    foreach ($params as $param) {
                        $parameters[] = $param->getName();
                    }
                    $visibility = '';
                    if ($method->isPublic()) {
                        $visibility = 'public';
                    } elseif ($method->isProtected()) {
                        $visibility = 'protected';
                    } elseif ($method->isPrivate()) {
                        $visibility = 'private';
                    }

                    if ($method->isAbstract()) {
                        $visibility .= ' abstract' ;
                    }
                    if ($method->isStatic()) {
                        $visibility .= ' static';
                    }
                    $parameters = implode(', ', $parameters);
                    $result .= "<div class='ddd-object-method ddd-collapsible ddd-hidden'><div class='ddd-object-method-visibility'><div class='ddd-$visibility'>$visibility</div></div><div class='ddd-object-method-name'>{$method->getName()}</div><div class='ddd-object-method-params'>($parameters)</div></div>";
                }
            } else {
                $result .= '<div class="ddd-object-method ddd-collapsible ddd-hidden">none</div>';
            }
            $result .= '</div></div></div></div>';
            break;
        default:
            $result .= '';
    }

    if (is_array($parent)) {
        $parent[$parentKey] = $self;
    } elseif (is_object($parent)) {
        $parentKey = str_replace(chr(0), '', $parentKey);
        $parent->{$parentKey} = $self;
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
    return "uuid_to_bin('$guid')";
}
