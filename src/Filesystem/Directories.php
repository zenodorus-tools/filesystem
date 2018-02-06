<?php namespace Zenodorus\Filesystem;

use \Zenodorus\Filesystem;

class Directories
{
    /**
     * Determine if a path is beneath another path.
     *
     * This is strictly string comparison; it does no path validation or path
     * checking.
     *
     * @param string $child
     * @param string $parent
     * @return boolean
     */
    public static function isBeneath(string $child, string $parent)
    {
        if ((strlen($child) >= strlen($parent))
            && strpos($child, $parent) === 0) {
                return true;
        }

        return false;
    }

    /**
     * Determine if a path is beneath another path.
     *
     * This is strictly string comparison via `Filesystem::resolve()`; it does
     * no path validation or path checking.
     *
     * @see Filesystem::resolve()
     *
     * @param string $child
     * @param string $parent
     * @return boolean
     */
    public static function isBeneathResolve(
        string $child,
        string $parent
    ) {
        $args = [
            'child' => $child,
            'parent' => $parent,
        ];

        array_walk($args, function (&$dir, $name) {
            $dir = Filesystem::resolve($dir);
        });

        return Directories::isBeneath($args['child'], $args['parent']);
    }

    /**
     * Determine if a real path is beneath another path.
     *
     * This still compares stringes, but it does so on paths that have been
     * run through `realpath()` so in theory it should be "real" information.
     *
     * @param string $child
     * @param string $parent
     * @param string $childDir
     * @param string $parentDir
     * @return boolean
     */
    public static function isBeneathReal(
        string $child,
        string $parent,
        string $childDir = null,
        string $parentDir = null
    ) {
        $realChildPath = Filesystem::resolveReal($child, $childDir);
        $realParentPath = Filesystem::resolveReal($parent, $parentDir);

        if ($realChildPath && $realParentPath) {
            return Directories::isBeneath($realChildPath, $realParentPath);
        }

        return false;
    }
}
