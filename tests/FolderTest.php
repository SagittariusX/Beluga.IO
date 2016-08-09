<?php


class FolderTest extends PHPUnit_Framework_TestCase
{

   protected function tearDown()
   {
      \Beluga\IO\Folder::Delete( __DIR__ . '/tmp/test5' );
      parent::tearDown();
   }

   public function setUp()
   {
      \Beluga\IO\Folder::Create( __DIR__ . '/tmp/test5' );
   }

   public function testUp()
   {
      $this->assertEquals( '/foo/bar', \Beluga\IO\Folder::Up( '/foo/bar/baz' ), 'Folder::Up() Test 1' );
      $this->assertEquals( '/foo/bar/', \Beluga\IO\Folder::Up( '/foo/bar/baz', 1, '/' ), 'Folder::Up() Test 2' );
      $this->assertEquals( '/foo', \Beluga\IO\Folder::Up( '/foo/bar/baz', 2 ), 'Folder::Up() Test 3' );
      $this->assertEquals( '/', \Beluga\IO\Folder::Up( '/foo/bar/baz', 3 ), 'Folder::Up() Test 4' );
      $this->assertEquals( '/', \Beluga\IO\Folder::Up( '/foo/bar/baz', 4 ), 'Folder::Up() Test 5' );
      $this->assertEquals( '', \Beluga\IO\Folder::Up( '', 10 ), 'Folder::Up() Test 6' );
      $this->assertEquals( '/', \Beluga\IO\Folder::Up( '', 10, '/' ), 'Folder::Up() Test 7' );
   }

   public function testGetFirstExisting()
   {
      $this->assertEquals( __DIR__, \Beluga\IO\Folder::GetFirstExisting( __DIR__ . '/foo/bar' ), 'Folder::GetFirstExisting() Test 1' );
      $this->assertEquals( __DIR__ . '/tmp/test1', \Beluga\IO\Folder::GetFirstExisting( __DIR__ . '/tmp/test1/foo/bar' ), 'Folder::Up() Test 2' );
      $this->assertEquals( __DIR__ . '/tmp/test1', \Beluga\IO\Folder::GetFirstExisting( __DIR__ . '/tmp\\test1/foo/bar' ), 'Folder::Up() Test 3' );
   }

   public function testCanCreate()
   {
      $this->assertTrue( \Beluga\IO\Folder::CanCreate( __DIR__ . '/tmp/test3' ), 'Folder::CanCreate() Test 1' );
      $this->assertFalse( \Beluga\IO\Folder::CanCreate( __DIR__ . '/tmp/test1' ), 'Folder::CanCreate() Test 2' );
      $this->assertFalse( \Beluga\IO\Folder::CanCreate( '/var/log/foobar' ), 'Folder::CanCreate() Test 3' );
   }

   /**
    * @expectedE xception Beluga\IO\IOError
    */
   public function testCreate1()
   {
      #\Beluga\IO\Folder::Create( '/var/log/foobar' );
   }

   /**
    * @expectedE xception Beluga\IO\IOError
    */
   public function testCreate2()
   {
      #\Beluga\IO\Folder::Create( '' );
   }

   public function testCreate3()
   {
      \Beluga\IO\Folder::Create( __DIR__ . '/tmp/test4' );
      $this->assertTrue( true, 'Folder::Create() Test 2-try' );
   }

   public function testDelete()
   {

      try
      {
         \Beluga\IO\Folder::Delete( __DIR__ . '/tmp/test5' );
         $this->assertTrue( true, 'Folder::Delete() Test 1-try' );
      }
      catch ( \Throwable $ex )
      {
         $this->assertTrue( false, 'Folder::Delete() Test 1-catch' );
      }

   }

   public function testListAllFiles()
   {
      $this->assertEquals( [], \Beluga\IO\Folder::ListAllFiles( __DIR__ . '/tmp/test1', false ), 'Folder::ListAllFiles() Test 1' );
      $required = [
         __DIR__ . '/tmp/test1/test11/test11.txt',
         __DIR__ . '/tmp/test1/test12/test121/test121.txt'
      ];
      $this->assertEquals( $required, \Beluga\IO\Folder::ListAllFiles( __DIR__ . '/tmp/test1', true ), 'Folder::ListAllFiles() Test 1' );

   }

   public function testGetRealPath()
   {

   }

   public function testClear()
   {

   }

   public function testMove()
   {

   }

   public function testMoveContents()
   {

   }

   public function testCopy()
   {

   }

   public function testZip()
   {

   }/**/

}
