<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelSLS\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @author zxf
 * @date    2020年4月18日
 * @method static void putLogs(array $contents)
 * @method static \Seffeng\LaravelSLS\SLSLog loadConfig(string $store)
 *
 * @see \Seffeng\LaravelSLS\SLSLog
 */
class SLSLog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sls';
    }
}
