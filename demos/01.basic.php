<?php

require __DIR__ . '/../vendor/autoload.php';

\PHPLocals\Context::enter(function () {
    $ctx = \PHPLocals\Context::getContext();
    $ctx->set('foo', 123);
    echo "foo: {$ctx->get('foo')}\n";

    \PHPLocals\Context::enter(function () {
        $ctx = \PHPLocals\Context::getContext();
        $ctx->set('foo', 456);
        echo "foo: {$ctx->get('foo')}\n";
    });

    echo "foo: {$ctx->get('foo')}\n";
});