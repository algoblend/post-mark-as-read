<?php
/**
 * Bootstrap for PHPUnit tests
 * Simplified bootstrap that doesn't require full WordPress test infrastructure
 */

// Composer autoloader
$autoload = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Set up test environment
define('ABSPATH', '/tmp/wordpress/');

echo "\n";
echo "========================================\n";
echo "Post Mark as Read Plugin - Test Suite\n";
echo "========================================\n";
echo "Running simplified tests without full WordPress test infrastructure\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHPUnit Version: " . PHPUnit\Runner\Version::id() . "\n";
echo "========================================\n";
echo "\n";
