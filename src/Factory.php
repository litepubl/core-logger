<?php

namespace litepubl\core\logfactory;

use litepubl\core\logmanager\FactoryInterface
use litepubl\core\logmanager\LogManagerInterface
use Monolog\ErrorHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Factory implements FactoryInterface
{
    const format = "%datetime%\n%channel%.%level_name%:\n%message%\n%context% %extra%\n\n";
protected $debug;
protected $logFile;

    public function __construct(string $logFile, bool $debug = false)
{
$this->logFile = $logFile;
$this->debug = $debug;
}

    public function getLogManager(): LogManagerInterface
    {
        $logger = new logger('general');

        if (!$this->debug) {
            $handler = new ErrorHandler($logger);
            $handler->registerErrorHandler([], false);
            //$handler->registerExceptionHandler();
            $handler->registerFatalHandler();
        }

        $handler = new StreamHandler($this->logFile, Logger::DEBUG, true, 0666);
        $handler->setFormatter(new LineFormatter(static ::format, null, true, false));
        $logger->pushHandler($handler);

        $runtime = new RuntimeHandler(Logger::WARNING);
        $runtime->setFormatter(new EmptyFormatter());
        $logger->pushHandler($runtime);

        if (!$this->debug && $app->installed) {
            $handler = new MailerHandler('[error] ' . $app->site->name, Logger::WARNING);
            $handler->setFormatter(new LineFormatter(static ::format, null, true, false));
            $logger->pushHandler($handler);
        }

$exceptionFormater = new ExceptionFormater();
return new Manager($logger, $runtime, $exceptionFormater);
    }
}
