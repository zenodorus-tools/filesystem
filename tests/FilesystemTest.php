<?php namespace Zenodorus;

use \PHPUnit\Framework\TestCase;
use \Symfony\Component\Filesystem\Filesystem as SymFile;

class FilesystemTest extends TestCase
{
    const TESTDIRS = [
        'files/star',
        'files/star/wars',
        'files/star/trek',
        'files/magic',
    ];

    const TESTFILES = [
        'files/star/wars/millenium.falcon',
        'files/star/trek/enterprise',
        'files/star/gate',
        'files/magic/hands',
        'files/magic/mike'
    ];

    protected $dir;

    protected function setUp()
    {
        $fs = new SymFile();

        $this->dir = dirname(__FILE__);
        $root = $this->dir;

        $fs->mkdir(array_map(function($dir) use ($root) {
            return $root . DIRECTORY_SEPARATOR . $dir;
        }, $this::TESTDIRS));
        $fs->touch(array_map(function($file) use ($root) {
            return $root . DIRECTORY_SEPARATOR . $file;
        }, $this::TESTFILES));
    }

    protected function tearDown()
    {
        Filesystem::recursiveRemove(Filesystem::slash($this->dir, 'files'));
    }

    public function testSlash()
    {
        $expected = sprintf('star%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $prepend = sprintf('star%1$strek%1$s', DIRECTORY_SEPARATOR);
        $append = sprintf('%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::slash($prepend, $append));
    }
    
    public function testSlashLeading()
    {
        $expected = sprintf('%1$sstar%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $prepend = sprintf('%1$sstar%1$strek%1$s', DIRECTORY_SEPARATOR);
        $append = sprintf('%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::slash($prepend, $append));
    }
    
    public function testSlashTrailing()
    {
        $expected = sprintf('star%1$strek%1$senterprise%1$s', DIRECTORY_SEPARATOR);
        $prepend = sprintf('star%1$strek%1$s', DIRECTORY_SEPARATOR);
        $append = sprintf('%1$senterprise%1$s', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::slash($prepend, $append));
    }
    
    public function testSlashMultipleRemove()
    {
        $expected = sprintf('star%1$strek%1$senterprise%1$s', DIRECTORY_SEPARATOR);
        $prepend = sprintf('star%1$strek%1$s%1$s', DIRECTORY_SEPARATOR);
        $append = sprintf('%1$senterprise%1$s', DIRECTORY_SEPARATOR);
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

    public function testNormalize()
    {
        $path = "/something/is/in\here.file";
        $expected = sprintf('%1$ssomething%1$sis%1$sin%1$shere.file', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::normalize($path));
    }

    public function testResolve()
    {
        $expected = sprintf('star%1$strek', DIRECTORY_SEPARATOR);
        $path = sprintf('star%1$swars%1$s..%1$strek', DIRECTORY_SEPARATOR);
        $this->assertEquals($expected, Filesystem::resolve($path));
    }

    public function testResolveImpliedAbsolute()
    {
        $expected = sprintf('%1$sstar%1$strek', DIRECTORY_SEPARATOR);
        $path = sprintf('%1$sstar%1$swars%1$s..%1$strek', DIRECTORY_SEPARATOR);
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
            $this->dir
        );
        $path = sprintf(
            'files%1$sstar%1$swars%1$s..%1$strek',
            DIRECTORY_SEPARATOR
        );
        $this->assertEquals(
            $expected,
            Filesystem::resolveReal($path, $this->dir),
            sprintf(
                'Not matching! %sexpected: %s %spath: %s, %s$this->dir: %s',
                PHP_EOL,
                $expected,
                PHP_EOL,
                $path,
                PHP_EOL,
                $this->dir
            )
        );
    }

    public function testResolveRealFull()
    {
        $expected = sprintf(
            '%2$s%1$sfiles%1$sstar%1$strek',
            DIRECTORY_SEPARATOR,
            $this->dir
        );
        $path = sprintf(
            '%2$s%1$sfiles%1$sstar%1$swars%1$s..%1$strek',
            DIRECTORY_SEPARATOR,
            $this->dir
        );
        $this->assertEquals(
            $expected,
            Filesystem::resolveReal($path),
            sprintf(
                'Not matching! %sexpected: %s %spath: %s, %s$this->dir: %s',
                PHP_EOL,
                $expected,
                PHP_EOL,
                $path,
                PHP_EOL,
                $this->dir
            )
        );
    }

    public function testRecursiveRemove()
    {
        $dir = Filesystem::slash($this->dir, 'some', 'where');
        $file = Filesystem::slash(
            $this->dir,
            'some',
            'where',
            'out.there'
        );
        mkdir($dir, 0777, true);
        touch($file);
        $this->assertFileExists($file, 'File was not created.');
        Filesystem::recursiveRemove(
            Filesystem::slash($this->dir, 'some')
        );
        $this->assertFileNotExists(
            $file,
            'Files & directories were not deleted.'
        );
    }

    public function testRecursiveRemoveLeave()
    {
        $dir = Filesystem::slash($this->dir, 'some', 'where');
        $file = Filesystem::slash(
            $this->dir,
            'some',
            'where',
            'out.there'
        );
        mkdir($dir, 0777, true);
        touch($file);
        $this->assertFileExists($file, 'File was not created.');
        Filesystem::recursiveRemove(
            Filesystem::slash($this->dir, 'some'),
            false
        );
        $this->assertFileNotExists(
            $file,
            'Files & directories were not deleted.'
        );
        $this->assertFileExists(
            Filesystem::slash($this->dir, 'some')
        );
        rmdir(Filesystem::slash($this->dir, 'some'));
    }
}
