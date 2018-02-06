<?php


namespace PHPLocals\Manager;


final class BaseContext implements ContextManagerInterface
{
    private $callback;
    private $on_enter = array();
    private $on_exit = array();
    private $args;

    public function __construct (callable $callback, $on_enter = null, $on_exit = null, ...$args)
    {
        $this->callback = $callback;
        if (null !== $on_enter) {
            $this->on_enter = \is_callable($on_enter) ? [$on_enter, ] : $on_enter;
        }

        if (null !== $on_exit) {
            $this->on_exit = \is_callable($on_exit) ? [$on_exit, ] : $on_exit;
        }

        $this->args = $args;
    }

    public function __invoke ()
    {
        return \call_user_func_array($this->callback, $this->args);
    }

    public function enter ()
    {
        foreach ($this->on_enter as $callback) {
            $callback();
        }
    }

    public function exit (\Throwable $exception = null)
    {
        foreach ($this->on_exit as $callback) {
            $callback($exception);
        }
    }
}