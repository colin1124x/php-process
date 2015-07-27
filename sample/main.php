#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Colin\Process\Manager;

$pool = new Manager();

// 加入一個背景工作
$pool->add(function($payload){

    sleep(2);
    echo "\t\e[35m", 'p1 working...', "\e[m", PHP_EOL;

    // i am a long execute time worker
    sleep(10);
    echo "\t\e[35m", 'p1 ', print_r($payload, 1), "\e[m", PHP_EOL;
    echo "\t\e[35m", 'p1 work finished!!', "\e[m", PHP_EOL;

    return "Hello, word";

}, array('Hello payload...'));

// 加入一個背景工作
$pool->add(function(){

    sleep(2);
    echo "\t\e[35m", 'p2 working...', "\e[m", PHP_EOL;

    sleep(5);

    echo "\t\e[35m", 'p2 work finished!!', "\e[m", PHP_EOL;

    // no return
});

// 執行事件
// 取出最先完成的第一筆
echo "\e[m", 'waiting top 1...', "\e[m", PHP_EOL;
$pool->top(1, function($finish){
    foreach ($finish as $i => $pid) {
        echo "\e[32mtop[{$i}] finish[".print_r($pid, 1)."]\e[m", PHP_EOL;
    }
});

// 主程序繼續主要工作
for ($i = 10; $i > 0; $i--) {
    echo "\e[m", 'main process is running...', $i, PHP_EOL;
}


