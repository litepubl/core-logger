<?php

namespace litepubl\core\logfactory;

use litepubl\core\container\factories\Base;
use litepubl\core\logmanager\FactoryInterface;
use litepubl\core\logmanager\LogManagerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Factory extends Base implements FactoryInterface
{
    const CHANNEL = 'general';
    const FORMAT = "%datetime%\n%channel%.%level_name%:\n%message%\n%context% %extra%\n\n";
    protected $logFileName = 'logs/log.log';
    protected $implementations = [
        LogManagerInterface::class=> Manager::class,
        LoggerInterface::class => Logger::class,
    ];

    protected $classMap = [
        Manager::class=> 'createManager',
        Logger::class => 'createLogger',
        MailerHandler::class => 'createMailerHandler',
        RuntimeHandler::class => 'createRuntimeHandler',
        ExceptionFormater::class => 'createExceptionFormater',
        ];

    protected function getLogFileName(): string
    {
        $app = $this->container->get('app');
        return $app->getPaths()->data . $this->logFileName;
    }

    public function createManager(): Manager
    {
        $logger = $this->container->get(Logger::class);
        $runTime = $this->container->get(RuntimeHandler::class);
        $logger->pushHandler($runtime);
        $exceptionFormater  = $this->container->get(ExceptionFormater::class);

        return new Manager($logger, $runtime, $exceptionFormater);
    }

    public function createLogger(): Logger
    {
        $app = $this->container->get('app');
        $logger = new logger(static::CHANNEL);

        if (!$app->getDebug()) {
            $handler = new ErrorHandler($logger);
            $handler->registerErrorHandler([], false);
            $handler->registerFatalHandler();
        }

        $handler = new StreamHandler($this->logFile, Logger::DEBUG, true, 0666);
        $handler->setFormatter(new LineFormatter(static ::FORMAT, null, true, false));
        $logger->pushHandler($handler);

        if (!$app->getDebug() && $app->getInstalled()) {
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
