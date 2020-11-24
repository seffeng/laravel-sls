<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelSLS\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Seffeng\LaravelSLS\Helpers\ArrayHelper;
use Monolog\Logger;

class SLSHandler extends AbstractProcessingHandler
{
    /**
     *
     * @var string
     */
    protected $store;

    /**
     *
     * @author zxf
     * @date   2020年11月24日
     * @param string $connection
     * @param int|string $level
     * @param bool $bubble
     */
    public function __construct(string $store = null, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->store = $store;
    }

    /**
     *
     * {@inheritDoc}
     * @see \Monolog\Handler\AbstractProcessingHandler::write()
     */
    protected function write(array $record): void
    {
        $context = ArrayHelper::getValue($record, 'context', []);
        $exception = ArrayHelper::getValue($context, 'exception');
        if ($exception && $exception instanceof \Exception) {
            $context = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }
        if ($this->store) {
            app('sls')->loadConfig($this->store);
        }
        $datetime = ArrayHelper::getValue($record, 'datetime');
        app('sls')->putLogs([
            'level'   => ArrayHelper::getValue($record, 'level_name', 'INFO'),
            'env'     => ArrayHelper::getValue($record, 'channel', ''),
            'message' => ArrayHelper::getValue($record, 'message', ''),
            'context' => json_encode($context),
            'datetime' => $datetime ? $datetime->format('Y-m-d H:i:s') : '',
            'extra' => json_encode(ArrayHelper::getValue($record, 'extra', [])),
        ]);
    }
}
