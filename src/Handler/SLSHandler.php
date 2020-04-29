<?php

namespace Seffeng\LaravelSLS\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Seffeng\LaravelSLS\Helpers\ArrayHelper;

class SLSHandler extends AbstractProcessingHandler
{
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
