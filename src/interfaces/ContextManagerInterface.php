<?php


namespace PHPLocals\Manager;


interface ContextManagerInterface
{
    public function __enter ();

    public function __exit (\Throwable $exception = null);
}