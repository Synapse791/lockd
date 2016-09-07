<?php

namespace Lockd\Services\Install;

use Illuminate\Filesystem\Filesystem;
use Lockd\Services\BaseService;

/**
 * Class CheckManager
 *
 * @package Lockd\Services\Install
 * @author Iain Earl <synapse791@gmail.com>
 * @codeCoverageIgnore
 */
class CheckManager extends BaseService
{
    /** @var Filesystem */
    private $fileSystem;

    /**
     * CheckManager constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->fileSystem = $filesystem;
    }

    /**
     * Checks that PHP version
     *
     * @return bool
     */
    public function isPhpVersionOk()
    {
        $versionString = explode('-', phpversion())[0];

        $parts = explode('.', $versionString);

        $major = $parts[0];
        $minor = $parts[1];
        $hotfix = $parts[2];

        if (intval($major) >= 5)
            if (intval($minor) >= 5)
                if (intval($hotfix) >= 9)
                    return true;

        return false;
    }

    /**
     * Checks PHP module is loaded
     *
     * @param string $module
     * @return bool
     */
    public function isModuleLoaded($module)
    {
        return extension_loaded($module);
    }

    /**
     * Checks storage folder is writable
     *
     * @return bool
     */
    public function isStorageWritable()
    {
        return $this->fileSystem->isWritable(app_path() . '/../storage');
    }
}