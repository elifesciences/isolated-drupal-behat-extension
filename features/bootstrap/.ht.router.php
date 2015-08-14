<?php

$url = parse_url($_SERVER["REQUEST_URI"]);

if (file_exists('.' . $url['path'])) {
    // Serve the requested resource as-is.
    return false;
}

// Populate the "q" query key with the path, skip the leading slash.
$_GET['q'] = $_REQUEST['q'] = ltrim($url['path'], '/');

// Run Drupal.
require 'index.php';
