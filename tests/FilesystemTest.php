<?php namespace Zenodorus;

use \PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    public function testSlash()
    {
        $expected = sprintf('star%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $prepend = sprintf('star%1$strek%1$s', DIRECTORY_SEPARATOR);
        $append = sprintf('%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::slash($prepend, $append));
    }

    public function testResolve()
    {
        $expected = sprintf('star%1$strek', DIRECTORY_SEPARATOR);
        $path = sprintf('star%1$swars%1$s..%1$strek', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::resolve($path));
    }

    public function testResolveAbsolute()
    {
        $expected = sprintf('%1$sstar%1$strek', DIRECTORY_SEPARATOR);
        $path = sprintf('%1$sstar%1$swars%1$s..%1$strek', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::resolve($path, true));
    }

    public function testResolveReal()
    {
        $expected = sprintf(
            '%2$s%1$sfiles%1$sstar%1$strek',
            DIRECTORY_SEPARATOR,
            __DIR__
        );
        $path = sprintf(
            'files%1$sstar%1$swars%1$s..%1$strek',
            DIRECTORY_SEPARATOR
        );
        $this->assertEquals($expected, Filesystem::resolveReal($path, __DIR__));
    }

    public function testResolveRealFull()
    {
        $expected = sprintf(
            '%2$s%1$sfiles%1$sstar%1$strek',
            DIRECTORY_SEPARATOR,
            __DIR__
        );
        $path = sprintf(
            '%2$s%1$sfiles%1$sstar%1$swars%1$s..%1$strek',
            DIRECTORY_SEPARATOR,
            __DIR__
        );
        $this->assertEquals($expected, Filesystem::resolveReal($path));
    }
}
