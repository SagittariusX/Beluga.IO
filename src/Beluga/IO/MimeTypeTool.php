<?php
/**
 * In this file the class '\Beluga\IO\MimeTypeTool' is defined.
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
 * This is a static helper class to handle MIME types.
 */
abstract class MimeTypeTool
{


   # <editor-fold desc="= = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   private static $mimeTypes = array(
      'afl' => 'video/animaflex',
      'ai' => 'application/postscript',
      'aif' => 'audio/x-aiff',
      'aifc' => 'audio/x-aiff',
      'aiff' => 'audio/x-aiff',
      'aim' => 'application/x-aim',
      'arc' => 'application/octet-stream',
      'arj' => 'application/octet-stream',
      'asf' => 'video/x-ms-asf',
      'asm' => 'text/x-asm',
      'asp' => 'text/asp',
      'au' => 'audio/basic',
      'avi' => 'video/avi',
      'avs' => 'video/avs-video',
      'bin' => 'application/octet-stream',
      'bmp' => 'image/bmp',
      'bsh' => 'application/x-bsh',
      'bz' => 'application/x-bzip',
      'bz2' => 'application/x-bzip2',
      'c' => 'text/x-c',
      'c++' => 'text/plain',
      'cat' => 'application/vnd.ms-pki.seccat',
      'cc' => 'text/plain',
      'cer' => 'application/x-x509-ca-cert',
      'class' => 'application/java',
      'conf' => 'text/plain',
      'cpio' => 'application/x-cpio',
      'cpp' => 'text/x-c',
      'cpt' => 'application/mac-compactpro',
      'csh' => 'application/x-csh',
      'css' => 'text/css',
      'def' => 'text/plain',
      'der' => 'application/x-x509-ca-cert',
      'dll' => 'application/octet-stream',
      'dms' => 'application/octet-stream',
      'doc' => 'application/msword',
      'dot' => 'application/msword',
      'dump' => 'application/octet-stream',
      'dvi' => 'application/x-dvi',
      'dwg' => 'application/acad',
      'el' => 'text/x-script.elisp',
      'eps' => 'application/postscript',
      'etx' => 'text/x-setext',
      'exe' => 'application/octet-stream',
      'fdf' => 'application/vnd.fdf',
      'fli' => 'video/x-fli',
      'fpx' => 'image/vnd.fpx',
      'gif' => 'image/gif',
      'gtar' => 'application/x-gtar',
      'gz' => 'application/x-gzip',
      'gzip' => 'application/x-gzip',
      'hdf' => 'application/x-hdf',
      'help' => 'application/x-helpfile',
      'hlp' => 'application/x-winhelp',
      'hqx' => 'application/mac-binhex',
      'hta' => 'application/hta',
      'htm' => 'text/html',
      'html' => 'text/html',
      'htmls' => 'text/html',
      'htx' => 'text/html',
      'ico' => 'image/x-icon',
      'imap' => 'application/x-httpd-imap',
      'inf' => 'application/inf',
      'java' => 'text/x-java-source',
      'jfif' => 'image/jpeg',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg' => 'image/jpeg',
      'js' => 'application/x-javascript',
      'kar' => 'music/x-karaoke',
      'ksh' => 'application/x-ksh',
      'latex' => 'application/x-latex',
      'lha' => 'application/octet-stream',
      'list' => 'text/plain',
      'log' => 'text/plain',
      'lsp' => 'application/x-lisp',
      'lst' => 'text/plain',
      'ltx' => 'application/x-latex',
      'lzh' => 'application/octet-stream',
      'm1v' => 'video/mpeg',
      'm2a' => 'audio/mpeg',
      'm2v' => 'video/mpeg',
      'm3u' => 'audio/x-mpequrl',
      'man' => 'application/x-troff-man',
      'mcd' => 'application/mcad',
      'mht' => 'message/rfc822',
      'mhtml' => 'message/rfc822',
      'mid' => 'application/x-midi',
      'midi' => 'application/x-midi',
      'mime' => 'message/rfc822',
      'mm' => 'application/base64',
      'mod' => 'audio/x-mod',
      'moov' => 'video/quicktime',
      'mov' => 'video/quicktime',
      'mp2' => 'audio/x-mpeg',
      'mp3' => 'audio/mpeg3',
      'mpe' => 'video/mpeg',
      'mpeg' => 'video/mpeg',
      'mpg' => 'video/mpeg',
      'mpga' => 'audio/mpeg',
      'mpp' => 'application/vnd.ms-project',
      'mv' => 'video/x-sgi-movie',
      'pas' => 'text/pascal',
      'pbm' => 'image/x-portable-bitmap',
      'pct' => 'image/x-pict',
      'pcx' => 'image/x-pcx',
      'pdf' => 'application/pdf',
      'pgm' => 'image/x-portable-graymap',
      'php' => 'text/plain',
      'pic' => 'image/pict',
      'pict' => 'image/pict',
      'pl' => 'text/plain',
      'png' => 'image/png',
      'pot' => 'application/vnd.ms-powerpoint',
      'ppa' => 'application/vnd.ms-powerpoint',
      'pps' => 'application/vnd.ms-powerpoint',
      'ppt' => 'application/vnd.ms-powerpoint',
      'ps' => 'application/postscript',
      'psd' => 'application/octet-stream',
      'py' => 'text/x-script.phyton',
      'qt' => 'video/quicktime',
      'ra' => 'audio/x-realaudio',
      'ram' => 'audio/x-pn-realaudio',
      'rm' => 'application/vnd.rn-realmedia',
      'rt' => 'text/richtext',
      'rtf' => 'application/x-rtf',
      'rtx' => 'text/richtext',
      'rv' => 'video/vnd.rn-realvideo',
      'sea' => 'application/octet-stream',
      'sgm' => 'text/x-sgml',
      'sgml' => 'text/x-sgml',
      'sh' => 'application/x-sh',
      'shtml' => 'text/html',
      'snd' => 'audio/basic',
      'so' => 'application/octet-stream',
      'swf' => 'application/x-shockwave-flash',
      'tar' => 'application/x-tar',
      'tcl' => 'application/x-tcl',
      'tcsh' => 'text/x-script.tcsh',
      'tex' => 'application/x-tex',
      'texi' => 'application/x-texinfo',
      'texinfo' => 'application/x-texinfo',
      'text' => 'text/plain',
      'tgz' => 'application/x-compressed',
      'tif' => 'image/tiff',
      'tiff' => 'image/tiff',
      'txt' => 'text/plain',
      'vcs' => 'text/x-vcalendar',
      'voc' => 'audio/voc',
      'vrml' => 'application/x-vrml',
      'wav' => 'audio/x-wav',
      'wbmp' => 'image/vnd.wap.wbmp',
      'wml' => 'text/vnd.wap.wml',
      'word' => 'application/msword',
      'xbm' => 'image/x-xbitmap',
      'xht' => 'application/xhtml+xml',
      'xhtml' => 'application/xhtml+xml',
      'xla' => 'application/x-msexcel',
      'xlb' => 'application/vnd.ms-excel',
      'xlc' => 'application/vnd.ms-excel',
      'xls' => 'application/vnd.ms-excel',
      'xm' => 'audio/xm',
      'xml' => 'text/xml',
      'xpm' => 'image/xpm',
      'x-png' => 'image/png',
      'zip' => 'application/x-zip-compressed',
      'zsh' => 'text/x-script.zsh'
   );

   # </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the MIME type, associated with the defined file name extension.
    *
    * @param  string $extension The file name extension (with or without the leading dot)
    * @return string            Returns the MIME type
    */
   public static function GetByExtension( string $extension ) : string
   {

      $normalizedExtension = \strtolower( \ltrim( $extension, '.' ) );

      if ( isset( static::$mimeTypes[ $normalizedExtension ] ) )
      {
         return static::$mimeTypes[ $normalizedExtension ];
      }

      return 'application/octet-stream';

   }

   /**
    * Returns the MIME type, associated with the file name extension of the defined file name/path
    *
    * @param  string $fileName The file name/path
    * @return string Returns the MIME type.
    */
   public static function GetByFileName( string $fileName ) : string
   {

      return static::GetByExtension( File::GetExtension( $fileName ) );

   }

   /**
    * Returns if the defined MIME type represents a image type.
    *
    * @param  string $mimeType The MIME type to check.
    * @return boolean
    */
   public static function IsImageType( string $mimeType ) : bool
   {

      return 0 === \strpos( $mimeType, 'image/' );

   }

   /**
    * Returns if the defined file name extension points to a image file type.
    *
    * @param  string $extension The file name extension (with or without the leading dot.
    * @return boolean
    */
   public static function IsImageTypeExtension( string $extension ) : bool
   {

      return 0 === \strpos( static::GetByExtension( $extension ), 'image/' );

   }

   /**
    * Returns if the defined file name/path points to a image file type.
    *
    * @param  string $fileName File (name or path)
    * @return boolean
    */
   public static function IsImageTypeFileName( string $fileName ) : bool
   {

      return 0 === \strpos( static::GetByFileName( $fileName ), 'image/' );

   }

   /**
    * Returns if the defined file uses a valid GIF image file header.
    *
    * @param  string $file
    * @param  string $firstBytes If defined it is used to get image info from.
    * @return boolean
    */
   public static function HasGifHeader( string $file, string $firstBytes = null ) : bool
   {

      if ( empty( $firstBytes ) )
      {
         $firstBytes = File::ReadFirstBytes( $file, 6 );
      }

      return (bool) \preg_match( '~^GIF\d~', $firstBytes );

   }

   /**
    * Returns if the defined file uses a valid JPEG image file header.
    *
    * @param  string $file
    * @param  string $firstBytes If defined it is used to get image info from.
    * @return boolean
    */
   public static function HasJpegHeader( string $file, string $firstBytes = null ) : bool
   {

      if ( empty( $firstBytes ) )
      {
         $firstBytes = File::ReadFirstBytes( $file, 4 );
      }

      return (
            ( 'ff' == \dechex( \ord( $firstBytes [ 0 ] ) ) )
         && ( 'd8' == \dechex( \ord( $firstBytes [ 1 ] ) ) )
         && ( 'ff' == \dechex( \ord( $firstBytes [ 2 ] ) ) )
      );

   }

   /**
    * Returns if the defined file uses a valid PNG image file header.
    *
    * @param  string $file
    * @param  string $firstBytes If defined it is used to get image info from.
    * @return boolean
    */
   public static function HasPngHeader( string $file, string $firstBytes = null ) : bool
   {

      if ( empty( $firstBytes ) )
      {
         $firstBytes = File::ReadFirstBytes( $file, 6 );
      }

      return (
            ( '89' == \dechex( \ord( $firstBytes[ 0 ] ) ) )
         && ( '50' == \dechex( \ord( $firstBytes[ 1 ] ) ) )
         && ( '4e' == \dechex( \ord( $firstBytes[ 2 ] ) ) )
         && ( '47' == \dechex( \ord( $firstBytes[ 3 ] ) ) )
         && ( '0d' == \dechex( \ord( $firstBytes[ 4 ] ) ) )
      );

   }

   /**
    * Returns the web image MIME type, depending to file name extension and used image file header.
    *
    * If its not a web image FALSE is returned. Valid web image ar images of type GIF, PNG and JPEG.
    *
    * @param  string $file
    * @return string|FALSE
    */
   public static function GetWebImageMimeTypeByHeaderCode( string $file )
   {
      $f8b = File::ReadFirstBytes( $file, 8 );
      $ext = \strtolower( File::GetExtensionName( $file ) );
      switch ( $ext )
      {
         case 'gif':
            if ( static::HasGifHeader( $file, $f8b ) )
            {
               return 'image/gif';
            }
            return false;
         case 'jpg':
         case 'jpeg':
            if ( static::HasJpegHeader( $file, $f8b ) )
            {
               return 'image/jpeg';
            }
            return false;
         case 'png':
            if ( static::HasPngHeader( $file, $f8b ) )
            {
               return 'image/png';
            }
            return false;
         default:
            return false;
      }
   }

   # </editor-fold>


}

