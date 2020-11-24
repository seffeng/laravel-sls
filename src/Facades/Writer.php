<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelSLS\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void emergency(string $message, array $context = [])
 * @method static void alert(string $message, array $context = [])
 * @method static void critical(string $message, array $context = [])
 * @method static void error(string $message, array $context = [])
 * @method static void warning(string $message, array $context = [])
 * @method static void notice(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static void debug(string $message, array $context = [])
 * @method static void log($level, string $message, array $context = [])
 * @method static \Psr\Log\LoggerInterface channel(string $channel = null)
 * @method static \Psr\Log\LoggerInterface stack(array $channels, string $channel = null)
 *
 * @see \Seffeng\LaravelSLS\Writer
 */
class Writer extends Facade
{
    /**
     *
     * @author zxf
     * @date   2020年4月26日
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sls.writer';
    }
}