<?php

$baseDir = dirname(__DIR__, 2);
$addonName = 'queue';
$namespacePrefix = 'BoldMinded\Queue';
$addonDirName = 'addons/' . $addonName;
$themeDirName = 'themes/user/' . $addonName;
$addonDir = $baseDir . '/' . $addonDirName;
$themeDir = $baseDir . '/' . $themeDirName;
$addonDistDir = 'system/user/' . $addonDirName;
$themeDistDir = $themeDirName;
$outputDir = $baseDir . '/build';
$tempDir = sys_get_temp_dir() . '/' . $addonName;

// Set this as tmp for easier debugging
//$tempDir = $baseDir . '/tmp';
