<?php
/**
 * In this file the class '\Beluga\IO\FileAccessError' is defined.
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


/**
 * This exception should be used if accessing a file for reading and/or writing fails.
 *
 * The class extends from {@see \Beluga\IO\IOError}.
 *
 * @since v0.1
 */
class FileAccessError extends IOError
{


   # <editor-fold desc="= = =   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

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

   /**
    * Creating file access.
    */
   const ACCESS_CREATE = 'create';

   /**
    * Deleting file access.
    */
   const ACCESS_DELETE = 'delete';

   # </editor-fold>


   # <editor-fold desc="= = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   private $access;

   # </editor-fold>


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param string     $package
    * @param string     $file     The file where accessing fails
    * @param string     $access   The access type (see \Beluga\IO\FileAccessException::ACCESS_* class constants)
    * @param string     $message  The optional error message
    * @param integer    $code     A optional error code (Defaults to \E_USER_ERROR)
    * @param \Throwable $previous A Optional previous exception.
    */
   public function __construct(
      string $package, string $file, string $access = self::ACCESS_READ, string $message = null, $code = 256,
      \Throwable $previous = null )
   {

      parent::__construct(
         $package,
         $file,
         \sprintf( 'Could not %s file.', $access ) . static::appendMessage( $message ),
         $code,
         $previous
      );

      $this->access = $access;

   }

   # </editor-fold>


   # <editor-fold desc="= = =   G E T T E R S   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the error file access type (see \Beluga\IO\FileAccessException::ACCESS_* class costants)
    *
    * @return string
    */
   public final function getAccessType() : string
   {
      return $this->access;
   }

   # </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Init's a new \Beluga\IO\FileAccessError for file read mode.
    *
    * @param  string     $file     The file where reading fails.
    * @param  string     $message  The optional error message.
    * @param  integer    $code     A optional error code (Defaults to \E_USER_ERROR)
    * @param  \Throwable $previous A Optional previous exception.
    * @return \Beluga\IO\FileAccessError
    */
   public static function Read(
      string $package, string $file, string $message = null, int $code = \E_USER_ERROR, \Throwable $previous = null )
   : FileAccessError
   {
      return new FileAccessError(
         $package,
         $file,
         static::ACCESS_READ,
         $message,
         $code,
         $previous
      );
   }

   /**
    * Init's a new \Beluga\IO\FileAccessError for file write mode.
    *
    * @param  string     $file     The file where reading fails.
    * @param  string     $message  The optional error message.
    * @param  integer    $code     A optional error code (Defaults to \E_USER_ERROR)
    * @param  \Throwable $previous A Optional previous exception.
    * @return \Beluga\IO\FileAccessError
    */
   public static function Write(
      string $package, string $file, string $message = null, int $code = \E_USER_ERROR, \Throwable $previous = null )
   : FileAccessError
   {

      return new FileAccessError(
         $package,
         $file,
         static::ACCESS_WRITE,
         $message,
         $code,
         $previous
      );

   }

   # </editor-fold>


}

