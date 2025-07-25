<?php

namespace Core;

class ClassLoader {
    private static array $namespaces = [];

    public static function register(): void {
        spl_autoload_register([self::class, 'loadClass']);
    }

    public static function addNamespace(string $prefix, string $baseDir): void {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';

        if (!isset(self::$namespaces[$prefix])) {
            self::$namespaces[$prefix] = [];
        }

        array_unshift(self::$namespaces[$prefix], $baseDir);
    }

    public static function loadClass(string $class): void {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);

            if (self::loadMappedFile($prefix, $relativeClass)) {
                return;
            }

            $prefix = rtrim($prefix, '\\');
        }
    }

    private static function loadMappedFile(string $prefix, string $relativeClass): bool {
        if (!isset(self::$namespaces[$prefix])) {
            return false;
        }

        foreach (self::$namespaces[$prefix] as $baseDir) {
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (self::requireFile($file)) {
                return true;
            }
        }

        return false;
    }

    private static function requireFile(string $file): bool {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}