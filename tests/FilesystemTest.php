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

    public function testSlashSingle()
    {
        $this->assertEquals(
            'enterprise',
            Filesystem::slash('enterprise')
        );
    }

    public function testSlashMultiple()
    {
        $expected = sprintf('star%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertEquals(
            $expected,
            Filesystem::slash('star', 'trek', 'enterprise')
        );
    }
    
    public function testSlashAr()
    {
        $expected = sprintf('star%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertEquals(
            $expected,
            Filesystem::slashAr(['star', 'trek', 'enterprise'])
        );
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
        $this->assertEquals(
            $expected,
            Filesystem::resolveReal($path, __DIR__),
            sprintf(
                'Not matching! %sexpected: %s %spath: %s, %s__DIR__: %s',
                PHP_EOL,
                $expected,
                PHP_EOL,
                $path,
                PHP_EOL,
                __DIR__
            )
        );
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
        $this->assertEquals(
            $expected,
            Filesystem::resolveReal($path),
            sprintf(
                'Not matching! %sexpected: %s %spath: %s, %s__DIR__: %s',
                PHP_EOL,
                $expected,
                PHP_EOL,
                $path,
                PHP_EOL,
                __DIR__
            )
        );
    }

    public function testRecursiveRemove()
    {
        $dir = Filesystem::slash(__DIR__, 'some', 'where');
        $file = Filesystem::slash(
            __DIR__,
            'some',
            'where',
            'out.there'
        );
        mkdir($dir, 0777, true);
        touch($file);
        $this->assertFileExists($file, 'File was not created.');
        Filesystem::recursiveRemove(
            Filesystem::slash(__DIR__, 'some')
        );
        $this->assertFileNotExists(
            $file,
            'Files & directories were not deleted.'
        );
    }

    public function testRecursiveRemoveLeave()
    {
        $dir = Filesystem::slash(__DIR__, 'some', 'where');
        $file = Filesystem::slash(
            __DIR__,
            'some',
            'where',
            'out.there'
        );
        mkdir($dir, 0777, true);
        touch($file);
        $this->assertFileExists($file, 'File was not created.');
        Filesystem::recursiveRemove(
            Filesystem::slash(__DIR__, 'some'),
            false
        );
        $this->assertFileNotExists(
            $file,
            'Files & directories were not deleted.'
        );
        $this->assertFileExists(
            Filesystem::slash(__DIR__, 'some')
        );
        rmdir(Filesystem::slash(__DIR__, 'some'));
    }
}
