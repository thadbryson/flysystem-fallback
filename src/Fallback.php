<?php

namespace TCB\Flysystem;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

/**
 * Class Fallback
 *
 * @author Thad Bryson <thadbry@gmail.com>
 */
class Fallback
{

    /**
     * Master filesystem.
     *
     * @var FilesystemInterface[]
     */
    protected $filesystems;

    /**
     * Fallback constructor.
     *
     * @param FilesystemInterface $primary
     * @param array               $fallbacks
     */
    public function __construct(FilesystemInterface $primary, array $fallbacks)
    {
        // Add primary to beginning of array.
        $this->filesystems = [$primary];

        // Add each Fallback.
        foreach ($fallbacks as $index => $fallback) {

            $this->add($fallback);
        }
    }

    /**
     * Add a fallback Filesystem.
     *
     * @param \League\Flysystem\FilesystemInterface $fallback
     *
     * @return void
     */
    protected function add(FilesystemInterface $fallback)
    {
        $this->filesystems[] = $fallback;
    }

    /**
     * Find what index of Filesystem
     *
     * @param string $path
     *
     * @return int|string|false
     */
    public function findIndex($path)
    {
        foreach ($this->filesystems as $index => $filesystem) {

            if ($filesystem->has($path) === true) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Find what Filesystem this path is in. By priority.
     *
     * @param string $path
     *
     * @return FilesystemInterface|false
     */
    public function find($path)
    {
        $index = $this->findIndex($path);

        // Not found?
        if ($index === false) {
            return false;
        }

        return $this->filesystems[$index];
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->execute($path, 'has');
    }

    /**
     * Read a file.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The file contents or false on failure.
     */
    public function read($path)
    {
        return $this->execute($path, 'read');
    }

    /**
     * Retrieves a read-stream for a path.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return resource|false The path resource or false on failure.
     */
    public function readStream($path)
    {
        return $this->execute($path, 'readStream');
    }

    /**
     * Go through each Filesystem to search for a result on a method call.
     *
     * @param string $path
     * @param string $method
     *
     * @return mixed
     */
    protected function execute($path, $method)
    {
        $filesystem = $this->find($path);

        // Not found?
        if ($filesystem === false) {
            return false;
        }

        // Call method on object.
        return call_user_func_array([$filesystem, $method], [$path]);
    }
}
