<?php

$baseDir = dirname(__DIR__, 2);
$addonName = 'queue';
$namespacePrefix = 'BoldMinded\Queue';
$addonDirName = 'addons/' . $addonName;
$addonDir = $baseDir . '/' . $addonDirName;
$addonDistDir = 'system/user/' . $addonDirName;
$outputDir = $baseDir . '/build';
$tempDir = sys_get_temp_dir() . '/' . $addonName;

// Set this as tmp for easier debugging
//$tempDir = $baseDir . '/tmp';
