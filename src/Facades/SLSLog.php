<?php

namespace Seffeng\LaravelSLS\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @author zxf
 * @date    2020年4月18日
 * @method static void putLogs(array $contents)
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
