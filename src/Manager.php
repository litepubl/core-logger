<?php

namespace litepubl\core\logfactory;

use Psr\Log\LoggerInterface;
use litepubl\core\logmanager\LogManagerInterface;

class Manager implements LogManagerInterface
{
    protected $logger;
    protected $runtime;
protected $exceptionFormater;

    public function __construct(LoggerInterface $logger, RuntimeHandler $runtime, ExceptionFormater $exceptionFormater)
    {
        $this->logger = $logger;
$this->runtime = $runtime;
$this->exceptionFormater = $exceptionFormater;
}

    public function getLogger():LoggerInterface
{
return $this->logger;
}

    public function logException(\Throwable $e, array $context = [])
    {
        $this->getLogger()->alert($this->exceptionFormater->getLog($e), $context);
    }

    public function trace(string $message = '', array $context = [])
    {
        $this->getLogger()->info($this->exceptionFormater->trace($message), $context);
    }

    public function getHtmlLog(): string
    {
return $this->runtime->getHtml();
    }
}
