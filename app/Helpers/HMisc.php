<?php

class HMisc
{
    public static function echoIfInConsole($message, ...$args)
    {
        if (HMisc::isRunningInConsole()) {
            $message = '['.date('d.m.Y H:i:s', time() + HDate::getMoscowUtcOffset()).'] '.$message."\n";

            if (count(func_get_args()) > 1) {
                $args = func_get_args();
                array_shift($args);
                $message .= vdr($args)."\n";
            }
            echo $message;
        }
    }

    public static function echoIfDebuggingInConsole($message, ...$args)
    {
        if (HMisc::isRunningInConsole() && getenv('DEBUG')) {
            $message = '['.date('d.m.Y H:i:s', time() + HDate::getMoscowUtcOffset()).'] '.$message."\n";

            if (count(func_get_args()) > 1) {
                $args = func_get_args();
                array_shift($args);
                $message .= vdr($args)."\n";
            }
            echo $message;
        }
    }

    public static function isRunningInConsole()
    {
        return PHP_SAPI == 'cli';
    }

    public static function debug(...$arguments)
    {
        $args = func_get_args();

        foreach ($args as &$arg) {
            if (!is_scalar($arg)) {
                $arg = json_encode($arg, JSON_UNESCAPED_UNICODE);
            }
        }

        $now = DateTime::createFromFormat('U.u', microtime(true));
        $timestampStr = $now->format('d.m.Y H:i:s.u');

        $content = $timestampStr.': '.implode(', ', $args)."\n";
        $debugLog = storage_path('/logs/debug.log');
        file_put_contents($debugLog, $content, FILE_APPEND | LOCK_EX);
    }
}
