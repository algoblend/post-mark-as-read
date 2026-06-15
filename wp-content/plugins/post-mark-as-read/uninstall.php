<?php
/**
 * Uninstall Script for Post Mark as Read
 * 
 * This file is called when the plugin is deleted via the WordPress admin.
 * It removes all plugin data from the database.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

delete_option('pmar_button_title');
delete_option('pmar_button_icon');
delete_option('pmar_button_location');

$wpdb->query(
    "DELETE FROM {$wpdb->postmeta} 
    WHERE meta_key LIKE 'pmar_read_%' OR meta_key LIKE 'pmar_read_date_%'"
);

delete_option('pmar_version');
