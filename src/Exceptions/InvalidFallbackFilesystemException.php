<?php

namespace TCB\Flysystem\Exceptions;

use Exception;
use League\Flysystem\FilesystemInterface;

/**
 * Class InvalidFallbackFilesystemException
 *
 * @author Thad Bryson <thadbry@gmail.com>
 */
class InvalidFallbackFilesystemException extends Exception
{

    /**
     * InvalidFallbackFilesystemException constructor.
     *
     * @param string|string $index
     */
    public function __construct($index)
    {
        parent::__construct(sprintf('Fallback object at index "%s" does not implement %s', $index,
            FilesystemInterface::class), 400);
    }
}
