<?php
/**
 * In this file the class '\Beluga\IO\FileAlreadyExistsError' is defined.
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
 * This class defines a exception, thrown if a file already exists but it should not exist.
 *
 * It extends from {@see \Beluga\IO\IOError}.
 *
 * @since v0.1
 */
class FileAlreadyExistsError extends IOError
{


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param string     $package
    * @param string     $file     The already existing file
    * @param string     $message  The optional error message
    * @param integer    $code     The optional error code (Default to \E_USER_ERROR)
    * @param \Throwable $previous A optional previous exception
    */
   public function __construct(
      string $package, string $file, string $message = null, $code = \E_USER_ERROR, \Throwable $previous = null )
   {
      parent::__construct(
         $package,
         $file,
         'The File does already exist.' . static::appendMessage( $message ),
         $code,
         $previous
      );
   }

   # </editor-fold>


}

