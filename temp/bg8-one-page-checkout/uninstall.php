<?php
/**
 * Uninstall script for BG8 One Page Checkout
 * 
 * This file is executed when the plugin is uninstalled (deleted) via WordPress admin.
 * It removes all plugin data and settings.
 * 
 * @package BG8_OnePageCheckout
 * @version 1.5.1
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
delete_option('bg8_sc_options');

// Remove any transients
delete_transient('bg8_sc_version_check');

// Clear any cached data
wp_cache_flush();

// Note: We don't remove user meta or post meta as it might be needed for other purposes
// If you need to remove specific data, add it here
