<?php

namespace ByTIC\Auth\Tests;

use Mockery;
use Nip\Config\Config;
use Nip\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        $container = \Nip\Container\Utility\Container::container(true);

        $container->set('inflector', \Nip\Inflector\Inflector::instance());
        $container->set('config', new Config());
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        Container::setInstance(null);
        Mockery::close();
    }
}
