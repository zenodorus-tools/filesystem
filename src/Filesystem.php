<?php namespace Zenodorus;

class Filesystem
{
    /**
     * Combine two strings with a directory separator.
     *
     * This is purely string manipulation: Although this function will likely
     * be used for paths, it does no path validation of any sort.
     *
     * @param string $prepend
     * @param string $append
     * @return string
     */
    public static function slash(string $prepend, string $append)
    {
        $prepend = rtrim(trim($prepend), '/\\');
        $append = ltrim(trim($append), '/\\');
        return sprintf("%s%s%s", $prepend, DIRECTORY_SEPARATOR, $append);
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

        return false;
    }
}
