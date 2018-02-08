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
     * @param string $sections      All strings you wish to concatenate.
     * @return string
     */
    public static function slash(string ...$sections)
    {
        $count = count($sections);

        // If we only got a single section, return it.
        if (1 === $count) {
            return $section[0];
        }
        // Somehow we got NO sections, so return null.
        elseif (0 >= $count || empty($sections)) {
            return null;
        }
        // Only two sections; concatenated and return!
        elseif (2 === $count) {
            return sprintf(
                "%s%s%s",
                rtrim(trim($sections[0]), '/\\'),
                DIRECTORY_SEPARATOR,
                ltrim(trim($sections[1]), '/\\')
            );
        }
        // Multiple sections, so let's get recursive!
        else {
            $append = array_shift($sections);
            $prepend = array_shift($sections);
            $compiled = static::slash($append, $prepend);

            return call_user_func_array(
                __METHOD__,
                array_merge([$compiled], $sections)
            );
        }
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
     * @return void
     */
    public static function resolve(string $path, bool $absolute = false)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
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
     * If a directory/file does not exist, this method returns `false`.
     *
     * This will always return an absolute path.
     *
     * @param string $path
     * @return bool|string      Real path if it exists, bool false otherwise.
     */
    public static function resolveReal(string $path, string $workingDir = null)
    {
        if (null === $workingDir
        /**
         * Path includes full (abosolute) path, which doesn't need
         * working directory.
         */
            && $real = realpath(Filesystem::resolve($path))) {
            return $real;
        } elseif (null !== $workingDir) {
        /**
         * ...Otherwise, add $workingDir before trying to find it.
         */
            $fullPath = Filesystem::slash($workingDir, $path);
            return realpath(Filesystem::resolve($fullPath));
        }

        return null;
    }
}
