<?php namespace Zenodorus\Filesystem;

use \PHPUnit\Framework\TestCase;
use \Symfony\Component\Filesystem\Filesystem as SymFile;
use Zenodorus\Filesystem as Filesystem;

class DirectoriesTest extends TestCase
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

    public function testIsBeneath()
    {
        $parent = sprintf('star%1$strek', DIRECTORY_SEPARATOR);
        $child = sprintf('star%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertTrue(Directories::isBeneath($child, $parent));
    }

    public function testIsBeneathResolve()
    {
        $parent = sprintf('star%1$swars%1$s..%1$strek', DIRECTORY_SEPARATOR);
        $child = sprintf('star%1$strek%1$senterprise', DIRECTORY_SEPARATOR);
        $this->assertTrue(Directories::isBeneathResolve($child, $parent));
    }
    
    public function testisBeneathReal()
    {
        $parent = sprintf(
            'files%1$sstar%1$swars%1$s..%1$strek',
            DIRECTORY_SEPARATOR,
            $this->dir
        );
        $child = sprintf(
            'files%1$sstar%1$strek%1$senterprise',
            DIRECTORY_SEPARATOR,
            $this->dir
        );
        $this->assertTrue(Directories::isBeneathReal(
            $child,
            $parent,
            $this->dir,
            $this->dir
        ));
    }
}
