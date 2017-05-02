<?php

namespace litepubl\core\logfactory;

use litepubl\core\instances\BaseFactory;
use litepubl\core\logmanager\FactoryInterface;
use litepubl\core\logmanager\LogManagerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;

class Factory extends BaseFactory implements FactoryInterface
{
    const CHANNEL = 'general';
    const FORMAT = "%datetime%\n%channel%.%level_name%:\n%message%\n%context% %extra%\n\n";
    protected $debug;
    protected $logFile;

    public function __construct(string $logFile, bool $debug = false)
    {
        $this->logFile = $logFile;
        $this->debug = $debug;
    }

    protected function getClassMap(): array
    {
        return [
        Manager::class=> 'createManager',
        LogManagerInterface::class=> 'createManager',
        Logger::class => 'createLogger',
        LoggerInterface::class => 'createLogger',
        MailerHandler::class => 'createMailerHandler',
        RuntimeHandler::class => 'createRuntimeHandler',
        ExceptionFormater::class => 'createExceptionFormater',
        ];
    }

    public function createManager(): LogManagerInterface
    {
        $logger = $this->container->get(LoggerInterface::class);
        $runTime = $this->container->get(RuntimeHandler::class);
        $logger->pushHandler($runtime);
        $exceptionFormater  = $this->container->get(ExceptionFormater::class);

        return new Manager($logger, $runtime, $exceptionFormater);
    }

    public function createLogger(): Logger
    {
        $app = $this->container->get(App::class);
        $logger = new logger(static::CHANNEL);

        if (!$this->debug) {
            $handler = new ErrorHandler($logger);
            $handler->registerErrorHandler([], false);
            $handler->registerFatalHandler();
        }

        $handler = new StreamHandler($this->logFile, Logger::DEBUG, true, 0666);
        $handler->setFormatter(new LineFormatter(static ::FORMAT, null, true, false));
        $logger->pushHandler($handler);

        if (!$this->debug && $app->getInstalled()) {
            $logger->pushHandler($this->createMailerHandler());
        }

        return $logger;
    }

    public function createMailerHandler(): MailerHandler
    {
            $handler = new MailerHandler('[error] ' . $app->site->name, Logger::WARNING);
            $handler->setFormatter(new LineFormatter(static ::FORMAT, null, true, false));
        return $handler;
    }

    public function createRuntimeHandler(): RuntimeHandler
    {
        $runtime = new RuntimeHandler(Logger::WARNING);
        $runtime->setFormatter(new EmptyFormatter());
        return $runtime;
    }

    public function createExceptionFormater(): ExceptionFormater
    {
        return new ExceptionFormater();
    }
}
