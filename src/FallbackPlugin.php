<?php

namespace TCB\Flysystem;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

/**
 * class FallbackPlugin
 *
 * @author Thad Bryson <thadbry@gmail.com>
 */
class FallbackPlugin implements PluginInterface
{

    /**
     * Master filesystem.
     *
     * @var FilesystemInterface
     */
    protected $master;

    /**
     * Set master Filesystem.
     *
     * @param FilesystemInterface $master
     *
     * @return void
     */
    public function setFilesystem(FilesystemInterface $master)
    {
        $this->master = $master;
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
        return new Fallback($this->master, $fallbacks);
    }
}
