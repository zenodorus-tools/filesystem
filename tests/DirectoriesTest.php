<?php namespace Zenodorus\Filesystem;

use \PHPUnit\Framework\TestCase;

class DirectoriesTest extends TestCase
{
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
            __DIR__
        );
        $child = sprintf(
            'files%1$sstar%1$strek%1$senterprise',
            DIRECTORY_SEPARATOR,
            __DIR__
        );
        $this->assertTrue(Directories::isBeneathReal(
            $child,
            $parent,
            __DIR__,
            __DIR__
        ));
    }
}
