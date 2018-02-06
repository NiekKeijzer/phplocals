<?php


namespace PHPLocals\Manager;


final class BaseContext implements ContextManagerInterface
{
    private $callback;
    private $on_enter;
    private $on_exit;
    private $args;

    public function __construct (callable $callback, array $on_enter = array(), array $on_exit = array(), ...$args)
    {
        $this->callback = $callback;
        $this->on_enter = $on_enter;
        $this->on_exit = $on_exit;
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