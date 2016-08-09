<?php


use Beluga\IO\Path;


class PathTest extends PHPUnit_Framework_TestCase
{

   public function setUp() { }

   public function testCombine()
   {
      $this->assertEquals( '/foo/bar', Path::Combine( '/foo/bar' ), 'Path::Combine(…) Test 1 fails' );
      $this->assertEquals( '/foo/bar', Path::Combine( '/foo', 'bar' ), 'Path::Combine(…) Test 2 fails' );
      $this->assertEquals( '/foo/bar', Path::Combine( '/foo/', 'bar' ), 'Path::Combine(…) Test 3 fails' );
      $this->assertEquals( '/foo/bar', Path::Combine( '/foo/', '/bar/' ), 'Path::Combine(…) Test 4 fails' );
      $this->assertEquals( '/foo/bar/baz', Path::Combine( '/foo/', 'bar/', '/baz/' ), 'Path::Combine(…) Test 5 fails' );
      $this->assertEquals( '/foo/bar/baz', Path::Combine( '/foo/', 'bar\\', '/baz/' ), 'Path::Combine(…) Test 6 fails' );
   }

   public function testNormalize()
   {
      $this->assertEquals( '/foo/bar', Path::Normalize( '/foo/bar/' ), 'Path::Normalize(…) Test 1 fails' );
      $this->assertEquals( '/foo/bar', Path::Normalize( '/foo\\bar' ), 'Path::Normalize(…) Test 2 fails' );
      $this->assertEquals( '/foo/bar', Path::Normalize( '/foo/./bar' ), 'Path::Normalize(…) Test 3 fails' );
   }

   public function testIsAbsolute()
   {

      $this->assertFalse( Path::IsAbsolute( null ), 'Path::IsAbsolute(…) Test 1 fails' );

      if ( Beluga\IO\File::IS_WIN )
      {
         $this->assertFalse( Path::IsAbsolute( '/foo/bar', true ), 'Path::IsAbsolute(…) Test 2 fails' );
         $this->assertTrue( Path::IsAbsolute( 'C:/foo/bar', true ), 'Path::IsAbsolute(…) Test 3 fails' );
         $this->assertTrue( Path::IsAbsolute( '/foo/bar', false ), 'Path::IsAbsolute(…) Test 4 fails' );
      }
      else
      {
         $this->assertTrue( Path::IsAbsolute( '/foo/bar', true ), 'Path::IsAbsolute(…) Test 2 fails' );
         $this->assertFalse( Path::IsAbsolute( 'C:/foo/bar', true ), 'Path::IsAbsolute(…) Test 3 fails' );
         $this->assertTrue( Path::IsAbsolute( 'C:/foo/bar', false ), 'Path::IsAbsolute(…) Test 4 fails' );
      }

   }

   public function testUnixize()
   {
      $this->assertEquals( '/foo/bar', Path::Unixize( '/foo/bar' ), 'Path::Unixize(…) Test 1 fails' );
      $this->assertEquals( '/foo/bar/', Path::Unixize( '/foo\\bar/' ), 'Path::Unixize(…) Test 2 fails' );
      $this->assertEquals( 'C:/foo/bar', Path::Unixize( 'C:\\foo\\bar' ), 'Path::Unixize(…) Test 3 fails' );
      $this->assertEquals( '', Path::Unixize( null ), 'Path::Unixize(…) Test 4 fails' );
   }

   public function testRemoveWorkingDir()
   {
      $this->assertEquals( 'tmp/test1/test11', Path::RemoveWorkingDir( __DIR__ . '/tmp/test1/test11' ), 'Path::RemoveWorkingDir(…) Test 1 fails' );
      $this->assertEquals( '', Path::RemoveWorkingDir( null ), 'Path::RemoveWorkingDir(…) Test 2 fails' );
      $this->assertEquals( '/foo/bar', Path::RemoveWorkingDir( '/foo/bar' ), 'Path::RemoveWorkingDir(…) Test 3 fails' );
   }

}
