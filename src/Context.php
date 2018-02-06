<?php

namespace PHPLocals;


class Context
{
    private static $local;
    private static $on_enter;
    private static $on_exit;

    public static function onEnter(callable $callback)
    {
        static::$on_enter[] = $callback;
    }

    public static function onExit(callable $callback)
    {
        static::$on_exit[] = $callback;
    }

    private static function runCallbacks(array $callbacks, ...$args)
    {
        foreach ($callbacks as $callback) {
            call_user_func_array($callback, $args);
        }
    }

    public static function getContext(bool $create = false): Local
    {
        if (null === static::$local) {
            static::$local = new Local();
        } elseif ($create) {
            static::$local = new Local(static::$local);
        }

        return static::$local;
    }

    public static function enter(callable $callback, ...$args)
    {
        // Ensure we create a new Local
        $local = static::getContext(true);
        static::runCallbacks(static::$on_enter);

        $exception = null;
        try {
            static::runCallbacks([$callback, ], $args);
        } catch (\Throwable $e) {
            $exception = $e;
        } finally {
            static::exit($exception);
        }
    }

    public static function exit(\Throwable $exception = null)
    {
        static::runCallbacks(static::$on_exit, $exception);

        $ctx = static::getContext();
        // Switch the current local for it's parent. Which
        //  might be `null` once we are at the end of the
        //  call chain.
        static::$local = $ctx->getParent();
    }
}