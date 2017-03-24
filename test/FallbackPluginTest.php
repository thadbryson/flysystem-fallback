<?php

namespace Test;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem;
use TCB\Flysystem\Fallback;
use TCB\Flysystem\FallbackPlugin;

/**
 * Class FallbackPluginTest
 *
 * @author Thad Bryson <thadbry@gmail.com>
 */
class FallbackPluginTest extends \PHPUnit_Framework_TestCase
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
        $primary = new Filesystem(new Adapter(__DIR__ . '/dirs/primary'));
        $primary->addPlugin(new FallbackPlugin);

        $fallbacks[] = new Filesystem(new Adapter(__DIR__ . '/dirs/fallbacks/fallback.0'));

        $this->fallback = $primary->getFallback($fallbacks);
    }

    /**
     * Is Fallback object on the primary filesystem.
     *
     * @return void
     */
    public function testFallbackObjectOnPlugin()
    {
        $this->assertEquals(Fallback::class, get_class($this->fallback));
    }

    /**
     * ->findIndex($path) returns correct numeric index.
     *
     * @return void
     */
    public function testGetFallbackIndex()
    {
        $this->assertEquals('0', $this->fallback->findIndex('only-primary/test.txt'));
        $this->assertEquals('0', $this->fallback->findIndex('create-dir/1.json'));
        $this->assertEquals('0', $this->fallback->findIndex('create-dir/1.txt'));

        $this->assertEquals(1, $this->fallback->findIndex('0dir/'));
        $this->assertEquals(1, $this->fallback->findIndex('0dir/some-file'));
    }

    /**
     * ->findIndex($path) not found. Returns FALSE.
     *
     * @return void
     */
    public function testFindIndexFalseNotFound()
    {
        $this->assertFalse($this->fallback->findIndex('not-found'));
        $this->assertFalse($this->fallback->findIndex('create-dir/3.PHP'));
    }

    /**
     * Primary dir has file given. Using ->has().
     *
     * @return void
     */
    public function testHasOnPrimary()
    {
        $this->assertTrue($this->fallback->has('create-dir'));
        $this->assertEquals(0, $this->fallback->findIndex('create-dir'));

        $this->assertTrue($this->fallback->has('create-dir/'));
        $this->assertEquals(0, $this->fallback->findIndex('create-dir/'));

        $this->assertTrue($this->fallback->has('create-dir/1.json'));
        $this->assertEquals(0, $this->fallback->findIndex('create-dir/1.json'));

        $this->assertTrue($this->fallback->has('create-dir/1.txt'));
        $this->assertEquals(0, $this->fallback->findIndex('create-dir/1.txt'));

        $this->assertTrue($this->fallback->has('create-dir/2.yml'));
        $this->assertEquals(0, $this->fallback->findIndex('create-dir/2.yml'));

        $this->assertTrue($this->fallback->has('create-dir/3.php'));
        $this->assertEquals(0, $this->fallback->findIndex('create-dir/3.php'));
    }

    /**
     * Test ->has($path) on a fallback.
     *
     * @return void
     */
    public function testHasOnFallback()
    {
        $this->assertTrue($this->fallback->has('create-dir/0.json'));
        $this->assertNotEquals(0, $this->fallback->has('create-dir/0.json'));

        $this->assertTrue($this->fallback->has('create-dir/0.txt'));
        $this->assertNotEquals(0, $this->fallback->has('create-dir/0.txt'));

        $this->assertTrue($this->fallback->has('0dir'));
        $this->assertNotEquals(0, $this->fallback->has('0dir'));

        $this->assertTrue($this->fallback->has('0dir/some-file'));
        $this->assertNotEquals(0, $this->fallback->has('0dir/some-file'));
    }

    /**
     * ->has($path) not found. Returns FALSE.
     *
     * @return void
     */
    public function testHasFalse()
    {
        $this->assertFalse($this->fallback->has('not-a-dir'));
        $this->assertFalse($this->fallback->has('not-a-dir/'));
        $this->assertFalse($this->fallback->has('create-dir/1.json.'));
        $this->assertFalse($this->fallback->has('0dir/no-file'));
    }

    public function testReadPrimary()
    {
        $this->assertEquals(0, $this->fallback->findIndex('create-dir/1.json'));

        $contents = json_decode($this->fallback->read('create-dir/1.json'), $assoc = true);

        $this->assertNotFalse($contents);
        $this->assertEquals('here', $contents['some-text']);
    }

    public function testReadFallback()
    {
        $this->assertNotEquals(0, $this->fallback->findIndex('0dir/some-file'));

        $contents = $this->fallback->read('0dir/some-file');

        $this->assertNotFalse($contents);
        $this->assertEquals("what here?\n", $contents);
    }

    /**
     * ->read($path) not found. Returns FALSE.
     *
     * @return void
     */
    public function testReadFalseNotFound()
    {
        $this->assertFalse($this->fallback->read('0dir-nah/'));
        $this->assertFalse($this->fallback->read('0dir/some-file2'));
        $this->assertFalse($this->fallback->read('create-dir/1.yml'));
    }

    /**
     * ->readStream($path) on the primary.
     *
     * @return void
     */
    public function testReadStreamPrimary()
    {
        $this->assertTrue(is_resource($this->fallback->readStream('create-dir/1.json')));
        $this->assertTrue(is_resource($this->fallback->readStream('create-dir')));
        $this->assertTrue(is_resource($this->fallback->readStream('only-primary/test.txt')));
    }

    /**
     * ->readStream($path) on a fallback.
     *
     * @return void
     */
    public function testReadStreamFallback()
    {
        $this->assertTrue(is_resource($this->fallback->readStream('0dir')));
        $this->assertTrue(is_resource($this->fallback->readStream('0dir/some-file')));
    }

    /**
     * ->readStream($path) not found. Returns FALSE.
     *
     * @return void
     */
    public function testReadStreamFalseNotFound()
    {
        $this->assertFalse($this->fallback->readStream('0dir-nah/'));
        $this->assertFalse($this->fallback->readStream('0dir/some-file2'));
        $this->assertFalse($this->fallback->readStream('create-dir/1.yml'));
    }
}
