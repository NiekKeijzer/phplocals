<?php


namespace PHPLocals\Manager;


class File implements ContextManagerInterface
{
    private $path;
    private $mode;
    private $callback;

    private $handle;

    public function __construct ($path, $mode = null, callable $callback)
    {
        $this->path = $path;
        $this->mode = $mode;
        $this->callback = $callback;
    }

    public function __invoke ()
    {
        \call_user_func($this->callback, $this->handle);
    }

    public function enter ()
    {
        $this->handle = fopen($this->path, $this->mode);
    }

    public function exit (\Throwable $exception = null)
    {
        if (null !== $this->handle) {
            fclose($this->handle);
        }
    }
}