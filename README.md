# TheNexxuz Utils

[![Build Status](https://travis-ci.com/thenexxuz/utils.svg)](https://travis-ci.com/thenexxuz/utils)
![PHP from Packagist](https://img.shields.io/packagist/php-v/thenexxuz/utils)
![Packagist Version](https://img.shields.io/packagist/v/thenexxuz/utils)
![Packagist](https://img.shields.io/packagist/l/thenexxuz/utils)

## Helpers

See the [helper readme](README-helpers.md)

## Classes
### MeasureTime

Use this class to measure response time when debugging or to include time in your API responses.

#### Usage
```
$time = new MeasureTime();

/* Do some stuff */

echo $time->mark(); /* Total time since $time was instantiated */

/* Do more stuff */

echo $time->markInterval(); /* Time since last mark()  */

/* Do even more stuff */

echo $time->mark(); /* Total time since $time was instantiated */
```

### ProcessId

Create a PID file and lock out running the script multiple times. 

#### Installation

```composer require thenexxuz/utils```

#### Usage

Add `use TheNexxuz\Utils\ProcessId;` to your file.

Within your code add the following to the start of your script.
```
$pid = new ProcessId('myScriptName');
$pid->setLock();
```
or
```
$pid = new ProcessId();
$pid->setScriptName('myScriptName');
$pid->setLock();
```
If no name is given then `script` is used.

You can check if the process is running (boolean returned).
```
$pid->isRunning()
```

Add the following to the end of your script.
```
$pid->releaseLock();
```

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
