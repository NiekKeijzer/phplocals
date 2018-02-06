<?php

namespace PHPLocals;


use PHPLocals\Manager\BaseContext;
use PHPLocals\Manager\ContextManagerInterface;

class Context
{
    private static $local;
    private static $on_enter = array();
    private static $on_exit = array();

    public static function getLocal ()
    {
        return static::$local;
    }

    public static function onEnter(callable $callback)
    {
        static::$on_enter[] = $callback;
    }

    public static function getOnEnter ()
    {
        return static::$on_enter;
    }

    public static function onExit(callable $callback)
    {
        static::$on_exit[] = $callback;
    }

    public static function getOnExit ()
    {
        return static::$on_exit;
    }

    public static function flush ()
    {
        static::$on_enter = array();
        static::$on_exit = array();
        static::$local = null;
    }

    private static function runCallbacks(array $callbacks, ...$args)
    {
        foreach ($callbacks as $callback) {
            \call_user_func_array($callback, $args);
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

    /**
     * @param callable|\PHPLocals\Manager\ContextManagerInterface $callback
     * @param array ...$args
     */
    public static function enter(callable $callback, ...$args)
    {
        if (!($callback instanceof ContextManagerInterface)) {
            $callback = new BaseContext($callback, static::$on_enter, static::$on_exit, $args);
        }

        // Ensure we create a new Local
        $local = static::getContext(true);
        $callback->enter();

        $exception = null;
        try {
            static::runCallbacks([$callback, ], $args);
        } catch (\Throwable $e) {
            $exception = $e;
        } finally {
            $callback->exit($exception);

            $ctx = static::getContext();
            // Switch the current local for it's parent. Which
            //  might be `null` once we are at the end of the
            //  call chain.
            static::$local = $ctx->getParent();
        }
    }
}