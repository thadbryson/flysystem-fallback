<?php

namespace TCB\Flysystem;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use TCB\Flysystem\Exceptions\InvalidFallbackFilesystemException;

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
     * @param FilesystemInterface $master
     * @param array               $fallbacks
     */
    public function __construct(FilesystemInterface $master, array $fallbacks)
    {
        $this->filesystems = array_merge([$master], $fallbacks);

        // Check Fallbaks are FilesystemInterface
        foreach ($this->filesystems as $index => $filesystem) {

            // Invalid FilesystemObject.
            if ($filesystem instanceof FilesystemInterface === false) {
                throw new InvalidFallbackFilesystemException($index);
            }
        }
    }

    /**
     * Go through each Filesystem to search for a result on a method call.
     *
     * @param string $path
     * @param string $method
     * @param mixed  $resultNotWanted
     *
     * @return mixed
     */
    protected function search($path, $method, $resultNotWanted)
    {
        foreach ($this->filesystems as $filesystem) {

            $result = call_user_func_array([$filesystem, $method], [$path]);

            // If result is not the negative?
            // It's the result we want - return it.
            if ($result !== $resultNotWanted) {
                return $result;
            }
        }

        // Failure - return FALSE
        return false;
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
        return $this->search($path, 'has', false);
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
        return $this->search($path, 'read', false);
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
        return $this->search($path, 'readStream', false);
    }
}
