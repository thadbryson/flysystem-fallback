<?php

namespace Test;

use TCB\Flysystem\Fallback;

/**
 * Class FallbackPluginTest
 *
 * @author Thad Bryson <thadbry@gmail.com>
 */
class FallbackPluginTest extends AbstractTestBase
{
    /**
     * Is Fallback object on the master filesystem.
     *
     * @return void
     */
    public function testGettingSyncFromPlugin()
    {
        $this->assertEquals(Fallback::class, get_class($this->fallback));
    }

    public function testGetFallbackIndex()
    {
        $this->assertEquals(0, $this->fallback->findFallback('only-master/test.txt'));
        $this->assertEquals(0, $this->fallback->findFallback('create-dir/1.json'));
        $this->assertEquals(0, $this->fallback->findFallback('create-dir/1.txt'));

        $this->assertEquals(1, $this->fallback->findFallback('0dir/'));
        $this->assertEquals(1, $this->fallback->findFallback('0dir/some-file'));
    }

    public function testGetFallbackFalseNotFound()
    {
        $this->assertFalse($this->fallback->findFallback('not-found'));
        $this->assertFalse($this->fallback->findFallback('create-dir/3.PHP'));
    }

    /**
     * Master dir has file given.
     *
     * @return void
     */
    public function testHasOnMaster()
    {
        $this->assertTrue($this->fallback->has('create-dir'));
        $this->assertTrue($this->fallback->has('create-dir/'));
        $this->assertTrue($this->fallback->has('create-dir/1.json'));
        $this->assertTrue($this->fallback->has('create-dir/1.txt'));
        $this->assertTrue($this->fallback->has('create-dir/2.yml'));
        $this->assertTrue($this->fallback->has('create-dir/3.php'));
    }

    public function testHasOnFallback()
    {
        $this->assertTrue($this->fallback->has('create-dir/0.json'));
        $this->assertTrue($this->fallback->has('create-dir/0.txt'));

        $this->assertTrue($this->fallback->has('0dir'));
        $this->assertTrue($this->fallback->has('0dir/some-file'));
    }

    public function testHasFalse()
    {
        $this->assertFalse($this->fallback->has('not-a-dir'));
        $this->assertFalse($this->fallback->has('not-a-dir/'));
        $this->assertFalse($this->fallback->has('create-dir/1.json.'));
        $this->assertFalse($this->fallback->has('0dir/no-file'));
    }

    public function testReadMaster()
    {
        $contents = json_decode($this->fallback->read('create-dir/1.json'), $assoc = true);

        $this->assertNotFalse($contents);
        $this->assertEquals('here', $contents['some-text']);
    }

    public function testReadFallback()
    {
        $contents = $this->fallback->read('0dir/some-file');

        $this->assertNotFalse($contents);
        $this->assertEquals("what here?\n", $contents);
    }

    public function testReadFalseNotFound()
    {
        $this->assertFalse($this->fallback->read('0dir-nah/'));
        $this->assertFalse($this->fallback->read('0dir/some-file2'));
        $this->assertFalse($this->fallback->read('create-dir/1.yml'));
    }

    public function testReadStreamMaster()
    {
        $this->assertTrue(is_resource($this->fallback->readStream('create-dir/1.json')));
        $this->assertTrue(is_resource($this->fallback->readStream('create-dir')));
        $this->assertTrue(is_resource($this->fallback->readStream('only-master/test.txt')));
    }

    public function testReadStreamFallback()
    {
        $this->assertTrue(is_resource($this->fallback->readStream('0dir')));
        $this->assertTrue(is_resource($this->fallback->readStream('0dir/some-file')));
    }

    public function testReadStreamFalseNotFound()
    {
        $this->assertFalse($this->fallback->readStream('0dir-nah/'));
        $this->assertFalse($this->fallback->readStream('0dir/some-file2'));
        $this->assertFalse($this->fallback->readStream('create-dir/1.yml'));
    }
}
