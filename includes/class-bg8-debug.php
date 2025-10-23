<?php
/**
 * BG8 One Page Checkout - Debug Helper
 * 
 * Add this to your wp-config.php temporarily to debug plugin issues:
 * define('BG8_SC_DEBUG', true);
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class BG8_Debug {
    
    public static function init() {
        if ( defined( 'BG8_SC_DEBUG' ) && BG8_SC_DEBUG ) {
            add_action( 'wp_footer', [ __CLASS__, 'debug_info' ] );
            add_action( 'admin_footer', [ __CLASS__, 'debug_info' ] );
        }
    }
    
    public static function debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        echo '<div id="bg8-debug" style="position: fixed; bottom: 0; left: 0; background: #000; color: #fff; padding: 10px; font-size: 12px; z-index: 9999; max-width: 400px; font-family: monospace;">';
        echo '<strong>BG8 One Page Checkout Debug Info:</strong><br>';
        
        // Plugin status
        echo 'Plugin Active: ' . ( is_plugin_active( 'bg8-one-page-checkout/bg8-one-page-checkout.php' ) ? 'Yes' : 'No' ) . '<br>';
        
        // WooCommerce status
        echo 'WooCommerce Active: ' . ( function_exists( 'is_woocommerce' ) ? 'Yes' : 'No' ) . '<br>';
        
        // Checkout function
        echo 'is_checkout() Available: ' . ( function_exists( 'is_checkout' ) ? 'Yes' : 'No' ) . '<br>';
        
        // Current page
        if ( function_exists( 'is_checkout' ) ) {
            echo 'Is Checkout Page: ' . ( is_checkout() ? 'Yes' : 'No' ) . '<br>';
        }
        
        // Plugin constants
        echo 'BG8_SC_VERSION: ' . ( defined( 'BG8_SC_VERSION' ) ? BG8_SC_VERSION : 'Not defined' ) . '<br>';
        echo 'BG8_SC_DIR: ' . ( defined( 'BG8_SC_DIR' ) ? BG8_SC_DIR : 'Not defined' ) . '<br>';
        echo 'BG8_SC_URL: ' . ( defined( 'BG8_SC_URL' ) ? BG8_SC_URL : 'Not defined' ) . '<br>';
        
        // Plugin options
        $options = get_option( 'bg8_sc_options', [] );
        echo 'Options Count: ' . count( $options ) . '<br>';
        
        // Error log check
        $error_log = ini_get( 'error_log' );
        echo 'Error Log: ' . ( $error_log ? $error_log : 'Not set' ) . '<br>';
        
        echo '<br><small>Remove BG8_SC_DEBUG from wp-config.php to hide this.</small>';
        echo '</div>';
    }
    
    public static function log_error( $message ) {
        if ( defined( 'BG8_SC_DEBUG' ) && BG8_SC_DEBUG ) {
            error_log( '[BG8 One Page Checkout] ' . $message );
        }
    }
}

// Initialize debug if enabled
BG8_Debug::init();
