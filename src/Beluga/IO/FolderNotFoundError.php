<?php
/**
 * In this file the class '\Beluga\IO\FolderNotFoundError' is defined.
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
 * This exception should be used if a required folder not exists.
 *
 * The class extends from {@see \Beluga\IO\IOError}.
 *
 * @since v0.1
 */
class FolderNotFoundError extends IOError
{


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init a new instance
    *
    * @param string     $package
    * @param string     $folder   The missed folder.
    * @param string     $message  The optional error message
    * @param int        $code     The optional error code (Default to \E_USER_ERROR)
    * @param \Throwable $previous A optional previous exception
    */
   public function __construct(
      string $package, string $folder, string $message = null, $code = 256, \Throwable $previous = null )
   {
      parent::__construct(
         $package,
         $folder,
         'The Folder not exists.' . static::appendMessage( $message ),
         $code,
         $previous
      );
   }

   # </editor-fold>


}

