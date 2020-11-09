# TheNexxuz Utils - Helpers

[![Build Status](https://travis-ci.com/thenexxuz-inc/utils.svg)](https://travis-ci.com/thenexxuz/utils)
![PHP from Packagist](https://img.shields.io/packagist/php-v/thenexxuz/utils)
![Packagist Version](https://img.shields.io/packagist/v/thenexxuz/utils)
![Packagist](https://img.shields.io/packagist/l/thenexxuz/utils)

## Helpers
A few common helpers for debugging.

### ddd
Similar to Laravel's `dd()` command but not reliant on any Laravel packages with configurable color modes: `light`/`dark`/`monokai`/`custom` just add environment variable `DEBUG_COLOR_MODE` or query parameter to the URL `?debug_color_mode` selecting color mode. When the color mode `custom` is selected then the following additional environment variables are needed and any valid CSS color value can be used: `white`, `#abc`, `#ff00ff`, `rgba(45, 36, 145, 0.75)` 
```
DDD_COLOR_BACKGROUND
DDD_COLOR_TEXT_COLOR
DDD_COLOR_TYPE_STRING
DDD_COLOR_TYPE_INTEGER
DDD_COLOR_TYPE_DOUBLE
DDD_COLOR_TYPE_BOOLEAN
DDD_COLOR_ARRAY_EMPTY
DDD_COLOR_VISIBILITY_PUBLIC
DDD_COLOR_VISIBILITY_PROTECTED
DDD_COLOR_VISIBILITY_PRIVATE
DDD_COLOR_INFO
```


### lineNo
just return the line number of where it was called.

### validGuid
simple UUIDv4 validation.

### todayInDates
pass an array of dates in `yyyy-mm-dd` format and returns true or false if today's date is in the array.

### removeTodayDate
pass an array of dates in `yyyy-mm-dd` format and returns and array removing today's date if it's in the array.

### guidToHexInSql
create the correct MySQL string to convert a UUID to BIN(16) hex.

#### Testing

Run the tests with `vendor/bin/phpunit --coverage-html build/coverage-report`

#### Contributing

Please try to use GitFlow. [More information here.](https://nvie.com/posts/a-successful-git-branching-model/]) [And here.](https://support.gitkraken.com/git-workflows-and-extensions/git-flow/)

  * Fork it! 
  * Create your feature branch: `git checkout -b feature/my-new-feature` 
  * Commit your changes: `git commit -am 'Add some feature'` 
  * Push to the branch: `git push origin feature/my-new-feature`
  * Submit a pull request!

#### License
[GPL v2](LICENSE)
