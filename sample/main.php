#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Colin\Process;
use Colin\ProcessPool;

$pool = new ProcessPool();

// 建構式傳入 Work process
$process_1 = new Process(function($payload){

    echo "\e[35m", 'p1 working...', "\e[m", PHP_EOL;

    // i am a long execute time worker
    sleep(10);

    echo "\e[35m", 'p1 work finished!!', "\e[m", PHP_EOL;

    return "Hello, {$payload}";

}, 'word');

$process_2 = new Process(function(){
    sleep(5);
});

$process_1
    // 註冊程序啟動事件
    ->on(Process::EVENT_START, function(){echo "\e[33m", 'fork start:', __LINE__, "\e[m", PHP_EOL;})
    // 註冊工作開始事件
    ->on(Process::EVENT_CHILD_WORK_START, function(){sleep(1);echo "\e[33m", 'p1 event: work start:', __LINE__, "\e[m", PHP_EOL;})
    // 註冊工作結束事件
    ->on(Process::EVENT_CHILD_WORK_END, function(){echo "\e[33m", 'p1 event: work end:', __LINE__, "\e[m", PHP_EOL;});

// 添加到程序池
$pool->add($process_1);
$pool->add($process_2);

// 執行事件
$process_2->exec();
$process_1->exec(function($result){
    echo ">>> p1 result [{$result}] <<<", PHP_EOL;
});

echo "\e[32m p1 pid = {$process_1->pid()}\e[m", PHP_EOL;
echo "\e[32m p2 pid = {$process_2->pid()}\e[m", PHP_EOL;

// 主程序繼續主要工作
for ($i = 10; $i > 0; $i--) {
    echo "\e[m", 'main process is running...', $i, PHP_EOL;
}

// 取出最先完成的第一筆
echo "\e[m", 'waiting top 1...', "\e[m", PHP_EOL;
$pool->top(1, function($processes){
    foreach ($processes as $i => $p) {
        echo "\e[32mtop[{$i}] finish[{$p->pid()}]\e[m", PHP_EOL;
    }
});
