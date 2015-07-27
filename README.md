# colin/process

PHP 子程序控制物件

### How to use

#### 簡單範例

```php

require __DIR__.'/../vendor/autoload.php';

use Colin\Process\Manager;

$pool = new Manager();

$pool->add($callable_worker, $params);

// 開出子程序跑 $callable_work ...
$p->exec();

// or 如果需要工作程序的回傳值
$p->exec(function($ret){
    // 就是他了...
});

// 主程序繼續往下執行

```

#### 詳細範例

[sample]

[sample]:sample/main.php