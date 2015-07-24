# colin/process

PHP 子程序控制物件

### How to use

```php

require __DIR__.'/../vendor/autoload.php';

use Colin\Process;

$process = new Process(function($payload){
    return "Hello, {$payload}";
}, 'word');

$process
    ->on(Process::EVENT_START, function(){echo 'fork start:', __LINE__, PHP_EOL;})
    ->on(Process::EVENT_CHILD_WORK_START, function(){echo 'work start:', __LINE__, PHP_EOL;})
    ->on(Process::EVENT_CHILD_WORK_END, function(){echo 'work end:', __LINE__, PHP_EOL;});

$process->exec(function($result){
    echo ">>> [{$result}] <<<", PHP_EOL;
});

```

### sample

[sample]


[sample]:sample/main.php