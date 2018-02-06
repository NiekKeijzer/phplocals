<?php


namespace PHPLocals\Manager;


interface ContextManagerInterface
{
    public function __invoke ();

    public function enter ();

    public function exit (\Throwable $exception = null);
}