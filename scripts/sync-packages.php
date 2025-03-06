<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/filesystem.php';

$iterator = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($addonDir, \RecursiveDirectoryIterator::SKIP_DOTS),
    \RecursiveIteratorIterator::LEAVES_ONLY
);

download_and_extract_package('basee', 'Basee', $outputDir, $addonDir);

// Update the namespaces
foreach ($iterator as $name => $file) {
    if ($file->isDir()) {
        continue;
    } else {
        copy_file($name, $name, function ($contents) use ($namespacePrefix) {
            $contents = str_replace('namespace Litzinger\Basee', 'namespace '. $namespacePrefix .'\Library\Basee', $contents);
            $contents = str_replace('use Litzinger\Basee', 'use '. $namespacePrefix .'\Library\Basee', $contents);

            return $contents;
        });
    }
}


