<?php

namespace App\Logger\Formatters;

use Monolog\Formatter\NormalizerFormatter;
use Illuminate\Http\Request;

/**
 * Suitable for console or file output.
 */
class BetterFormatter extends NormalizerFormatter
{
    protected $messageTemplate = "%s in %s line %s \n%s";
    protected $frameTemplate = "%s: %s%s%s%s%sin %s on line %s\n";
    protected $argsTemplate = '(%s)';
    protected $extraTemplate = "%s: %s\n";
    protected $outputTemplate = "%s\n%s\n%s\n";

    protected $escapeHtml = false;

    /**
     * Formats a log record.
     *
     * @param array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        if (($exception = $record['context']['exception'] ?? false)) {
            $message = $this->formatMessage($exception);
            $trace = $this->formatTrace($exception);
        } else {
            $message = $record['message'];
            $trace = '';
        }
        $extra = $this->formatExtra($record['extra']);

        return sprintf($this->outputTemplate, $message, $trace, $extra);
    }

    /**
     * Formats a set of log records.
     *
     * @param array $records A set of records to format
     *
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $message = '';

        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    /**
     * Formats an array as a string.
     *
     * @param array $args The argument array
     *
     * @return string
     */
    private function formatArgs(array $args)
    {
        $result = array();
        $keys = 0;

        foreach ($args as $key => $arg) {
            if (is_object($arg)) {
                if (method_exists($arg, 'toJson')) {
                    $formattedValue = mb_substr(\HJson::encode(\HJson::decode(($arg->toJson()))), 0, config('exceptions.trace.maxJsonLength'));
                } elseif ($arg instanceof Request) {
                    $formattedValue = mb_substr(\HJson::encode($arg->all()), 0, config('exceptions.trace.maxJsonLength'));
                } else {
                    $formattedValue = sprintf('{%s}', $this->formatClass(get_class($arg)));
                }
            } elseif (is_array($arg)) {
                $formattedValue = sprintf('%s', is_array($arg) ? $this->formatArgs($arg) : $arg);
            } elseif (is_string($arg)) {
                $formattedValue = sprintf("'%s'", mb_substr($this->escapeHtml ? $this->escapeHtml($arg) : $arg, 0, config('exceptions.trace.maxStringLength')));
            } elseif (null === $arg) {
                $formattedValue = 'null';
            } elseif (is_resource($arg)) {
                $formattedValue = 'resource';
            } else {
                $formattedValue = str_replace("\n", '', var_export($this->escapeHtml ? $this->escapeHtml((string) $arg) : (string) $arg, true));
            }

            if ($keys >= config('exceptions.trace.maxArrayKeys')) {
                break;
            }
            ++$keys;

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }

    private function formatMessage($exception)
    {
        $message = sprintf($this->messageTemplate,
            $this->formatClass(get_class($exception)),
            basename($exception->getFile()),
            $exception->getLine(),
            $exception->getMessage()
        );

        return $message;
    }

    private function formatTrace($exception)
    {
        $trace = '';
        $level = 1;
        $frames = $exception->getTrace();
        array_unshift($frames, ['file' => $exception->getFile(), 'line' => $exception->getLine()]);

        foreach ($frames as $frame) {
            if (!isset($frame['file'])) {
                $frame['file'] = $exception->getFile();
                $frame['line'] = $exception->getLine();
            }

            $class = '';

            if (isset($frame['class'])) {
                $class = $this->formatClass($frame['class']);
            }

            if (false === array_search(basename($frame['file']), config('exceptions.trace.excludeFiles'))
                && false === array_search($class, config('exceptions.trace.excludeClasses'))) {
                $trace .= sprintf($this->frameTemplate,
                    $level++,
                    isset($frame['function']) ? 'at ' : '',
                    $class,
                    $frame['type'] ?? '',
                    $frame['function'] ?? '',
                    isset($frame['args']) ? (sprintf($this->argsTemplate, $this->formatArgs($frame['args'])).' ') : '',
                    basename($frame['file']),
                    $frame['line'] ?? ''
                );
            }
        }

        return $trace;
    }

    protected function formatExtra(&$extra)
    {
        $extraOutput = '';

        if (isset($extra)) {
            foreach ($extra as $key => $value) {
                $value = $this->convertToString($value);
                $extraOutput .= sprintf($this->extraTemplate, $key, $value);
            }
        }

        return $extraOutput;
    }

    protected function formatClass($class)
    {
        $parts = explode('\\', $class);

        return array_pop($parts);
    }

    protected function convertToString($data)
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        $data = $this->normalize($data);

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return str_replace('\\/', '/', json_encode($data));
    }

    /**
     * HTML-encodes a string.
     */
    protected function escapeHtml($str)
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
