<?php

namespace Test;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem;
use TCB\Flysystem\Fallback;
use TCB\Flysystem\FallbackPlugin;

/**
 * Class AbstractTestBase
 *
 * @author Thad Bryson <thadbry@gmail.com>
 */
class AbstractTestBase extends \PHPUnit_Framework_TestCase
{

    /**
     * Fallback plugin utility.
     *
     * @var Fallback
     */
    protected $fallback;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $master = new Filesystem(new Adapter(__DIR__ . '/dirs/master'));
        $master->addPlugin(new FallbackPlugin);

        $fallbacks[] = new Filesystem(new Adapter(__DIR__ . '/dirs/fallbacks/fallback.0'));

        $this->fallback = $master->getFallback($fallbacks);
    }
}
