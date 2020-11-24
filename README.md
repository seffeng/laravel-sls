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
#SLS_ERROR_LOG_CHANNEL= #可选[默认-daily]

# 3、修改 /config/logging.php 配置，channels 中增加 sls，以下方式二选一；
    
## 3.1 修改 /.env 中 LOG_CHANNEL 为 stack，stack.channels 增加 sls，建议使用此方式，可配置 store
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
            'with' => [    // 可选项：当使用多个存储时可配置此参数
                'store' => 'default'    // 对应 sls.php 配置的 stores 的 key
            ]
        ],

## 3.2 修改 /.env 中 LOG_CHANNEL 为 sls，不支持配置 store
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
#SLS_ERROR_LOG_CHANNEL= #可选[默认-daily]

# 3、使用 Log::info() 方式时需增加配置文件/config/logging.php，channels 中增加 sls,参考文件/vendor/laravel/lumen-framework/config/logging.php，以下方式二选一；

## 3.1 修改 /.env 中 LOG_CHANNEL 为 stack，stack.channels 增加 sls，建议使用此方式，可配置 store
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
            'with' => [    // 可选项：当使用多个存储时可配置此参数
                'store' => 'default'    // 对应 sls.php 配置的 stores 的 key
            ]
        ],

## 3.2 修改 /.env 中 LOG_CHANNEL 为 sls，不支持配置 store
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
|  ├─Exceptions
│  │  SLSException.php
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

# 3、是否同时记录本地日志，可自行通过 /config/logging.php 配置。
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
            // 如果不同日志内容需要不同 project、logStore、topic 和 source
            // 方法一：请在写日志（ Log::info()|SLSLog::putLogs()|app('sls')->putLogs()...）前执行 setProject、setLogStore、setTopic、setSource 或 在 sls.php 配置多个 store
            // app('sls')->setProject('project-new')->setLogStore('logStore-new');
            // app('sls')->setTopic('topic-new')->setSource('source-new');
            // 配置了多个 store 时：app('sls')->loadConfig('store-new')

            // 方法二：在 sls.php 配置多个 store；注意 logging.php 配置选择第一种 handler 方式配置 多个 channel ，此时可通过 channel 实现：
            // Log::channel('sls2')->debug('admin create user.333', ['user' => 'bbb']);

            // 需配置 logging，同时 LOG_CHANNEL 为 sls
            // tap 方式：写到本地同时写到阿里云日志；handler 方式：写到阿里云日志
            Log::debug('admin create user.333', ['user' => 'bbb', 'action' => 'cccccccccccc']);
        }
    }
}
    
```

## 项目依赖

| 依赖                         | 仓库地址                                                 | 备注 |
| :--------------------------- | :------------------------------------------------------- | :--- |
| lokielse/aliyun-open-api-sls | https://github.com/AliyunOpenAPI/php-aliyun-open-api-sls | 无   |

### 更新日志

* [CHANGELOG.md](CHANGELOG.md)

### 备注

1、测试脚本 tests/LogTest.php 仅作为示例供参考；

2、from [lokielse/laravel-sls](https://github.com/lokielse/laravel-sls) 。



