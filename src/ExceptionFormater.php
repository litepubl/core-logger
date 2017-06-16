<?php

namespace LitePubl\Core\Logger;

class ExceptionFormater
{
    const  NEW_LINE = "\n";
    const HEADER = 'Caught exception:';
    protected $homeDir;

    public function __construct(string $homeDir = '')
    {
        $this->homeDir = $homeDir;
    }

    public function getLog(\Throwable $e): string
    {
        $result = static::HEADER;
        $result .= static::NEW_LINE ;
        $result .=$e->getMessage();
        $result .= static::NEW_LINE ;

                $result= sprintf('#0 %d %s ', $e->getLine(), $e->getFile());
        $result .= static::NEW_LINE ;

        $result .= $this->getTraceLog($e->getTrace());
        $result = str_replace($this->homeDir, '', $result);

        return $result;
    }

    public function trace(string $message = ''): string
    {
        $result = $message;
        if ($result) {
                $result .= static::NEW_LINE ;
        }

        $result .= $this->getTraceLog(\debug_backtrace());
        $result = str_replace($this->homeDir, '', $result);
        return $result;
    }

    protected function getTraceLog(array $trace): string
    {
        $result = '';
        foreach ($trace as $i => $item) {
            if (isset($item['line'])) {
                $result.= sprintf('#%d %d %s ', $i, $item['line'], $item['file']);
            }

            if (isset($item['class'])) {
                $result.= $item['class'] . $item['type'] . $item['function'];
            } else {
                $result.= $item['function'] . '()';
            }

            if (isset($item['args']) && count($item['args'])) {
                $result.= static::NEW_LINE ;
                $args = [];
                foreach ($item['args'] as $arg) {
                    $args[] = $this->dump($arg);
                }

                $result.= implode(', ', $args);
            }

            $result.= static::NEW_LINE ;
        }

        return $result;
    }

    public function dump($v): string
    {
        switch (\gettype($v)) {
            case 'string':
                if ((strlen($v) > 60) && ($i = strpos($v, ' ', 50))) {
                    $v = substr($v, 0, $i);
                }

                $result = \sprintf('\'%s\'', $v);
                break;

            case 'object':
                $result = \get_class($v);
                break;

            case 'boolean':
                $result = $v ? 'true' : 'false';
                break;

            case 'integer':
            case 'double':
            case 'float':
                $result = $v;
                break;

            case 'array':
                $result = '';
                foreach ($v as $k => $item) {
                    $s = $this->dump($item);
                    $result.= "$k = $s;\n";
                }

                $result = "[\n$result]\n";
                break;

            default:
                $result = \gettype($v);
        }

        return $result;
    }
}
