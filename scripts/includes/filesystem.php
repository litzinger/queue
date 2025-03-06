<?php

/**
 * @param string $path
 * @return bool
 */
function rmdir_recursive($path)
{
    if (!@is_dir($path)) {
        return true;
    }

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $name => $file) {
        if ($file->isDir()) {
            @rmdir($name);
        } else {
            @unlink($name);
        }
    }

    if (file_exists($path)) {
        @rmdir($path);
    }

    return true;
}

/**
 * @param string $path
 */
function make_dir($path)
{
    if (!@mkdir($path, 0777, true) && !is_dir($path)) {
        echo sprintf('Could not create "%s" directory...' . PHP_EOL, $path);
        exit(1);
    }
}

/**
 * @param string   $source
 * @param string   $dest
 * @param callable $process
 */
function copy_file($source, $dest, callable $process)
{
    $contents = @file_get_contents($source);
    $contents = $process($contents);
    @file_put_contents($dest, $contents);
    unset($contents);
}

function copy_dir($source, $dest) {
    make_dir($dest);
    rename($source, $dest);
}

function download_and_extract_package(string $repoName, string $className, string $buildDir, string $addonDir)
{
    $buildSrc = $buildDir . sprintf('/%s', $repoName);
    $repoSrc = $buildDir . sprintf('/%s/%s-master/src', $repoName, $repoName);
    $dest = $addonDir . sprintf('/Library/%s', $className);

    // Remove old package
    exec(sprintf('rm -rf %s', $dest));

    // Grab new files and move them into place
    exec(sprintf('wget -O build/%s-master.zip https://github.com/litzinger/%s/archive/master.zip', $repoName, $repoName));

    $zip = new ZipArchive;
    $res = $zip->open($buildDir . sprintf('/%s-master.zip', $repoName));

    if ($res === true) {
        $zip->extractTo($buildDir . sprintf('/%s', $repoName));
        $zip->close();

        copy_dir($repoSrc, $dest);

        // Cleanup
        exec(sprintf('rm -rf %s', $buildSrc));
    } else {
        echo sprintf('Could not extract %s :( Aborting build.', $repoName);
    }
}

