<?php
namespace Concrete\Package\Formidable\Src\Formidable;

defined('C5_EXECUTE') or die('Access Denied.');

use \League\Flysystem\AdapterInterface;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Text;

class Uploader {

    public static function upload($pointer, $filename = '', $dir = '') 
    {   
        if (empty($filename)) {
            $filename = basename($pointer);
        }
        
        $app = Application::getFacadeApplication();
            
        $sanitizedFilename = $app->make(Text::class)->sanitize($filename);
     
        $storageLocation = $app->make(ImportOptions::class)->getStorageLocation();
        $filesystem = $storageLocation->getFileSystemObject();

        $src = @fopen($pointer, 'rb');
        if ($src === false) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
        }
        try {
            $filesystem->writeStream(
                'tmp/'.$dir.$sanitizedFilename,
                $src,
                [
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                    //'mimetype' => $filesystem->getMimetype($src)
                ]
            );
            $writeError = null;
        } catch (\Exception $e) {
            $writeError = $e;
        } catch (\Throwable $e) {
            $writeError = $e;
        }
        @fclose($src);
        if ($writeError !== null) {
            return [
                'errors'=> true,
                'message' => $writeError->getMessage() //ImportException::fromErrorCode(ImportException::E_FILE_UNABLE_TO_STORE, $writeError)
            ];
        }
        
        return [
            'success' => true,
            'file' => $sanitizedFilename,
        ];
    }

    public static function delete($filename, $dir = '') 
    {   
        // validation needed?

        $app = Application::getFacadeApplication();
        
        $storageLocation = $app->make(ImportOptions::class)->getStorageLocation();
        $filesystem = $storageLocation->getFileSystemObject();

        try {
            $filesystem->delete('tmp/'.$dir.$filename);
            $writeError = null;
        } catch (\Exception $e) {
            $writeError = $e;
        } catch (\Throwable $e) {
            $writeError = $e;
        }

        if ($writeError !== null) {
            return [
                'errors'=> true,
                'message' => $writeError->getMessage() //ImportException::fromErrorCode(ImportException::E_FILE_UNABLE_TO_STORE, $writeError)
            ];
        }
        
        return [
            'success' => true,
            'file' => $filename
        ];
    }

    public static function size($filename, $dir = '') 
    {
        return filesize(DIR_APPLICATION.'/files/tmp/'.$dir.$filename);
    }

    public static function mimeType($filename, $dir = '')
    {
        $app = Application::getFacadeApplication();   

        $storageLocation = $app->make(ImportOptions::class)->getStorageLocation();
        $filesystem = $storageLocation->getFileSystemObject();

        return $filesystem->getMimetype(DIR_APPLICATION.'/files/tmp/'.$dir.$filename);
    }
   
}
