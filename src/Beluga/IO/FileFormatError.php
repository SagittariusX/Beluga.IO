<?php
/**
 * In this file the class '\Beluga\IO\FileFormatError' is defined.
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
 * This exception should be used if a accessed file contains a wrong/bad format.
 *
 * The class extends from {@see \Beluga\IO\Exception}.
 *
 * @since v0.1
 */
class FileFormatError extends IOError
{


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance
    *
    * @param string     $package
    * @param string     $file     The bad formatted file.
    * @param string     $message  The optional error message
    * @param int        $code     The optional error code
    * @param \Throwable $previous A optional previous exception
    */
   public function __construct(
      string $package, string $file, string $message = null, $code = 254, \Throwable $previous = null )
   {

      parent::__construct(
         $package,
         $file,
         'File format is wrong or illegal.' . static::appendMessage( $message ),
         $code,
         $previous
      );

   }

   # </editor-fold>


}

