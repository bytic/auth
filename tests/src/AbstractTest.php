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
    protected function setUp(): void
    {
        parent::setUp();

        $container = \Nip\Container\Utility\Container::container(true);

        $container->set('inflector', \Nip\Inflector\Inflector::instance());
        $container->set('config', new Config());
    }

    /**
     * @param string $name
     */
    protected function loadConfigIntoContainer($name)
    {
        $container = \Nip\Container\Utility\Container::container(true);
        $path = TEST_FIXTURE_PATH . '/config/' . $name . '.php';
        $data = require $path;
        $container->set('config', new Config(['auth' => $data]));
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        Container::setInstance(null);
        Mockery::close();
    }
}
