<?php
namespace litepubl\core\logfactory;

use Monolog\Formatter\FormatterInterface;

class EmptyFormatter implements FormatterInterface
{

    public function format(array $record)
    {
        return '';
    }

    public function formatBatch(array $records)
    {
        return '';
    }
}
