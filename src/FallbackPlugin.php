<?php

namespace TCB\Flysystem;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

/**
 * class FallbackPlugin
 *
 * @author Thad Bryson <thadbry@gmail.com>
 *
 * @method Fallback getFallback(array $filesystems)
 */
class FallbackPlugin implements PluginInterface
{

    /**
     * Master filesystem.
     *
     * @var FilesystemInterface
     */
    protected $primary;

    /**
     * Set primary Filesystem.
     *
     * @param FilesystemInterface $primary
     *
     * @return void
     */
    public function setFilesystem(FilesystemInterface $primary)
    {
        $this->primary = $primary;
    }

    /**
     * Method to call on object returned from $this->handle().
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getFallback';
    }

    /**
     * Run plugin.
     *
     * @param FilesystemInterface[] $fallbacks
     *
     * @return \TCB\Flysystem\Fallback
     */
    public function handle(array $fallbacks)
    {
        return new Fallback($this->primary, $fallbacks);
    }
}
