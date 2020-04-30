## Laravel SLS

### 安装

```shell
# 安装
$ composer require seffeng/laravel-sls
```

##### Laravel

```php
# 1、生成配置文件
$ php artisan vendor:publish --tag="sls"

# 2、修改配置文件 /config/sls.php 或在 /.env 文件中添加配置
SLS_ACCESS_KEY_ID=
SLS_ACCESS_KEY_SECRET=
SLS_ENDPOINT=
SLS_PROJECT=
SLS_LOG_STORE=
#SLS_TOPIC=  #可选
#SLS_SOURCE=  #可选

# 3、修改 /config/logging.php 配置，channels 中增加 sls，以下方式二选一；
    
## 3.1 修改 /.env 中 LOG_CHANNEL 为 stack，stack.channels 增加 sls
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'sls'],  //增加 'sls'
            'ignore_exceptions' => false,
        ],
        ......

        'sls' => [
            'driver' => 'monolog',
            'handler' => Seffeng\LaravelSLS\Handler\SLSHandler::class,
            'level'  => 'debug',
        ],

## 3.2 修改 /.env 中 LOG_CHANNEL 为 ssl
    'channels' => [
        ......

        'sls' => [
            'driver' => 'daily',
            'level'  => 'debug',
            'path' => storage_path('logs/laravel.log'),
            'tap'  => [Seffeng\LaravelSLS\Logging\SLSFormatter::class],
            'days' => 14,
        ],
```

##### lumen

```php
# 1、将以下代码段添加到 /bootstrap/app.php 文件中的 Providers 部分
$app->register(Seffeng\LaravelSLS\SLSServiceProvider::class);

# 2、参考扩展包内 config/sls.php 在 /.env 文件中添加配置
SLS_ACCESS_KEY_ID=
SLS_ACCESS_KEY_SECRET=
SLS_ENDPOINT=
SLS_PROJECT=
SLS_LOG_STORE=
#SLS_TOPIC=  #可选
#SLS_SOURCE=  #可选

# 3、使用 Log::info() 方式时需增加配置文件/config/logging.php，channels 中增加 sls,参考文件/vendor/laravel/lumen-framework/config/logging.php，以下方式二选一；

## 3.1 修改 /.env 中 LOG_CHANNEL 为 stack，stack.channels 增加 sls
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'sls'],  //增加 'sls'
        ],
        ......

        'sls' => [
            'driver' => 'monolog',
            'handler' => Seffeng\LaravelSLS\Handler\SLSHandler::class,
            'level'  => 'debug',
        ],

## 3.2 修改 /.env 中 LOG_CHANNEL 为 ssl，
    'channels' => [
        ......

        'sls' => [
            'driver' => 'daily',
            'level'  => 'debug',
            'path' => storage_path('logs/lumen.log'),
            'tap'  => [Seffeng\LaravelSLS\Logging\SLSFormatter::class],
            'days' => 14,
        ],
```

### 目录说明

```
├─config
│   sls.php
├─src
│  │  SLSLog.php
│  │  SLSServiceProvider.php
│  │  Writer.php
│  ├─Facades
│  │    SLSLog.php
│  │    Writer.php
│  ├─Handler
│  │    SLSHandler.php
│  ├─Helpers
│  │    ArrayHelper.php
│  └─Logging
│       SLSContentFormatter.php
│       SLSFormatter.php
└─tests
    LogTest.php
```

### 示例

```php
# 1、如果控制器直接抛出符合 App\Exceptions\Handler->report() 的异常，则仅需配置logging.php，不需额外代码，该方法默认会写 error 日志；$logger->error($e->getMessage(),...)

# 2、App\Exceptions\Handler 在 /bootstrap/app.php 查看。
```

```php
/**
 * 参考 tests/LogTest.php
 */

use Illuminate\Support\Facades\Log;
use Seffeng\LaravelSLS\Facades\SLSLog;
use Seffeng\LaravelSLS\Facades\Writer;

class SiteController extends Controller
{
    public function index()
    {
        // 使用方式，建议最后一种
        $mode = 2;
        if ($mode === 1) {
            // 仅写到阿里云日志，内容支持多条：[['username' => 'admin', 'action' => 'create user.111'], ['username' => 'admin', 'action' => 'delete user.111']]
            app('sls')->putLogs(['username' => 'admin', 'action' => 'create user.111']);
        } elseif ($mode === 2) {
            // 仅写到阿里云日志，内容支持多条：[['username' => 'admin', 'action' => 'create user.111'], ['username' => 'admin', 'action' => 'delete user.111']]
            SLSLog::putLogs([['username' => 'admin', 'action' => 'create user.111'], ['username' => 'admin', 'action' => 'delete user.111']]);
        } elseif ($mode === 3) {
            // 仅写到阿里云日志
            Writer::info('bbbb', ['user' => 'bbb', 'action' => 'cccccccccccc']);
        } else {
            // 如果不同日志内容需要不同 topic 和 source，请在写日志（ Log::info()|SLSLog::putLogs()|app('sls')->putLogs()...）前执行 setTopic、setSource
            // app('sls')->setTopic('topic-new')->setSource('source-new');

            // 写到本地同时写到阿里云日志，需配置 logging，同时 LOG_CHANNEL 为 ssl
            Log::debug('admin create user.333', ['user' => 'bbb', 'action' => 'cccccccccccc']);
        }
    }
}
    
```

## 项目依赖

| 依赖                         | 仓库地址                                                 | 备注 |
| :--------------------------- | :------------------------------------------------------- | :--- |
| lokielse/aliyun-open-api-sls | https://github.com/AliyunOpenAPI/php-aliyun-open-api-sls | 无   |

### 备注

1、测试脚本 tests/LogTest.php 仅作为示例供参考；

2、from [lokielse/laravel-sls](https://github.com/lokielse/laravel-sls) 。



