<?php

// path to test lib bootstrap.php
$test_lib_bootstrap_file = dirname( __FILE__ ) . '/includes/bootstrap.php';

if ( ! file_exists( $test_lib_bootstrap_file ) ) {
    echo PHP_EOL . "Error : unable to find " . $test_lib_bootstrap_file . PHP_EOL;
    exit( '' . PHP_EOL );
}

// set plugin and options for activation
$GLOBALS[ 'wp_tests_options' ] = array(
        'active_plugins' => array(
                'post-mark-as-read/post-mark-as-read.php'
        ),
        'wpsp_test' => true
);

// call test-lib's bootstrap.php
require_once $test_lib_bootstrap_file;

$current_user = new WP_User( 1 );
$current_user->set_role( 'administrator' );

echo PHP_EOL;
echo 'Using Wordpress core : ' . ABSPATH . PHP_EOL;
echo PHP_EOL;