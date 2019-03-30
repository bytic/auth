<?php

namespace ByTIC\Auth\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends TestCase
{
    protected $object;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}
