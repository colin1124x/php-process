#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Colin\Process;

// 建構式傳入 Work process
$process = new Process(function($payload){
    // i am a long execute time worker

    echo 'working...', PHP_EOL;

    sleep(5);

    echo 'work finished!!', PHP_EOL;

    return "Hello, {$payload}";

}, 'word');

$process
    // 註冊程序啟動事件
    ->on(Process::EVENT_START, function(){echo 'fork start:', __LINE__, PHP_EOL;})
    // 註冊工作開始事件
    ->on(Process::EVENT_CHILD_WORK_START, function(){echo 'work start:', __LINE__, PHP_EOL;})
    // 註冊工作結束事件
    ->on(Process::EVENT_CHILD_WORK_END, function(){echo 'work end:', __LINE__, PHP_EOL;});

// 執行事件
$process->exec(function($result){
    echo ">>> [{$result}] <<<", PHP_EOL;
});

// 主程序繼續主要工作
for ($i = 10; $i > 0; $i--) {
    echo 'main process is running...', $i, PHP_EOL;
}
