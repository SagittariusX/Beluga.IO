<?php
/**
 * In this file the class '\Beluga\IO\File' is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-08
 * @subpackage     IO
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\IO;


use Beluga\MissingExtensionError;


/**
 * This class defines an object for simple handling file in OOP manner. It also gives you some
 * static methods for file handling.
 *
 * A instance can only be created by static {@see \Beluga\IO\File::CreateNew} and {@see \Beluga\IO\File::OpenRead}
 * methods.
 *
 * @since      v0.1
 */
class File
{


   // <editor-fold desc="// = = = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The file path of current instance.
    *
    * @var string
    */
   private $file;

   /**
    * Access type of current file. See ::ACCESS_* class constants.
    *
    * @var string
    */
   private $access;

   /**
    * The current file pointer resource.
    *
    * @var resource
    */
   private $fp;

   /**
    * @var int
    */
   private $mode;

   /**
    * Is this file access an looked access?
    *
    * @var boolean
    */
   private $locked;

   # </editor-fold>


   // <editor-fold desc="// = = = =   C L A S S   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Reading file access.
    */
   const ACCESS_READ = 'read';

   /**
    * Writing file access.
    */
   const ACCESS_WRITE = 'write';

   /**
    * Reading and writing file access.
    */
   const ACCESS_READWRITE = 'read and write';

   const IS_WIN = ( DIRECTORY_SEPARATOR == '\\' );

   # </editor-fold>


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param string   $file
    * @param string   $access
    * @param resource $fp
    * @param integer  $mode
    * @param boolean  $locked
    */
   private function __construct( string $file, string $access, $fp, int $mode, bool $locked )
   {

      $this->access = $access;
      $this->file   = $file;
      $this->fp     = $fp;
      $this->mode   = $mode;
      $this->locked = $locked;

   }

   /**
    * The destructor.
    */
   public function  __destruct()
   {

      $this->close();

   }

   # </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   # <editor-fold desc="BOOLEAN METHODS">

   /**
    * Returns, if currently a usable file resource is open.
    *
    * @return boolean
    */
   public function isOpen() : bool
   {
      
      return \is_resource( $this->fp );
      
   }

   /**
    * Returns if reading is enabled.
    *
    * @return boolean
    */
   public function hasReadAccess() : bool
   {

      return $this->isOpen()
         && (
               $this->access == static::ACCESS_READ
            || $this->access == static::ACCESS_READWRITE
         );

   }

   /**
    * Returns if writing is enabled.
    *
    * @return boolean
    */
   public function hasWriteAccess() : bool
   {

      return $this->isOpen()
         && (
               $this->access == static::ACCESS_WRITE
            || $this->access == static::ACCESS_READWRITE
         );

   }

   # </editor-fold>

   # <editor-fold desc="READING METHODS">

   /**
    * Reads the next line and returns it.
    *
    * @param  boolean $removeNewlines    Remove trailing line breaks? (Defaults to TRUE)
    * @param  boolean $fast              Read fast without some checks? (Defaults to FALSE)
    * @return FALSE|string               Returns the resulting line, or (boolean)FALSE if EOF is reached.
    * @throws FileAccessError If reading is not allowed or if it fails.
    */
   public function readLine( bool $removeNewlines = true, bool $fast = false )
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasReadAccess() )
         {
            throw FileAccessError::Read(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not read!', $this->access )
            );
         }
      }

      try
      {
         
         if ( \feof( $this->fp ) )
         {
            return false;
         }
         
         if ( ! $removeNewlines )
         {
            return \fgets( $this->fp );
         }
         
         return \rtrim( \fgets( $this->fp ), "\r\n" );
         
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_READ,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Reads the next $count bytes and returns it.
    *
    * @param  integer $count             The count of bytes to read (Defaults to 1)
    * @param  boolean $fast              Read fast without some checks? (Defaults to FALSE)
    * @return FALSE|string               Returns the resulting string, or (boolean)FALSE if EOF is reached.
    * @throws FileAccessError If reading is not allowed or if it fails.
    */
   public function read( int $count = 1, bool $fast = false )
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( !$this->hasReadAccess())
         {
            throw FileAccessError::Read(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not read!', $this->access )
            );
         }
      }

      try
      {
         if ( \feof( $this->fp ) )
         {
            return false;
         }
         return \fread( $this->fp, $count );
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_READ,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Reads the next char and returns it.
    *
    * @param  boolean $fast              Read fast without some checks? (Defaults to FALSE)
    * @return FALSE|string               Returns the resulting char, or (boolean)FALSE if EOF is reached.
    * @throws FileAccessError If reading is not allowed or if it fails.
    */
   public function readChar( bool $fast = false )
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasReadAccess() )
         {
            throw FileAccessError::Read(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not read!', $this->access )
            );
         }
      }

      try
      {
         if ( \feof( $this->fp ) )
         {
            return false;
         }
         return \fgetc( $this->fp );
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_READ,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Reads a CSV row and returns it.
    *
    * @param  string  $delimiter          The CSV element separator (Defaults to ',')
    * @param  integer $maxLineLength      The max allowed line length (Defaults to 1024)
    * @param  boolean $fast               Read fast without some checks? (Defaults to FALSE)
    * @return array|FALSE                 Returns the resulting row, or (boolean)FALSE if EOF is reached.
    * @throws FileAccessError If reading is not allowed or if it fails.
    */
   public function readCsv( string $delimiter = ',', int $maxLineLength = 1024, bool $fast = false )
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasReadAccess() )
         {
            throw FileAccessError::Read(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not read!', $this->access )
            );
         }
      }

      try
      {
         return \fgetcsv( $this->fp, $maxLineLength, $delimiter );
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_READ,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Reads all from current pointer to EOF.
    *
    * @param  boolean $getLines          Return the result als lines array? (Default=FALSE)
    * @param  boolean $removeNewlines    Remove line breaks if $getLines is TRUE? (Default=TRUE)
    * @param  boolean $fast              Read fast without some checks? (Defaults to FALSE)
    * @return array|string|FALSE
    * @throws FileAccessError If reading is not allowed or if it fails.
    */
   public function readToEnd( bool $getLines = false, bool $removeNewlines = true, bool $fast = false )
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasReadAccess() )
         {
            throw FileAccessError::Read(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not read!', $this->access )
            );
         }
      }

      $result = '';
      if ( $getLines )
      {
         $result = array();
      }
      while ( false !== ( $line = $this->readLine( $removeNewlines && $getLines, true ) ) )
      {
         if ( $getLines )
         {
            $result[] = $line;
         }
         else
         {
            $result .= $line;
         }
      }

      return $result;

   }

   # </editor-fold>

   # <editor-fold desc="WRITING METHODS">

   /**
    * Writes a a string to current file, with a trailing linebreak.
    *
    * @param  string  $str               The string to write.
    * @param  string  $newline           Use this linebreak (Default=\n)
    * @param  boolean $fast              Write fast without some checks? (Defaults to FALSE)
    * @return boolean
    * @throws FileAccessError If writing is not allowed or if it fails.
    */
   public function writeLine( string $str, string $newline = "\n", bool $fast = false ) : bool
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasWriteAccess() )
         {
            throw FileAccessError::Write(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not write!', $this->access )
            );
         }
      }

      try { \fwrite( $this->fp, $str . $newline ); }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_WRITE,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

      return true;

   }

   /**
    * Writes a a string to current file, with a trailing linebreak. (Some checks are disabled)
    *
    * @param  string  $str               The string to write.
    * @param  string  $newline           Use this linebreak (Default=\n)
    * @throws FileAccessError If writing is not allowed or if it fails.
    */
   public function writeLineFast( string $str, string $newline = "\n" )
   {

      $this->writeLine( $str, $newline, true );

   }

   /**
    * Writes a a string or lines array to current file.
    *
    * @param  mixed   $strOrArray         The string to write, or a lines array to write.
    * @param  string  $newline            Use this linebreak (Default=\n)
    * @param  boolean $fast               Write fast without some checks? (Defaults to FALSE)
    * @return boolean
    * @throws FileAccessError If writing is not allowed or if it fails.
    */
   public function write( $strOrArray, string $newline = "\n", bool $fast = false ) : bool
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasWriteAccess() )
         {
            throw FileAccessError::Write(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not write!', $this->access )
            );
         }
      }

      try
      {
         if ( \is_array( $strOrArray ) )
         {
             foreach ( $strOrArray as $line )
             {
                 \fwrite( $this->fp, \rtrim($line, "\r\n") . $newline );
             }
         }
         else { \fwrite( $this->fp, $strOrArray ); }
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_WRITE,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

      return true;

   }

   /**
    * Writes a a string or lines array to current file.
    *
    * @param  string  $chars             The string to write, or a lines array to write.
    * @param  boolean $fast              Write fast without some checks? (Defaults to FALSE)
    * @throws FileAccessError If writing is not allowed or if it fails.
    */
   public function writeChars( string $chars, bool $fast = true )
   {
      $this->write( $chars, '', $fast );
   }

   /**
    * Write a csv format data row, defined as array.
    *
    * @param  array   $dataRow           The data row to write (numeric indicated array)
    * @param  string  $delimiter         The CSV column delimiter char. (default=',')
    * @param  boolean $fast              Write fast without some checks? (Defaults to FALSE)
    * @return boolean
    * @throws FileAccessError If writing is not allowed or if it fails.
    */
   public function writeCsv( array $dataRow, string $delimiter = ',', bool $fast = false ) : bool
   {

      if ( ! $fast )
      {
         // No fast (insecure) access = do the required checks
         if ( ! $this->isOpen() )
         {
            return false;
         }
         if ( ! $this->hasWriteAccess() )
         {
            throw FileAccessError::Write(
               'IO',
               $this->file,
               \sprintf( 'Current mode of opened file is "%s" and not write!', $this->access )
            );
         }
      }

      try { \fputcsv( $this->fp, $dataRow, $delimiter ); }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $this->file,
            FileAccessError::ACCESS_WRITE,
            'Writing of CSV-Data fails.',
            \E_USER_ERROR,
            $ex
         );
      }

      return true;

   }

   # </editor-fold>

   # <editor-fold desc="PointerPosition">

   /**
    * Returns the position of the current file pointer.
    *
    * @return integer|FALSE If there is no usable pointer, FALSE is returned
    */
   public function getPointerPosition()
   {

      if ( ! \is_resource( $this->fp ) )
      {
         return false;
      }

      return \ftell( $this->fp );

   }

   /**
    * Sets a new file pointer position.
    *
    * @param  integer $offset
    * @return boolean
    */
   public function setPointerPosition( int $offset = 0 ) : bool
   {

      if ( ! \is_resource( $this->fp ) )
      {
         return false;
      }

      return (bool) \fseek( $this->fp, $offset );

   }

   /**
    * Sets the file pointer position to the end of the file.
    *
    * @return boolean
    */
   public function setPointerPositionToEndOfFile() : bool
   {

      if ( ! \is_resource( $this->fp ) )
      {
         return false;
      }

      return (bool) \fseek( $this->fp, 0, \SEEK_END );

   }

   # </editor-fold>

   /**
    * Closes the current file pointer.
    */
   public function close()
   {

      if ( ! \is_resource( $this->fp ) )
      {
         return;
      }

      \fclose( $this->fp );
      $this->fp = null;

      if ( $this->access == static::ACCESS_WRITE || $this->access == static::ACCESS_READWRITE )
      {

         if ( ! \is_null( $this->mode ) && ! static::IS_WIN )
         {
            \chmod( $this->file, $this->mode );
         }

      }

   }

   /**
    * Returns the current used file path.
    *
    * @return string
    */
   public final function getFileName() : string
   {

      return $this->file;

   }

   # </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   # <editor-fold desc="P U B L I C   S T A T I C   I N S T A N C E   M E T H O D S">

   /**
    * Creates a new file and returns the associated {@see \Beluga\IO\File} instance.
    *
    * If $overwrite is FALSE and the file already exists, a {@see \Beluga\IO\FileAlreadyExistsException} is thrown.
    *
    * @param  string  $file              The path of the file to create.
    * @param  boolean $overwrite         Overwrite the file if it exists? (default=TRUE)
    * @param  boolean $lock              Use file locking? (default=FALSE)
    * @param  boolean $lockExclusive     If $lock is TRUE, use exclusive file locking? (default=FALSE)
    * @param  integer $mode              The file access mode (only used by unixoids) (default=0755)
    * @return \Beluga\IO\File                Returns the newly created File instance
    * @throws FileAlreadyExistsError If file exists and overwriting is disabled.
    * @throws FileAccessError        On errors while opening the file pointer
    */
   public static function CreateNew (
      string $file, bool $overwrite = true, bool $lock = false, bool $lockExclusive = false, $mode = 0755 )
      : File
   {

      if ( \file_exists( $file ) )
      {
         // The file already exists
         if ( ! $overwrite )
         {
            // Overwriting is disabled
            throw new FileAlreadyExistsError(
               'IO',
               $file,
               'Creation of this file fails!'
            );
         }
      }

      try
      {
         // Open the file pointer
         $fp = \fopen( $file, 'wb' );
         $locked = false;
         if ( $lock )
         {
            // Remember the locked state if file lock should be used
            $locked = \flock( $fp, $lockExclusive ? \LOCK_EX : \LOCK_SH );
         }
         // Return the new instance
         return new File( $file, static::ACCESS_WRITE, $fp, $mode, $locked );
      }
      catch ( \Throwable $ex )
      {
         // Handle catched Exceptions
         throw new FileAccessError(
            'IO',
            $file,
            FileAccessError::ACCESS_CREATE,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Opens a existing file for writing and sets the file pointer to the end of the file and
    * returns the associated {@see \Beluga\IO\File} instance. If the file does not exist it will be created.
    *
    * @param  string  $file                     The path of the file to open or create.
    * @param  boolean $lock                     Use file locking? (default=FALSE)
    * @param  boolean $lockExclusive            If $lock is TRUE, use exclusive file locking? (default=FALSE)
    * @param  integer $mode                     The file access mode (only used with unixoids) (default=0755)
    * @return \Beluga\IO\File                       Returns the File instance
    * @throws FileAccessError        On errors while opening the file pointer
    */
   public static function OpenForAppend ( string $file, bool $lock = false, bool $lockExclusive = false, $mode = 0755 )
      : File
   {

      if ( ! \file_exists( $file ) )
      {
         return static::CreateNew( $file, true, $lock, $lockExclusive, $mode );
      }

      try
      {
         $fp = \fopen( $file, 'ab' );
         $locked = false;
         if ( $lock )
         {
            $locked = \flock( $fp, $lockExclusive ? \LOCK_EX : \LOCK_SH );
         }
         return new File( $file, static::ACCESS_WRITE, $fp, $mode, $locked );
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $file,
            FileAccessError::ACCESS_CREATE,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Opens a existing file for reading and returns the associated {@see \Beluga\IO\File} instance.
    *
    * @param  string  $file                The path of the file to read.
    * @param  boolean $lock                Use file locking? (default=FALSE)
    * @param  boolean $lockExclusive       If $lock is TRUE, use exclusive file locking? (default=FALSE)
    * @return \Beluga\IO\File                  Returns the newly created File instance
    * @throws FileNotFoundError If the file not exists
    * @throws FileAccessError   On errors while opening the file pointer
    */
   public static function OpenRead( string $file, bool $lock = false, bool $lockExclusive = false ) : File
   {

      if ( ! \file_exists( $file ) )
      {
         throw new FileNotFoundError(
            'IO',
            $file,
            'Open file for reading fails.'
         );
      }

      try
      {
         $fp = \fopen( $file, 'rb' );
         $locked = false;
         if ( $lock )
         {
            $locked = \flock( $fp, $lockExclusive ? \LOCK_EX : \LOCK_SH );
         }
         return new File( $file, File::ACCESS_READ, $fp, 0750, $locked );
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'IO',
            $file,
            FileAccessError::ACCESS_READ,
            null,
            \E_USER_ERROR,
            $ex
         );
      }

   }

   # </editor-fold>

   # <editor-fold desc="Create + Delete + Move + Copy + ReadFirstBytes">

   /**
    * Creates a new file with the defined content.
    *
    * If $overwrite is FALSE and the file already exists, a {@see \Beluga\IO\FileAlreadyExistsException} is thrown.
    *
    * @param  string       $file                The path of the file to create.
    * @param  integer      $mode                The file access mode (only used by unixoids) (default=0750)
    * @param  boolean      $overwrite           Overwrite the file if it exists? (default=TRUE)
    * @param  string|array $contents            The contents of the file (string or lines array) (default='')
    * @throws FileAlreadyExistsError If file exists and overwriting is disabled.
    * @throws FileAccessError        On errors while opening the file pointer
    */
   public static function Create( string $file, $mode = 0750, bool $overwrite = true, $contents = '' )
   {

      $f = File::CreateNew( $file, $overwrite, true, true, $mode );
      $f->write( $contents );
      $f->close();

   }

   /**
    * Deletes the defined file.
    *
    * @param  string $file The path of the file to delete
    * @throws \Beluga\IO\IOError
    */
   public static function Delete( string $file )
   {

      if ( ! \file_exists( $file ) )
      {
         // No deleting required
         return;
      }

      try
      {
         \unlink( $file );
         \clearstatcache();
         if ( ! \file_exists( $file ) )
         {
            // Deleting was successfull
            return;
         }
      }
      catch ( \Throwable $ex ) { }

      if ( static::IS_WIN )
      {
         $f = \escapeshellarg( $file );
         try { \exec( "del {$f}" ); }
         catch ( \Throwable $ex )
         {
            throw new IOError( 'IO', $file, 'Deleting the defined file fails!', \E_USER_ERROR, $ex );
         }
         \clearstatcache();
         if ( \file_exists( $file ) )
         {
            throw new IOError( 'IO', $file, 'Deleting the defined file fails! No known method works.' );
         }
         return;
      }

      try { \exec( "unlink {$file}" ); }
      catch ( \Throwable $ex )
      {
         throw new IOError( 'IO', $file, 'Deleting the defined file fails!', \E_USER_ERROR, $ex );
      }

      \clearstatcache();

      if ( \file_exists( $file ) )
      {
         throw new IOError( 'IO', $file, 'Deleting the defined file fails!' );
      }

   }

   /**
    * Moves the defined file to a new location $targetFile. (Also known as renaming ;-) )
    *
    * If $replace is FALSE it only does the job if the $targetFile not exists, otherwise a
    * {@see \Beluga\IO\FileAlreadyExistsException} is thrown.
    *
    * @param  string $srcFile                   The file to move.
    * @param  string $targetFile                The target file path.
    * @param  bool   $replace                   Replace the target if it exists? (default=true)
    * @throws \Beluga\IO\FileNotFoundError      If the $srcFile does not exist.
    * @throws \Beluga\IO\FileAlreadyExistsError If $targetFile exists and overwriting is disabled.
    * @throws \Beluga\IO\IOError
    */
   public static function Move( string $srcFile, string $targetFile, bool $replace = true )
   {

      static::Copy( $srcFile, $targetFile, $replace );
      static::Delete( $srcFile );

   }

   /**
    * Copies the source file to the target file. $targetFile will only be overwritten if $overwrite is TRUE,
    * otherwise a {@see \Beluga\IO\FileAlreadyExistsException} is thrown.
    *
    * @param  string $sourceFile                The source file
    * @param  string $targetFile                The target/destination file.
    * @param  bool   $overwrite                 Overwrite $targetFile if it exists? (default=TRUE)
    * @throws \Beluga\IO\FileNotFoundError      If the $srcFile does not exist.
    * @throws \Beluga\IO\FileAlreadyExistsError If $targetFile exists and overwriting is disabled.
    * @throws \Beluga\IO\IOError
    */
   public static function Copy( string $sourceFile, string $targetFile, bool $overwrite = true )
   {

      if ( ! \file_exists( $sourceFile ) )
      {
         throw new FileNotFoundError( 'IO', $sourceFile, 'Could not copy a dont existing file.' );
      }

      if ( \file_exists( $targetFile ) )
      {
         if ( ! $overwrite )
         {
            throw new FileAlreadyExistsError(
               'IO',
               $targetFile,
               'Could not copy a file to defined target-file if overwriting is not allowed by current call of File::Copy()'
            );
         }
         static::Delete( $targetFile );
      }

      $output = null;

      try
      {
         $res = \copy( $sourceFile, $targetFile );
         if ( FALSE === $res )
         {
            throw new \Exception();
         }
      }
      catch ( \Throwable $ex )
      {
         $ex = null;
         try
         {
            $return_var = 1;
            if ( static::IS_WIN )
            {
               \ob_start();
               \exec(
                  "copy /Y /B \"{$sourceFile}\" \"{$targetFile}\" 2>&1",
                  $output,
                  $return_var
               );
               \ob_end_clean();
            }
            else
            {
               \ob_start();
               \exec(
                  "cp {$sourceFile} {$targetFile} 2>&1",
                  $output,
                  $return_var
               );
               \ob_end_clean();
            }
            if ( 0 !== $return_var )
            {
               throw new IOError( 'IO', $sourceFile, $output[ 0 ] );
            }
         }
         catch ( \Throwable $ex1 )
         {
            throw new IOError(
               'IO',
               $sourceFile,
               \sprintf( 'Copying file to "%s" fails.', $targetFile ),
               E_USER_ERROR,
               $ex1
            );
         }
      }

   }

   /**
    * Read the first $count bytes.
    *
    * @param  string  $file
    * @param  integer $count
    * @return string
    */
   public static function ReadFirstBytes( string $file, int $count ) : string
   {

      try
      {
         $f = static::OpenRead( $file );
         $res = $f->read( $count );
         $f->close();
         return $res;
      }
      catch ( \Exception $ex ) { $ex = null; }

      return '';

   }

   # </editor-fold>

   # <editor-fold desc="Zip + ZipList  + UnZip">

   /**
    * Compresses the defined source file to defined ZIP archive file.
    *
    * @param  string $sourceFile This file will be compressed by the zip archive
    * @param  string $zipFile    The target/destination ZIP file. (will be created or overwrite a existing)
    * @param  string $workingDir A optional working folder. (Usual its the folder, containing the $sourceFile)
    * @throws \Beluga\MissingExtensionError     If the require \ZipArchive class (ZIP extension) not exists
    * @throws \Beluga\IO\FileAccessError
    * @throws \Throwable
    */
   public static function Zip( string $sourceFile, string $zipFile, string $workingDir = null )
   {

      if ( ! \class_exists( '\\ZipArchive' ) )
      {
         throw new MissingExtensionError( 'ZIP', 'IO', 'Can not ZIP the file "' . $sourceFile . '"!' );
      }

      // Remember the original used working directory
      $owd = Path::Unixize( \getcwd() );

      // Prepare the actual working directory
      if ( empty( $workingDir ) )
      {
         $workingDir = Path::Unixize( \dirname( $sourceFile ) );
      }
      else
      {
         $workingDir = Path::Unixize( $workingDir );
      }

      if ( $workingDir == $owd )
      {
         // Current working dir and original working dir are the same.
         $owd = null;
      }
      else
      {
         // Set the new working directory
         \chdir( $workingDir );
      }

      // Remove the working directory from sourceFile (if it is contained)
      $sourceFile = \preg_replace(
         '~^' . \preg_quote( $workingDir, '~' ) . '/~',
         '',
         Path::Unixize( $sourceFile )
      );

      $oldFile = null;
      if ( \file_exists( $zipFile ) )
      {
         // The target ZIP file exists. Rename it to *.zip.old
         $oldFile = $zipFile . '.old';
         try { static::Move( $zipFile, $oldFile ); }
         catch ( \Throwable $ex )
         {
            // Renaming fails
            if ( ! \is_null( $owd ) )
            {
               // Restore the original working directory
               \chdir( $owd );
            }
            throw $ex;
         }
      }

      // Init the ZipArchive
      $zip = new \ZipArchive();

      if ( true === ( $res = $zip->open( $zipFile, \ZipArchive::CREATE ) ) )
      {
         // Successful opened the ZIP file writer

         // Add the source file to ZIP file writer
         $zip->addFile( $sourceFile );

         // Setting the optional ZIP archive comment
         $zip->setArchiveComment( 'Archived Single-File' );

         // Close the ZIP file
         if ( ! $zip->close() )
         {
            // ZIP file closing fails
            if ( ! empty( $oldFile ) )
            {
               // Restore the old zip file if defined
               static::Move( $oldFile, $zipFile );
            }
            if ( ! \is_null( $owd ) )
            {
               // Restore the original working directory
               \chdir( $owd );
            }
            throw new FileAccessError(
               'IO',
               $zipFile,
               FileAccessError::ACCESS_CREATE,
               'ZIP file could not be created cause write could not be completed! (Closing file fails)'
            );
         }

         if ( ! empty( $oldFile ) )
         {
            // Delete the old zip file if defined
            static::Delete( $oldFile );
         }

         if ( ! \is_null( $owd ) )
         {
            // Restore the original working directory
            \chdir( $owd );
         }

      }
      else
      {
         // Failed to open the ZIP file writer

         if ( ! \is_null( $owd ) )
         {
            // Restore the original working directory
            \chdir( $owd );
         }

         if ( ! empty( $oldFile ) )
         {
            // Restore the old zip file if defined
            static::Move( $oldFile, $zipFile );
         }

         throw new FileAccessError(
            'IO',
            $zipFile,
            FileAccessError::ACCESS_CREATE,
            'ZIP file could not be created cause ' . static::GetZipArchiveError( $res )
         );

      }

   }

   /**
    * Compresses multiple files in a ZIP archive file.
    *
    * If $sourceFiles is a numerically indicated array the origin file name is used inside the file.
    * If $sourceFiles is a associative array, the keys representing the file names, used inside the
    * archive, and the values are the real paths to the existing files.
    *
    * @param  array  $sourceFiles   The files to compress
    * @param  string $zipFile       The target/destination ZIP file. (will be created or overwrite a existing)
    * @param  string $zipFolderName Optional folder name, used inside the ZIP file, if one is required.
    * @throws \Beluga\MissingExtensionError     If the require \ZipArchive class (ZIP extension) not exists
    * @throws \Beluga\IO\FileAccessError If creating the ZIP file fails.
    */
   public static function ZipList( array $sourceFiles, string $zipFile, string $zipFolderName = null )
   {

      if ( ! \class_exists( '\\ZipArchive' ) )
      {
         throw new MissingExtensionError( 'ZIP', 'IO', 'Can not ZIP the file list!' );
      }

      $oldFile = null;
      if ( \file_exists( $zipFile ) )
      {
         $oldFile = $zipFile . '.old';
         static::Move( $zipFile, $oldFile );
      }

      $zip = new \ZipArchive();
      if ( true === ( $res = $zip->open( $zipFile, \ZipArchive::CREATE ) ) )
      {
         // ZIP Archive writer is opened successful


         if ( ! empty( $zipFolderName ) )
         {
            // Add a sub folder if required
            $zip->addEmptyDir( $zipFolderName );
            // Normalize the ZIP file internal folder name for file system usage
            $zipFolderName = \rtrim( $zipFolderName, '\\/' ) . '/';
            foreach ( $sourceFiles as $k => $v )
            {
               // If the key is numeric, use the value file name, otherwise the key defines the file name
               $key = \is_numeric( $k ) ? \basename( $v ) : $k;
               $zip->addFile( $v, $zipFolderName . '/' . $key );
            }
         }
         else
         {
            foreach ( $sourceFiles as $k=>$v )
            {
               if ( \is_numeric( $k ) )
               {
                  $zip->addFile( $v, \basename( $v ) );
               }
               else
               {
                  $zip->addFile( $v, $k );
               }
            }
         }

         if ( ! $zip->close() )
         {

            if ( ! empty( $oldFile ) )
            {
               static::Move( $oldFile, $zipFile );
            }

            throw new FileAccessError(
               'IO',
               $zipFile,
               FileAccessError::ACCESS_CREATE,
               'Zipfile could not be created cause write could not be completed! (Closing file fails)'
            );

         }

      }
      else
      {

         if ( ! empty( $oldFile ) )
         {
            static::Move( $oldFile, $zipFile );
         }

         throw new FileAccessError(
            'IO',
            $zipFile,
            FileAccessError::ACCESS_CREATE,
            'Zipfile could not be created cause ' . static::GetZipArchiveError( $res ) );

      }

   }

   /**
    * Extracts all files from defined ZIP file to defined target/destination folder.
    *
    * @param  string $zipFile      The ZIP file path.
    * @param  string $targetFolder The target/destination folder where the extracted files should be located.
    * @param  bool   $clearTarget  Empty/clear the target/destination folder before?
    * @throws \Beluga\MissingExtensionError If the require \ZipArchive class (ZIP extension) not exists
    * @throws \Beluga\IO\FileNotFoundError  If the ZIP file dont exists.
    * @throws \Beluga\IO\FileAccessError    If reading the ZIP file fails.
    */
   public static function UnZip( string $zipFile, string $targetFolder, bool $clearTarget = true )
   {

      if ( ! \class_exists( '\\ZipArchive' ) )
      {
         throw new MissingExtensionError( 'ZIP', 'IO', 'Can not UNZIP the file "' . $zipFile . '"!' );
      }

      if ( ! \file_exists( $zipFile ) )
      {
         throw new FileNotFoundError(
            'IO',
            $zipFile,
            'Could not extract from defined archive file.'
         );
      }

      if ( $clearTarget )
      {
         Folder::MoveContents( $targetFolder, $targetFolder . '-tmp', 0770, true );
      }

      $zip = new \ZipArchive();
      if ( true === ( $res = $zip->open( $zipFile ) ) )
      {
         $zip->extractTo( $targetFolder );
         $zip->close();
      }
      else
      {

         if ( ! empty( $clearTarget ) )
         {
            Folder::Move( $targetFolder . '-tmp', $targetFolder, 0770, true );
         }
         throw new FileAccessError(
            'IO',
            $zipFile,
            FileAccessError::ACCESS_READ,
            'Could not read from zip file cause ' . static::GetZipArchiveError( $res )
         );

      }

   }

   /**
    * Extracts the file with the name $zippedFileName inside the archive file to defined target/destination file.
    *
    * If the target file exists, it will be overwritten.
    *
    * @param  string $zipFile              The ZIP file path.
    * @param  string $zippedFileName       The name of the file to extract, used inside the ZIP file.
    * @param  string $targetFile           The file path of the resulting extracted file.
    * @throws \Beluga\MissingExtensionError If the require \ZipArchive class (ZIP extension) not exists
    * @throws \Beluga\IO\FileNotFoundError If the ZIP file dont exists.
    * @throws \Beluga\IO\FileAccessError
    */
   public static function UnZipSingleFile( string $zipFile, string $zippedFileName, string $targetFile )
   {

      if ( ! \file_exists( $zipFile ) )
      {
         throw new FileNotFoundError (
            'IO',
            $zipFile,
            'Could not extract from defined archive file.'
         );
      }

      try
      {
         \file_put_contents(
            $targetFile,
            \file_get_contents( 'zip://' . $zipFile . '#' . $zippedFileName )
         );
      }
      catch ( \Throwable $ex )
      {
         throw FileAccessError::Read(
            'IO',
            $zipFile,
            'Could not extract from defined archive file.',
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * @access private
    * @ignore
    * @param  int $code
    * @return string
    */
   public static function GetZipArchiveError( int $code ) : string
   {
      switch ( $code )
      {
         case \ZipArchive::ER_COMPNOTSUPP:
             return 'Zip-Component is not supported.';
         case \ZipArchive::ER_CRC:
             return 'a CRC-Error is thrown.';
         case \ZipArchive::ER_INCONS:
             return 'it results in a inconsistent zip-archive.';
         case \ZipArchive::ER_INTERNAL:
             return 'a internal error (unknown error) is thrown.';
         case \ZipArchive::ER_MEMORY:
             return 'zip-creation needs memory more than usable memory size.';
         case \ZipArchive::ER_OPEN:
             return 'open the archive results in a error.';
         case \ZipArchive::ER_WRITE:
             return 'writing into archive results in a error.';
         case \ZipArchive::ER_ZLIB:
             return 'a ZLib error (unknown message) is thrown.';
         default:
             return 'a unknown error is thrown.';
      }
   }

   # </editor-fold>

   # <editor-fold desc="GetExtension + GetExtensionName + GetNameWithoutExtension + ChangeExtension">

   /**
    * Returns the file name extension, including the leading dot, for defined file name/path.
    *
    * @param  string  $file           The file name/path.
    * @param  boolean $doubleExtension  If you require extensions, including also a single dot like '.abc.def'
    *                                 you have to set this parameter to TRUE.
    * @return string|FALSE Returns the file name extension NOT including the leading dot or FALSE if not extension.
    */
   public static function GetExtension( string $file, bool $doubleExtension = false )
   {

      // In case the path is a URL, strip any query string before getting extension
      $qCharPosition = \strpos( $file, '?' );
      if ( false !== $qCharPosition )
      {
         $file = \substr( $file, 0, $qCharPosition );
      }

      if ( ! $doubleExtension )
      {
         return '.' . \ltrim( Path::GetPathinfo( $file, \PATHINFO_EXTENSION ), '.' );
      }

      $hits = null;
      $file = \basename( $file );

      if ( \preg_match( '~^.+(\.[a-z0-9]{1,6}\.[a-z0-9]{1,6})$~i', $file, $hits ) )
      {
         return $hits[ 1 ];
      }

      $tmp = \explode( '.', $file );
      $tsz = \count( $tmp );

      if ( $tsz < 2 )
      {
         return false;
      }

      return '.' . $tmp[ $tsz - 1 ];

   }

   /**
    * Returns the file name extension, NOT including the leading dot, for defined file name/path.
    *
    * @param  string  $file           The file name/path.
    * @param  boolean $doubleExtension  If you require extensions, including also a single dot like '.abc.def'
    *                                 you have to set this parameter to TRUE.
    * @return string|FALSE Returns the file name extension NOT including the leading dot or FALSE if not extension.
    */
   public static function GetExtensionName( string $file, bool $doubleExtension = false )
   {
      return \ltrim( static::GetExtension( $file, $doubleExtension ), '.' );
   }

   /**
    * Returns the file name without the file name extension.
    *
    * @param  string  $file           The file name/path.
    * @param  boolean $doubleExtension  If you require extensions, including also a single dot like '.abc.def'
    *                                 you have to set this parameter to TRUE.
    * @return string|bool Name or bool FALSE.
    */
   public static function GetNameWithoutExtension( string $file, bool $doubleExtension = false )
   {
      if ( \FALSE === ( $ext = static::GetExtension( $file, $doubleExtension ) ) )
      {
         return \basename( $file );
      }
      return \substr( \basename( $file ), 0, -\strlen( $ext ) );
   }

   /**
    * Changes the file name extension of a defined file name/path. If the file exists in current file system
    * and $handle is TRUE, it will be renamed also for real.
    *
    * @param  string  $file           The file name/path.
    * @param  string  $newExtension   The new file name extension (with or without the leading dot)
    * @param  boolean $doubleExtension  If you require extensions, including also a single dot like '.abc.def'
    *                                 you have to set this parameter to TRUE.
    * @param  boolean $handle         Also real rename the file from file system if it exists? !!!Handle with care!!!
    * @return string
    * @throws \Beluga\IO\IOError
    */
   public static function ChangeExtension(
      string $file, string $newExtension, bool $doubleExtension = false, bool $handle = false ) : string
   {
      $base = \basename( $file );
      $folder = \substr( $file, 0, -\strlen( $base ) );
      $newExtension = '.' . \ltrim( $newExtension, '.' );
      $result =
            $folder
         .  static::GetNameWithoutExtension( $file, $doubleExtension )
         .  $newExtension;
      if ( $handle && \file_exists( $file ) )
      {
         static::Move( $file, $result, true );
      }
      return $result;
   }

   # </editor-fold>

   # </editor-fold>


}

