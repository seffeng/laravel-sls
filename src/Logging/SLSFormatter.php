<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelSLS\Logging;

use Illuminate\Log\Logger;

class SLSFormatter
{
    /**
     *
     * @author zxf
     * @date   2020年4月24日
     * @param  Logger $logger
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new SLSContentFormatter());
        }
    }
}
