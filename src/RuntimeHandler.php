<?php

namespace LitePubl\Core\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\Formatter\HtmlFormatter;

class RuntimeHandler extends AbstractProcessingHandler
{
    protected $log;

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->log = [];
    }

    protected function write(array $record)
    {
        $this->log[] = $record;
    }

    public function getHtml(): string
    {
        if (count($this->log)) {
            $formatter = new HtmlFormatter();
            $result = $formatter->formatBatch($this->log);
            //clear current log
            $this->log = [];
            return $result;
        }

        return '';
    }
}
