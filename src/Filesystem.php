<?php namespace Zenodorus;

class Filesystem
{
    /**
     * Combine strings with a directory separator.
     *
     * Accepts any number of strings.
     *
     * This is purely string manipulation: Although this function will likely
     * be used for paths, it does no path validation of any sort.
     *
     * @param string[] ...$sections All strings you wish to concatenate.
     * @return string
     */
    public static function slash(string ...$sections)
    {
        $count = count($sections);

        // If we only got a single section, return it.
        if (1 === $count) {
            return $sections[0];
        } // Somehow we got NO sections, so return null.
        elseif (0 >= $count || empty($sections)) {
            return null;
        } // Only two sections; concatenated and return!
        elseif (2 === $count) {
            $compiled = sprintf(
                "%s%s%s",
                trim($sections[0]),
                DIRECTORY_SEPARATOR,
                trim($sections[1])
            );
            // Check for double-slashes
            return preg_replace(
                '/\\\\{2,}|\/{2,}/',
                DIRECTORY_SEPARATOR,
                $compiled
            );
        } // Multiple sections, so let's get recursive!
        else {
            $append = array_shift($sections);
            $prepend = array_shift($sections);
            $compiled = static::slash($append, $prepend);

            return static::slashAr(array_merge([$compiled], $sections));
        }
    }

    /**
     * Run `Filesystem::slash()` on the contents of an array.
     *
     * This is for situations where it is more convenient to pass your list
     * of items to be slashed via an array rather than as separate args.
     *
     * @param array $array
     * @return string
     */
    public static function slashAr(array $array)
    {
        return call_user_func_array(__CLASS__.'::slash', $array);
    }

    /**
     * Normalize slashes for the current platform.
     *
     * @param string $path
     * @return string
     */
    public static function normalize(string $path)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Naively resolve paths.
     *
     * This attempts to discern the correct directory path from something that
     * contains relative indicators (i.e. `../`). It does not touch the actual
     * filesystem, so do not assume that these paths are accurate, or that they
     * even exist.
     *
     * Notably, this means that you can use this to deal with symlinks
     * (Specifically you can look at their locations without resolving their
     * targets.)
     *
     * @link http://www.php.net/manual/en/function.realpath.php#84012
     *
     * @param string $path
     * @param boolean $absolute     Set to `true` to prepend a directory
     *                              separator.
     * @return string
     */
    public static function resolve(string $path, bool $absolute = false)
    {
        $path = static::normalize($path);
        if (strpos($path, DIRECTORY_SEPARATOR) === 0) {
            // This was an absolute string, so keep it that way.
            $absolute = true;
        }
        /** @noinspection SpellCheckingInspection */
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $newPath = implode(DIRECTORY_SEPARATOR, $absolutes);

        if ($absolute === true) {
            $newPath = sprintf("%s%s", DIRECTORY_SEPARATOR, $newPath);
        }

        return $newPath;
    }

    /**
     * Resolve paths to real locations.
     *
     * A wrapper for `Filesystem::resolve()` that returns real paths. It will
     * fully evaluate symbolic links, so they will return their targets.
     *
     * If a directory/file does not exist, this method returns `null`.
     *
     * In certain situations, this may return `null` for files that are,
     * technically accessible (see php.net documentation for `file_exists()`).
     *
     * This will always return an absolute path.
     *
     * @see https://secure.php.net/manual/en/function.file-exists.php
     *
     * @param string $path
     * @param string|null $workingDir   The directory to use as the base.
     * @return bool|string              Real path if it exists, bool false otherwise.
     */
    public static function resolveReal(string $path, string $workingDir = null)
    {
        if (null === $workingDir
        /**
         * Path includes full (absolute) path, which doesn't need
         * working directory.
         */
            && file_exists($real = Filesystem::resolve($path))) {
            return $real;
        } elseif (null !== $workingDir) {
        /**
         * ...Otherwise, add $workingDir before trying to find it.
         */
            $fullPath = Filesystem::slash($workingDir, $path);
            $real = Filesystem::resolve($fullPath);
            return file_exists($real) ? $real : null;
        }

        return null;
    }

    /**
     * Removes all files and directories in a directory. BE CAREFUL!
     *
     * By default, this includes directory specified in `$dir`. Pass `false` to
     * the second argument to disable this behavior and leave `$dir` in place.
     *
     * @see https://stackoverflow.com/a/24563703
     *
     * @param string $dir
     * @param boolean $complete
     * @return boolean
     */
    public static function recursiveRemove(string $dir, bool $complete = true)
    {
        $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
        if ($complete) {
            rmdir($dir);
        }
        return true;
    }
}
