<?php  declare(strict_types=1);

namespace Seffeng\LaravelSLS\Tests;

use PHPUnit\Framework\TestCase;
use Seffeng\LaravelSLS\Facades\SLSLog;
use Illuminate\Support\Facades\Log;
use Seffeng\LaravelSLS\Facades\Writer;

class LogTest extends TestCase
{
    /**
     *
     * @author zxf
     * @date    2020年4月17日
     * @throws \Exception
     */
    public function testPutLogs()
    {
        try {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
