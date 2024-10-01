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
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);

                // Add current file to the zip archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Close and save the archive
        $zip->close();

        return true;
    }
}
