# Beluga.IO

The SagittariusX Beluga IO lib.

```bash
composer require sagittariusx/beluga.io
```

or include it inside you're composer.json

```json
{
   "require": {
      "php": ">=7.0",
      "sagittariusx/beluga.io": "^0.1.0"
   }
}
```

The library declares the 3 main classes:

* `Beluga\IO\Path` Some static Path helping methods
* `Beluga\IO\File` FIle handling class
* `Beluga\IO\Folder` Some static Folder/Directory helping methods

and the helper class

* `Beluga\IO\MimeTypeTool`


and last but not least the following errors/exceptions:

* `Beluga\IO\IOError`
* `Beluga\IO\FileAccessError`
* `Beluga\IO\FileAllreadyExistsError`
* `Beluga\IO\FileFormatError`
* `Beluga\IO\FileNotFoundError`
* `Beluga\IO\FolderAccessError`
* `Beluga\IO\FolderNotFoundError`