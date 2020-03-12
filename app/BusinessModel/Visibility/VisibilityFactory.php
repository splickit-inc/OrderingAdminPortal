<?php

namespace App\BusinessModel\Visibility;


use Illuminate\Support\Facades\App;

class VisibilityFactory implements IVisibilityFactory
{
    /**
     * @param $interface
     * @return array
     * @throws \Exception
     */
    public static function configure($interface)
    {
        try {
            $class = new \ReflectionClass($interface);
            $dirPath = str_replace('/' . basename($class->getFileName()), '', $class->getFileName());
            return static::getClassesThatImplementTheInterface($interface, $class->getNamespaceName(), $dirPath);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $interfaceClassName
     * @param string $nameSpace
     * @param string $dir
     * @return array
     */
    protected static function getClassesThatImplementTheInterface($interfaceClassName = '', $nameSpace = __NAMESPACE__, $dir = __DIR__)
    {
        try {
            $files = [];
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $filesName = str_replace('.php', '', $fileInfo->getFilename());
                $class = $nameSpace . '\\' . studly_case($filesName);
                $reflect = new \ReflectionClass($class);
                if ($reflect->implementsInterface($interfaceClassName) && $reflect->isInstantiable()) {
                    $files[$filesName] = $class;
                }
            }

            foreach ($files as $key => $value) {
                if (array_has(class_implements($value), $interfaceClassName)) {
                    App::bind($value::getName(), $value);
                }
            }

            return $files;
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    /**
     * @param $visibility
     * @param $interface
     * @throws \Exception
     */
    public static function setVisibility($visibility, $interface)
    {
        try {
            try {
                static::configure($interface);
                $visibilityClass = App::make($visibility);
                App::bind($interface, function () use ($visibilityClass) {
                    return $visibilityClass;
                });
            } catch (\Exception $exception) {
                throw $exception;
            }

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $visibility
     * @return null
     */
    public static function getVisibility($visibility)
    {
        try {
            return App::make($visibility);
        } catch (\Exception $exception) {
            return null;
        }
    }
}