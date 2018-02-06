<?php


use PHPLocals\Context;
use PHPLocals\Local;

class ContextTest extends \PHPUnit\Framework\TestCase
{

    public function testOnEnter ()
    {
        static::assertEmpty(Context::getOnEnter());
        Context::onEnter(function () {
        });

        static::assertNotEmpty(Context::getOnEnter());
    }

    public function testOnEnterCalled ()
    {
        $called = false;
        Context::onEnter(function () use (&$called) {
            $called = true;
        });

        Context::enter(function () {
        });

        static::assertTrue($called);
    }

    public function testOnExit ()
    {
        static::assertEmpty(Context::getOnExit());
        Context::onExit(function (\Throwable $exception = null) {
        });

        static::assertNotEmpty(Context::getOnExit());
    }

    public function testOnExitCalled ()
    {
        $called = false;
        Context::onExit(function (\Throwable $exception = null) use (&$called) {
            $called = true;
        });

        Context::enter(function () {
        });
        static::assertTrue($called);
    }

    public function testRemoveListeners ()
    {
        Context::onEnter(function () {});
        Context::onExit(function () {});

        static::assertNotEmpty(Context::getOnEnter());
        static::assertNotEmpty(Context::getOnExit());

        Context::flush();
        static::assertEmpty(Context::getOnEnter());
        static::assertEmpty(Context::getOnExit());
        static::assertNull(Context::getLocal());
    }

    public function testGetContext ()
    {
        $ctx = Context::getContext();
        static::assertInstanceOf(Local::class, $ctx);

        $ctx_2 = Context::getContext();
        static::assertEquals($ctx, $ctx_2);
    }

    public function testGetParentContext ()
    {
        $ctx = Context::getContext();
        static::assertInstanceOf(Local::class, $ctx);

        $child = Context::getContext(true);
        static::assertInstanceOf(Local::class, $child);
        static::assertEquals($child->getParent(), $ctx);
    }


    public function testEnter ()
    {
        $called = false;
        Context::enter(function () use (&$called) {
            $called = true;
        });

        static::assertTrue($called);
    }

    public function testEnterWithException ()
    {
        Context::onExit(function (\Throwable $exception = null) {
            static::assertNotNull($exception);
            static::assertInstanceOf(\RuntimeException::class, $exception);
            static::assertEquals($exception->getMessage(), 'Scary Exception!');
        });

        $called = false;
        Context::enter(function () use (&$called) {
            $called = true;
            throw new \RuntimeException('Scary Exception!');
        });

        static::assertTrue($called);
    }

    public function testNestedEnter ()
    {
        Context::flush();
        Context::enter(function () {
            $ctx = Context::getContext();
            $ctx->set('foo', 123);

            Context::enter(function () use ($ctx) {
                $child = Context::getContext();
                static::assertNotEquals($child, $ctx);
                static::assertEquals($child->get('foo'), 456);

                $child->set('foo', 456);
                static::assertEquals($child->get('foo'), 456);
            });

            static::assertEquals($ctx->get('foo'), 123);
        });

        static::assertNull(Context::getLocal());
    }
}
