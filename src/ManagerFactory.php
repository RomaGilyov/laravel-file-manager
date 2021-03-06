<?php

namespace Hippomundo\FileManager;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Hippomundo\FileManager\Exceptions\FileManagerException;
use Hippomundo\FileManager\Interfaces\Mediable;
use Hippomundo\FileManager\Models\File;
use Hippomundo\FileManager\Models\Media;
use Hippomundo\FileManager\Models\Video;

/**
 * Class Manager
 * @package Hippomundo\FileManager
 */
class ManagerFactory
{
    /**
     * @param $check
     * @return FileManager|MediaManager|VideoManager
     * @throws FileManagerException
     */
    public static function get($check)
    {
        if (is_string($check)) {
            return static::resolveStringCheck($check);
        }

        if ($check instanceof Mediable) {
            return static::resolveMediableCheck($check);
        }

        if ($check instanceof Relation) {
            return static::resolveMediableCheck($check->getRelated());
        }

        throw new FileManagerException("Not able to resolve");
    }

    /**
     * @param $string
     * @return FileManager|MediaManager|VideoManager
     * @throws FileManagerException
     */
    protected static function resolveStringCheck($string)
    {
        switch (Str::singular(strtolower($string))) {
            case 'image':
            case 'photo':
            case 'media':
                return new MediaManager();
            case 'video':
                return new VideoManager();
            case 'file':
                return new FileManager();
        }

        throw new FileManagerException("Not able to resolve {$string}");
    }

    /**
     * @param $mediable
     * @return FileManager|MediaManager|VideoManager
     * @throws FileManagerException
     */
    protected static function resolveMediableCheck($mediable)
    {
        if ($mediable instanceof Media) {
            return new MediaManager();
        }

        if ($mediable instanceof Video) {
            return new VideoManager();
        }

        if ($mediable instanceof File) {
            return new FileManager();
        }

        $className = class_basename($mediable);

        throw new FileManagerException("Not able to resolve {$className}");
    }
}
