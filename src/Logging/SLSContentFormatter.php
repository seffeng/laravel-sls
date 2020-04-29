<?php

namespace Seffeng\LaravelSLS\Logging;

use Monolog\Formatter\LineFormatter;
use Seffeng\LaravelSLS\Helpers\ArrayHelper;

class SLSContentFormatter extends LineFormatter
{
    /**
     *
     * {@inheritDoc}
     * @see \Monolog\Formatter\LineFormatter::format()
     */
    public function format(array $record): string
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
        return parent::format($record);
    }
}
