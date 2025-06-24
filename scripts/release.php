#!/usr/bin/env php
<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/filesystem.php';
require_once __DIR__ . '/includes/git.php';
require_once __DIR__ . '/includes/tagging.php';

// Create directories
make_dir($tempDir);
make_dir($tempDir . '/system/user/' . $addonDirName);
make_dir($tempDir . '/' . $themeDirName);
make_dir($outputDir);

register_shutdown_function('rmdir_recursive', $tempDir);

// Check branch
if (git_get_branch() !== 'develop') {
    echo 'You are not on the "develop" branch...' . PHP_EOL;
    exit(1);
}

// Determine release tag
$releaseTag = ($argc < 2) ? get_next_tag() : $argv[1];
if (is_valid_tag($releaseTag)) {
    echo sprintf('Building release "%s"...' . PHP_EOL, $releaseTag);
} else {
    echo sprintf('Release tag "%s" is not valid.' . PHP_EOL, $releaseTag);
    exit(1);
}

require_once __DIR__ . '/includes/packaging.php';

// Create git release
git_create_release($releaseTag);
