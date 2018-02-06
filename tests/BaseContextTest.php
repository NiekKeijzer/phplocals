<?php


use PHPLocals\Context;
use PHPLocals\Manager\BaseContext;

class BaseContextTest extends \PHPUnit\Framework\TestCase
{
    public function testCallable ()
    {
        $manager = new BaseContext(function () {});

        static::assertTrue(is_callable($manager));
    }

    public function test__invoke ()
    {
        $called = false;
        $manager = new BaseContext(function () use (&$called) {
            $called = true;
        });

        Context::enter($manager);
        static::assertTrue($called);
    }

    public function testEnter ()
    {
        $called = false;
        $manager = new BaseContext(function () {}, function () use (&$called) {
            $called =true;
        });

        Context::enter($manager);
        static::assertTrue($called);
    }

    public function testGlobalOnEnter ()
    {
        Context::flush();

        $called = false;
        Context::onEnter(function () use (&$called) {
            $called = true;
        });

        Context::enter(function () {});
        static::assertTrue($called);
    }

    public function testExit ()
    {
        $called = false;
        $manager = new BaseContext(function () {}, null, function () use (&$called) {
            $called =true;
        });

        Context::enter($manager);
        static::assertTrue($called);
    }

    public function testGlobalOnExit ()
    {
        Context::flush();

        $called = false;
        Context::onExit(function () use (&$called) {
            $called = true;
        });

        Context::enter(function () {});
        static::assertTrue($called);
    }
}
