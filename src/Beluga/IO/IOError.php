<?php
/**
 * In this file the class '\Beluga\IO\IOError' is defined.
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


use \Beluga\BelugaError;


/**
 * This class defines a exception, used as base exception of all IO exceptions.
 *
 * It extends from {@see \Beluga\BelugaError}.
 *
 * @since v0.1.0
 */
class IOError extends BelugaError
{


   # <editor-fold desc="= = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">"

   protected $path;

   # </editor-fold>


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param string         $package
    * @param string         $path     The path, associated with the error.
    * @param string         $message  A optional error message.
    * @param integer        $code     The optional error code
    * @param \Throwable     $previous A optional previous error/exception
    */
   public function __construct(
      string $package, string $path, string $message = null, $code = 256, \Throwable $previous = null )
   {

      parent::__construct(
         $package,
         \sprintf( 'There was a error with path [%s]!', $path ) . static::appendMessage( $message ),
         $code,
         $previous
      );

      $this->path = $path;

   }

   # </editor-fold>


   # <editor-fold desc="= = =   G E T T E R S   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the path that was error depending.
    *
    * @return string
    */
   public final function getPath() : string
   {
       return $this->path;
   }

   # </editor-fold>


}

