<?php

// Determine output file
$hash = git_get_hash();
$branch = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', str_replace('feature/', '', git_get_branch())));
$outputFileName = sprintf($addonName . '-%s-%s-%s.zip', $releaseTag, $branch, $hash);
$outputName = $outputDir .'/'. $outputFileName;

if (file_exists($outputName)) {
    echo sprintf('The output file "%s" already exists...' . PHP_EOL, $outputName);
    exit(1);
}

// Grab just the production dependencies
exec('composer install -o --no-dev --no-ansi --no-interaction --working-dir='. $baseDir);

// Build release files
$iterator = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($addonDir, \RecursiveDirectoryIterator::SKIP_DOTS),
    \RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $name => $file) {
    $sourceFile = $name;
    $outputFile = str_replace($addonDir, $tempDir . '/' . $addonDistDir, $name);

    if ($file->isDir()) {
        make_dir($outputFile);
    } else {
        copy_file($sourceFile, $outputFile, function ($contents) use ($releaseTag, $hash) {
            $contents = str_replace('@VERSION@', $releaseTag, $contents);
            $contents = str_replace('@BUILD_VERSION@', $hash, $contents);
            return $contents;
        });
    }
}

// Cleanup
exec('find '. $tempDir .' | grep composer.json | xargs rm');
exec('find '. $tempDir .' | grep composer.lock | xargs rm');
// Remove main vendor dir, we have everything scoped in vendor-build
exec('rm -rf '. $tempDir . '/' . $addonDistDir . '/vendor');
exec('rm -rf '. $tempDir . '/' . $addonDistDir . '/vendor-bin');

// Create release archive
$zipArchive = new ZipArchive();
$zipArchive->open($outputName, ZipArchive::CREATE);

$iterator = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
    \RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $name => $file) {
    $local_name = str_replace(rtrim($tempDir, '/') . '/', '', $name);

    if ($file->isDir()) {
        continue;
    } else {
        $zipArchive->addFile($name, $local_name);
    }
}

$zipArchive->close();

// Now that we've made the build, run install gain to regain dev dependencies we might have just deleted
exec('composer install -o --no-ansi --no-interaction --working-dir='. $baseDir);

exec(sprintf('cp %s %s', $outputName, '~/Dropbox/ee/releases/'. $addonName .'/'. $outputFileName));
echo sprintf('Build %s created!', $outputFileName) . "\n";
