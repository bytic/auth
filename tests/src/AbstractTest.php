<?php

namespace ByTIC\Auth\Tests;

use Mockery;
use Nip\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Container::setInstance(new Container());
        Container::getInstance()->set('inflector', \Nip\Inflector\Inflector::instance());
    }

    protected function tearDown()
    {
        parent::tearDown();
        Container::setInstance(null);
        Mockery::close();
    }
}
