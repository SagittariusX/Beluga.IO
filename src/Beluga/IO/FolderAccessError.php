<?php
/**
 * In this file the class '\Beluga\IO\FolderAccessError' is defined.
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
 * This exception should be used if accessing a folder for reading and/or writing fails.
 *
 * The class extends from {@see \Beluga\IO\IOException}.
 *
 * @since v0.1
 */
class FolderAccessError extends IOError
{


   # <editor-fold desc="= = =   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Reading folder access.
    */
   const ACCESS_READ = 'read';

   /**
    * Writing folder access.
    */
   const ACCESS_WRITE = 'write';

   /**
    * Reading and writing folder access.
    */
   const ACCESS_READWRITE = 'read and write';

   /**
    * Creating folder access.
    */
   const ACCESS_CREATE = 'create';

   /**
    * Deleting folder access.
    */
   const ACCESS_DELETE = 'delete';

   # </editor-fold>


   # <editor-fold desc="= = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   private $access;

   # </editor-fold>


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param string     $package
    * @param string     $folder   The folder where accessing fails.
    * @param string     $access   The access type (see \Beluga\IO\FolderAccessException::ACCESS_* class constants)
    * @param string     $message  The optional error message
    * @param integer    $code     A optional error code (Defaults to \E_USER_ERROR)
    * @param \Throwable $previous A Optional previous exception.
    */
   public function __construct(
      string $package, string $folder, string $access = self::ACCESS_READ, string $message = null,
      $code = 256, \Throwable $previous = null )
   {
      parent::__construct(
         $package,
         $folder,
         \sprintf( 'Could not %s folder.', $access ) . static::appendMessage( $message ),
         $code,
         $previous
      );
   }

   # </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the error folder access type (see \Beluga\IO\FolderAccessException::ACCESS_* class constants)
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
    * Init a new \Beluga\IO\FolderAccessError for folder read mode.
    *
    * @param string      $package
    * @param  string     $folder   The folder where reading fails.
    * @param  string     $message  The optional error message.
    * @param  integer    $code     A optional error code (Defaults to \E_USER_ERROR)
    * @param  \Throwable $previous A Optional previous exception.
    * @return \Beluga\IO\FolderAccessError
    */
   public static function Read(
      string $package, string $folder, string $message = null, int $code = \E_USER_ERROR, \Throwable $previous = null )
   : FolderAccessError
   {
      return new FolderAccessError(
         $package,
         $folder,
         static::ACCESS_READ,
         $message,
         $code,
         $previous
      );
   }

   /**
    * Init a new \Beluga\IO\FolderAccessError for folder write mode.
    *
    * @param string      $package
    * @param  string     $folder   The folder where reading fails.
    * @param  string     $message  The optional error message.
    * @param  integer    $code     A optional error code (Defaults to \E_USER_ERROR)
    * @param  \Throwable $previous A Optional previous exception.
    * @return \Beluga\IO\FolderAccessError
    */
   public static function Write(
      string $package, string $folder, string $message = null, int $code = \E_USER_ERROR, \Throwable $previous = null )
   : FolderAccessError
   {
      return new FolderAccessError(
         $package,
         $folder,
         static::ACCESS_WRITE,
         $message,
         $code,
         $previous
      );
   }

   # </editor-fold>


}

