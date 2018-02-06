<?php


use PHPLocals\Manager\BaseContext;

class BaseContextTest extends \PHPUnit\Framework\TestCase
{
    public function testCallable ()
    {
        $called = false;
        $manager = new BaseContext(function () use (&$called) {
            $called = true;
        });

        static::assertTrue(is_callable($manager));
        \PHPLocals\Context::enter($manager);
        static::assertTrue($called);
    }

    /*
    public function test__call ()
    {

    }

    public function test_enter ()
    {

    }

    public function test_exit ()
    {

    }
    */
}
