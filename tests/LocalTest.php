<?php


use PHPLocals\Local;

class LocalTest extends \PHPUnit\Framework\TestCase
{

    public function testGetParent ()
    {
        $local = new Local();
        static::assertNull($local->getParent());

        $child = new Local($local);
        static::assertNotNull($child->getParent());
        static::assertInstanceOf(Local::class, $child->getParent());
    }

    public function testGetVariables ()
    {
        $local = new Local();
        static::assertEmpty($local->getVariables());

        $local->set('foo', 123);
        static::assertNotEmpty($local->getVariables());
    }

    public function testSet ()
    {
        $local = new Local();
        $local->set('foo', 123);
        $vars = $local->getVariables();

        static::assertEquals($vars['foo'], 123);
    }

    public function testParentSet ()
    {
        $local = new Local();
        $local->set('foo', 123);

        $child = new Local($local);
        static::assertEquals($child->get('foo'), 123);

        $child->set('foo', 456);
        static::assertEquals($child->get('foo'), 456);
        static::assertEquals($local->get('foo'), 123);
    }

    public function testGet ()
    {
        $local = new Local();
        $local->set('foo', 123);

        static::assertEquals($local->get('foo'), 123);
        static::assertEquals($local->get('bar', 456), 456);
    }

    public function testParentGet ()
    {
        $local = new Local();
        $local->set('foo', 123);

        $child = new Local($local);
        static::assertEquals($child->get('foo'), 123);
    }

    public function testDel ()
    {
        $local = new Local();
        $local->set('foo', 123);
        $local->del('foo');

        static::assertEmpty($local->getVariables());
    }

    public function testChildDel ()
    {
        $local = new Local();
        $local->set('foo', 123);

        $child = new Local($local);
        $child->set('foo', 456);
        $child->del('foo');
        static::assertEmpty($child->getVariables());

        $child->del('foo', true);
        static::assertEmpty($local->getVariables());
    }

    public function testExists ()
    {
        $local = new Local();
        $local->set('foo', 123);

        static::assertTrue($local->exists('foo'));
        static::assertFalse($local->exists('bar'));
    }
}
