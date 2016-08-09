<?php
/**
 * In this file the class '\Beluga\IO\Folder' is defined.
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
 * A static folder (directory) helping class.
 *
 * @since      v0.1
 */
abstract class Folder
{


   # <editor-fold desc="= = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Goes the count of $upLevel up inside the defined folder. Each level up goes 1 level up to the parent folder.
    *
    * e.g.:
    *
    * <code>\Beluga\IO\Folder::Up( '/is/var/www', 2, '' );   // results in: '/is'</code>
    *
    * @param  string $folder  The base folder
    * @param  int    $upLevel How many folder levels we should go up? (Defaults to 1)
    * @param  string $endChar Append this character to the end of the resulting folder path. (Defaults to '')
    * @return string
    */
   public static function Up( string $folder, int $upLevel = 1, $endChar = '' ) : string
   {
      
       for ( ; $upLevel > 0; --$upLevel )
       {
          $folder = \dirname( $folder );
       }
      
       return $folder . $endChar;
      
   }

   /**
    * Checks if the defined folder exists. If so, $folder is returned normalized. If not so,
    * the first existing parent folder is returned normalized.
    *
    * @param  string $folder
    * @return string
    */
   public static function GetFirstExisting( string $folder ) : string
   {

      // Remove directory separator from $folder end
      $folder = \rtrim( $folder, '\\/' );

      if ( false !== \strpos( $folder, '/' ) && false !== \strpos( $folder, '\\' ) )
      {
         // Use mixed directory separators, normalize it
         if ( \DIRECTORY_SEPARATOR == '\\' )
         {
            $folder = \str_replace( '/', '\\', $folder );
         }
         else
         {
            $folder = \str_replace( '\\', '/', $folder );
         }
      }

      // Get the current sub folder level
      $level =
         \count(
            \explode(
               \DIRECTORY_SEPARATOR,
               \ltrim( $folder, '\\/' )
            )
         );

      for ( $i = 0; $i < $level && ! \is_dir( $folder ); ++$i )
      {
          $f = \dirname( $folder );
          if ( empty( $f ) || $f == $folder )
          {
             return '/';
          }
          $folder = $f;
      }

      \clearstatcache();

      return $folder;

   }

   /**
    * Checks, if the folder can be created. !Attention! If the folder already exists FALSE is returned!
    *
    * @param  string $folderPath The folder to check.
    * @return boolean
    */
   public static function CanCreate( string $folderPath ) : bool
   {

      if ( \is_dir( $folderPath ) )
      {
         return false;
      }

      $fip = static::GetFirstExisting( $folderPath );

      return \is_writable( $fip );

   }

   /**
    * Creates a new folder with defined mode. The mode only works on unixoid systems.
    *
    * @param  string  $folder The folder to create.
    * @param  integer $mode Mode for new folder (0700) only used for unixoide systems
    * @throws \Beluga\IO\FolderAccessError If creation fails
    */
   public static function Create( string $folder, int $mode = 0700 )
   {

      if ( \is_dir( $folder ) )
      {
         // No folder = do nothing
         return;
      }

      if ( ! static::CanCreate( $folder ) )
      {
         // No rights to create the required folder
         throw new FolderAccessError(
            'IO',
            $folder,
            FolderAccessError::ACCESS_CREATE,
            'Creation of folder fails cause base folder is not writable!'
         );
      }

      try
      {
         if ( \DIRECTORY_SEPARATOR == '\\' )
         {
            // Windows = do not use the mode
            $res = \mkdir( $folder, null, true );
         }
         else
         {
            // All other use the mode flag
            $res = \mkdir( $folder, $mode, true );
         }
      }
      catch ( \Throwable $ex )
      {
         // Folder creation fails
         throw new FolderAccessError(
            'IO',
            $folder,
            FolderAccessError::ACCESS_CREATE,
            'Creation of folder fails cause base folder is not writable!',
            \E_USER_ERROR,
            $ex
         );
      }

      if ( ! $res || ! \is_dir( $folder ) )
      {
         // Folder creation fails
         throw new FolderAccessError(
            'IO',
            $folder,
            FolderAccessError::ACCESS_CREATE,
            'Unknown error while executing folder creation.'
         );
      }

      \clearstatcache();

   }

   /**
    * Deletes the defined folder recursive with all contained files and sub folders.
    *
    * @param  string  $folder The folder to delete.
    * @param  boolean $clear  Clear only all folder contents and don't delete the main folder? (Defaults to FALSE)
    * @throws \Beluga\IO\IOError
    */
   public static function Delete( string $folder, bool $clear = false )
   {

      if ( empty( $folder ) && ! \is_dir( $folder ) )
      {
         // No folder = we are done here
         return;
      }

      // Ensure the folder ends with an directory separator.
      $folder = \rtrim( $folder, '\\/' ) . \DIRECTORY_SEPARATOR;

      // Start getting all elements of the defined folder

      $openDir = \opendir( $folder );
      // ignore . and .. (its always the first and second
      \readdir( $openDir ); \readdir( $openDir );
      while ( false !== ( $item = \readdir( $openDir ) ) )
      {
         // The absolute item path
         $path = $folder . $item;
         if ( !\is_dir( $path ) )
         {
            // $path is a file
            File::Delete( $path );
         }
         else
         {
            // $path is a folder. Delete it recursively
            static::Delete( $path );
         }
      }
      \closedir( $openDir );

      if ( ! $clear )
      {
         // $folder should not be deleted, only emptied. We are done here
         return;
      }

      // Finally delete $folder
      try { \rmdir( $folder ); }
      catch ( \Throwable $ex )
      {
         throw new IOError(
            'IO',
            $folder,
            'Could not delete the defined folder.',
            \E_USER_ERROR,
            $ex
         );
      }

   }

   /**
    * Returns all file paths inside the defined folder.
     *
     * @param  string  $folder
     * @param  boolean $recursive Also include sub folders? (Defaults to FALSE)
     * @return array
     */
   public static function ListAllFiles( string $folder, bool $recursive = false ) : array
   {

      // Init the array that should contain the resulting file paths
      $files = [];

      if ( ! \is_dir( $folder ) )
      {
         // No folder = we are done here
         return $files;
      }

      if ( ! $recursive )
      {
         // List only files directly contained inside $folder (none from sub folders)

         // Open the directory pointer
         $d = \dir( $folder );
         // Ignore . and ..
         $d->read(); $d->read();
         // loop the rest
         while ( false !== ( $entry = $d->read() ) )
         {
            $tmp = Path::Combine( $folder, $entry );
            if ( ! \is_file( $tmp ) )
            {
               // Ignore sub folders
               continue;
            }
            $files[] = $tmp;
         }
         $d->close();
         return $files;
      }

      // List all files, also from sub folders
      static::_listRecursive( $files, $folder );

      return $files;

   }

   /**
    * Returns all files, matching the defined filter.
    *
    * If $recursive is TRUE, it also includes al sub folders.
    *
    * If the filter is a callback function/method, so it must accept 2 parameters.
    *
    * - string $itemName The name of the current filter item
    * - string $itemPath THe absolute path of the item.
    *
    * @param  string          $folder    The folder
    * @param  string|callback $filter    Regex or callback|callable for filtering files
    * @param  boolean         $recursive Include sub folders? (Defaults to FALSE)
    * @return array
    */
   public static function ListFilteredFiles( string $folder, $filter, bool $recursive = false ) : array
   {

      // Init the array that should contain the resulting file paths
      $files = array();

      if ( ! \is_dir( $folder ) )
      {
         // No folder = we are done here
         return $files;
      }

      if ( ! $recursive )
      {
         // List only files directly contained inside $folder (none from sub folders)

         // Open the directory pointer
         $d = \dir( $folder );
         while ( false !== ( $entry = $d->read() ) )
         {
            if ( $entry == '.' || $entry == '..' )
            {
               // Ignore . and ..
               continue;
            }
            $tmp = Path::Combine( $folder, $entry );
            if ( ! \is_file( $tmp ) )
            {
               continue;
            }
            if ( \is_callable( $filter ) )
            {
               if ( \call_user_func( $filter, $entry, $tmp ) )
               {
                  $files[] = $tmp;
               }
               continue;
            }
            try
            {
               if ( ! \preg_match( $filter, $entry ) )
               {
                  continue;
               }
               $files[] = $tmp;
            }
            catch ( \Exception $ex ) { $ex = null; }
         }
         $d->close();
         return $files;
      }

      static::_listRecursiveFiltered( $files, $filter, $folder );
      return $files;

   }

   /**
    * Returns the real path of the defined folder. If the folder is defined as a absolute path, its returned as it,
    * only normalized. Otherwise the $basePath is used to make the folder absolute.
    *
    * Its no checked, if the folder or path existsa!
    *
    * Examples:
    *
    * <code>
    * echo "'" . \Beluga\IO\Folder::GetRealPath('../xyz', '/abc/def') . "'";
    * # outputs '/abc/xyz'
    * echo "'" . \Beluga\IO\Folder::GetRealPath('./xyz', '/abc/def') . "'";
    * # outputs '/abc/def/xyz'
    * echo "'" . \Beluga\IO\Folder::GetRealPath('/xyz', '/abc/def') . "'";
    * # outputs '/xyz'
    * echo "'" . \Beluga\IO\Folder::GetRealPath('C:/xyz', 'C:/abc/def') . "'";
    * # outputs 'C:/xyz'
    * echo "'" . \Beluga\IO\Folder::GetRealPath('../../xyz', '/abc/def') . "'";
    * # outputs '/xyz'
    * </code>
    *
    * @param  string $folder   The folder
    * @param  string $basePath The base path used if the $folder is relative. If its empty, getcwd() is used.
    * @return string
    */
   public static function GetRealPath( string $folder, string $basePath ) : string
   {

      if ( \is_null( $basePath ) || \strlen( $basePath ) < 1 )
      {
         // If no base path is defined use path given by getcwd()
         $basePath = getcwd();
      }

      // Meassure the length of $folder
      $flen = \strlen( $folder );

      // Switch basepath to OS directory separator and remove trailing directory separators
      $basePath = \rtrim( Path::Normalize( $basePath ), \DIRECTORY_SEPARATOR );

      if ( $flen < 1 )
      {
         // return the base path if the folder is empty.
         return $basePath;
      }

      // Switch folder to OS directory separator
      $folder = Path::Normalize( $folder );

      if ( $folder[ 0 ] == '/' )
      {
         // $folder is a absolute unix path. return it
         return $folder;
      }

      if ( $flen < 2 )
      {
         if ( $folder == '.' )
         {
            // $folder is only a dot '.'
            return $basePath;
         }
         // $folder is a single character
         return $basePath . \DIRECTORY_SEPARATOR . \trim( $folder, \DIRECTORY_SEPARATOR );
      }

      if ( $flen == 2 )
      {
         if ( File::IS_WIN && $folder[ 1 ] == ':' )
         {
            // If its only like C: in windows systems, append a \
            return $folder . \DIRECTORY_SEPARATOR;
         }
         if ( $folder[ 0 ] == '.' && $folder[ 1 ] == '.' )
         {
            return \dirname( $basePath );
         }
         // Return normally combined
         return $basePath . \DIRECTORY_SEPARATOR . \trim( $folder, \DIRECTORY_SEPARATOR );
      }

      if ( ( $folder[ 1 ] == ':' && $folder[ 2 ] == '/' )
        || ( $folder[ 1 ] == '\\' && $folder[ 2 ] == '\\' ) )
      {
         // Absolute windows folders. Return it.
         return $folder;
      }

      // @todo: The next lines does not handle some /../ inside the folder

      // Remove all leading ../ or ..\
      while ( 0 === \strpos( $folder, '..' . \DIRECTORY_SEPARATOR ) )
      {
         $basePath = \dirname( $basePath );
         $folder   = \substr( $folder, 3 );
      }

      return $basePath . \DIRECTORY_SEPARATOR . \trim( $folder, '/' );

   }

   /**
    * Removes all contents from defined folder (empties it)
    *
    * @param  string $folder
    * @throws \Beluga\IO\IOError
    */
   public static function Clear( string $folder )
   {
      static::Delete( $folder, true );
   }

   /**
    * Moves all folder path elements (files + sub folders) from $sourceFolder to $targetFolder.
    *
    * The target folder will be created if it do'nt exists.
    *
    * The source folder it empty after this action.
    *
    * @param  string $sourceFolder The source folder.
    * @param  string $targetFolder The target folder.
    * @param  int    $tFolderMode  The mode of the target folder if it must be created. (Defaults to 0700)
    * @param  bool   $clearTarget  Clear the target folder if it has some contents (Defaults to FALSE)
    * @throws \Beluga\IO\IOError
    * @uses   \Beluga\IO\Folder::Copy() Uses internally the Copy method and clears after it the $sourceFolder
    */
   public static function MoveContents(
      string $sourceFolder, string $targetFolder, int $tFolderMode = 0700, bool $clearTarget = false )
   {
      static::Copy( $sourceFolder, $targetFolder, $tFolderMode, $clearTarget );
      static::Clear( $sourceFolder );
   }

   /**
    * Moves the $sourceFolder to the $targetFolder. $sourceFolder will be deleted.
    *
    * @param  string $sourceFolder The source folder.
    * @param  string $targetFolder The target folder.
    * @param  int    $tFolderMode  The mode of the target folder if it must be created. (Defaults to 0700)
    * @param  bool   $clearTarget  Clear the target folder if it has some contents (Defaults to FALSE)
    * @throws \Beluga\IO\IOError
    */
   public static function Move(
      string $sourceFolder, string $targetFolder, int $tFolderMode = 0700, bool $clearTarget = false )
   {
      static::Copy( $sourceFolder, $targetFolder, $tFolderMode, $clearTarget );
      static::Delete( $sourceFolder );
   }

   /**
    * Copies all folder path elements (files + sub folders) from $sourceFolder to $targetFolder.
    *
    * @param  string $sourceFolder The source folder.
    * @param  string $targetFolder The target folder.
    * @param  int    $tFolderMode  The mode of the target folder if it must be created. (Defaults to 0700)
    * @param  bool   $clearTarget  Clear the target folder if it has some contents (Defaults to FALSE)
    * @throws \Beluga\IO\FolderNotFoundError
    * @throws \Beluga\IO\IOError
    */
   public static function Copy(
      string $sourceFolder, string $targetFolder, int $tFolderMode = 0700, bool $clearTarget = false )
   {

      // Remove trailing directory separators
      $sourceFolder = \rtrim( $sourceFolder, '\\/' );
      $targetFolder = \rtrim( $targetFolder, '\\/' );

      if ( ! \is_dir( $sourceFolder ) )
      {
         // If the source folder not exists, stop here…
         throw new FolderNotFoundError(
            'IO',
            $sourceFolder,
            'Can not copy folder contents to another folder if defined source folder not exists!'
         );
      }

      if ( \is_dir( $targetFolder ) )
      {
         // Target exists
         if ( $clearTarget )
         {
            // Clear the target
            static::Clear( $targetFolder );
         }
      }
      else
      {
         // Target folder dont exists, create it
         static::Create( $targetFolder, $tFolderMode );
      }

      // Start reading the folder elements
      $openDir = \opendir( $sourceFolder );

      // Ignore '.' and '..' items
      \readdir( $openDir ); \readdir( $openDir );

      // loop all other items
      while ( false !== ( $item = \readdir( $openDir ) ) )
      {
         $sPath = $sourceFolder . '/' . $item;
         $tPath = $targetFolder . '/' . $item;
         if ( ! \is_dir( $sPath ) )
         {
            // Its a file, copy it
            File::Copy( $sPath, $tPath );
         }
         else
         {
            // Its a folder, copy it
            static::Copy( $sPath, $tPath, $tFolderMode );
         }
      }

      // End reading the folder elements
      \closedir( $openDir );

   }

   /**
    * Zips all folder contents to defined ZIP archive.
    *
    * @param  string  $sourceFolder The folder to zip
    * @param  string  $zipFile      The ZIP archive destination file
    * @param  string  $zFolderName  If you will zip it inside a special folder inside the archive, name the folder here.
    * @param  boolean $overwrite    Overwrite the archive if it exists? (Defaults to TRUE)
    * @throws \Beluga\MissingExtensionError     If the require \ZipArchive class (ZIP extension) not exists
    * @throws \Beluga\IO\FileAlreadyExistsError If the ZIP file exists, and overwriting is disabled.
    * @throws \Beluga\IO\IOError                In all other error cases, while ZIP writing.
    */
   public static function Zip(
      string $sourceFolder, string $zipFile, string $zFolderName = null, bool $overwrite = true )
   {

      if ( ! \class_exists( '\\ZipArchive' ) )
      {
         throw new MissingExtensionError( 'ZIP', 'IO', 'Can not ZIP the folder "' . $sourceFolder . '"!' );
      }
      
      $oldFile = null;
      $res     = null;
      
      if ( \file_exists( $zipFile ) )
      {
         if ( ! $overwrite )
         {
            throw new FileAlreadyExistsError(
               'IO',
               $zipFile,
               'Overwriting the ZIP-file is not allowed by code.'
            );
         }
         $oldFile = $zipFile . '.old';
         File::Move( $zipFile, $oldFile );
      }

      $zip = new \ZipArchive();

      if ( true !== ( $res = $zip->open( $zipFile, \ZipArchive::CREATE ) ) )
      {
          if ( ! empty( $oldFile ) )
          {
             File::Move( $oldFile, $zipFile );
          }
          static::___handWriteError( $res, $zipFile );
      }

      if ( empty( $zFolderName ) )
      {
          $tmp = \preg_split( '~[\\\\/]~', \dirname( $zipFile ) );
          $zFolderName = $tmp[ \count( $tmp ) - 1 ];
      }

      $zip->addEmptyDir( $zFolderName );

      $openDir = \opendir( $sourceFolder );
      \readdir( $openDir ); \readdir( $openDir );
      while ( false !== ( $item = \readdir( $openDir ) ) )
      {
          $itemPath = $sourceFolder . '/' . $item;
          static::___zipItem( $item, $itemPath, $zip, $zFolderName );
      }
      \closedir( $openDir );

      $zip->close();

   }

   # </editor-fold>


   # <editor-fold desc="= = =   P R I V A T E   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = =">

   /**
    * @param  $res
    * @param  $zipFile
    * @throws \Beluga\IO\IOError
    */
   private static function ___handWriteError( $res, $zipFile )
   {
      throw new IOError(
         'IO',
         $zipFile,
         'ZIP archive file could not be created cause '
            . File::GetZipArchiveError( $res ) );
   }

   /**
    * @param             $item
    * @param string      $itemPath
    * @param \ZipArchive $zip
    * @param string      $zFolderName
    */
   private static function ___zipItem( $item, $itemPath, \ZipArchive $zip, $zFolderName )
   {
      if ( \is_dir( $itemPath ) )
      {
         $zFolderName .= '/' . $item;
         $zip->addEmptyDir( $zFolderName );
         $openDir = \opendir( $itemPath );
         while ( false !== ( $itm = \readdir( $openDir ) ) )
         {
            if ( $itm == '.' || $itm == '..' )
            {
               continue;
            }
            $itmPath = $itemPath . '/' . $itm;
            static::___zipItem( $itm, $itmPath, $zip, $zFolderName );
         }
      }
      else
      {
         $zip->addFile( $itemPath, $zFolderName . '/' . $item );
      }
   }

   /**
    * @param $res
    * @param $folder
    */
   private static function _listRecursive( &$res, $folder )
   {
      $d = \dir( $folder );
      while ( false !== ( $entry = $d->read() ) )
      {
         if ( $entry === '..' || $entry === '.' )
         {
            continue;
         }
         $tmp = Path::Combine( $folder, $entry );
         if ( \is_dir( $tmp ) )
         {
            static::_listRecursive( $res, $tmp );
         }
         else
         {
            $res[] = $tmp;
         }
      }
      $d->close();
   }

   /**
    * @param $res
    * @param $filter
    * @param $folder
    */
   private static function _listRecursiveFiltered( &$res, $filter, $folder )
   {
      $d = \dir( $folder );
      $d->read(); $d->read();
      while ( false !== ( $entry = $d->read() ) )
      {
         $tmp = Path::Combine( $folder, $entry );
         if ( ! \is_file( $tmp ) )
         {
            static::_listRecursiveFiltered( $res, $filter, $tmp );
            continue;
         }
         if ( \is_callable( $filter ) )
         {
            if ( \call_user_func( $filter, $entry, $tmp ) )
            {
               $res[] = $tmp;
            }
            continue;
         }
         try
         {
            if ( ! \preg_match( $filter, $entry ) )
            {
               continue;
            }
            $res[] = $tmp;
         }
         catch ( \Exception $ex ) { $ex = null; }
      }
      $d->close();
   }

   # </editor-fold>


}

