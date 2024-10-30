<?php

namespace App;

use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Zip
{
    public static function archive($source, $destination): bool
    {
        if (!extension_loaded('zip') || !File::isDirectory($source)) {
            return false;
        }

        $zip = new ZipArchive();

        if (!$zip->open($destination, ZipArchive::CREATE)) {
            return false;
        }

        $source = realpath($source);

        if (!is_dir($source)) {
            return false;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $file) {
            if ($file->isFile()) {
                $filePath     = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);

                // Add current file to the archive
                $zip->addFile($filePath, $relativePath);
            } elseif ($file->isLink()) {
                $filePath     = $file->getPathname();
                $relativePath = substr($filePath, strlen($source) + 1);
                $targetPath = readlink($filePath);

                // Recreate the symlinks in the archive
                $zip->addFromString($relativePath, $targetPath);
                $zip->setExternalAttributesName($relativePath, ZipArchive::OPSYS_UNIX, 0120777 << 16);
            }
        }

        $zip->close();

        return true;
    }
}
