# ProcessId

Create a PID file and lock out running the script multiple times. 

![PHP from Packagist](https://img.shields.io/packagist/php-v/aibuy/process-id) ![Packagist Version](https://img.shields.io/packagist/v/aibuy/process-id) ![Travis (.org)](https://img.shields.io/travis/aibuy/process-id) ![Packagist](https://img.shields.io/packagist/l/aibuy/process-id)

## Installation

```composer require aibuy/process-id```

## Usage

Add `use AiBuy\ProcessId\ProcessId;` to your file.

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

## Testing

Run the tests with `vendor/bin/phpunit --coverage-html build/coverage-report`

## Contributing

Please try to use GitFlow. [More information here.](https://nvie.com/posts/a-successful-git-branching-model/]) [And here.](https://support.gitkraken.com/git-workflows-and-extensions/git-flow/)

  * Fork it! 
  * Create your feature branch: `git checkout -b feature/my-new-feature` 
  * Commit your changes: `git commit -am 'Add some feature'` 
  * Push to the branch: `git push origin feature/my-new-feature`
  * Submit a pull request!

## License
[GPL v2](LICENSE)