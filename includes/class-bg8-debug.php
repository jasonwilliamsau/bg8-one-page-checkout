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
        echo '<strong>' . esc_html__( 'BG8 One Page Checkout Debug Info:', 'bg8-one-page-checkout' ) . '</strong><br>';
        
        // Plugin status
        echo esc_html__( 'Plugin Active:', 'bg8-one-page-checkout' ) . ' ' . ( is_plugin_active( 'bg8-one-page-checkout/bg8-one-page-checkout.php' ) ? esc_html__( 'Yes', 'bg8-one-page-checkout' ) : esc_html__( 'No', 'bg8-one-page-checkout' ) ) . '<br>';
        
        // WooCommerce status
        echo esc_html__( 'WooCommerce Active:', 'bg8-one-page-checkout' ) . ' ' . ( function_exists( 'is_woocommerce' ) ? esc_html__( 'Yes', 'bg8-one-page-checkout' ) : esc_html__( 'No', 'bg8-one-page-checkout' ) ) . '<br>';
        
        // Checkout function
        echo esc_html__( 'is_checkout() Available:', 'bg8-one-page-checkout' ) . ' ' . ( function_exists( 'is_checkout' ) ? esc_html__( 'Yes', 'bg8-one-page-checkout' ) : esc_html__( 'No', 'bg8-one-page-checkout' ) ) . '<br>';
        
        // Current page
        if ( function_exists( 'is_checkout' ) ) {
            echo esc_html__( 'Is Checkout Page:', 'bg8-one-page-checkout' ) . ' ' . ( is_checkout() ? esc_html__( 'Yes', 'bg8-one-page-checkout' ) : esc_html__( 'No', 'bg8-one-page-checkout' ) ) . '<br>';
        }
        
        // Plugin constants
        echo esc_html__( 'BG8_SC_VERSION:', 'bg8-one-page-checkout' ) . ' ' . esc_html( defined( 'BG8_SC_VERSION' ) ? BG8_SC_VERSION : esc_html__( 'Not defined', 'bg8-one-page-checkout' ) ) . '<br>';
        echo esc_html__( 'BG8_SC_DIR:', 'bg8-one-page-checkout' ) . ' ' . esc_html( defined( 'BG8_SC_DIR' ) ? BG8_SC_DIR : esc_html__( 'Not defined', 'bg8-one-page-checkout' ) ) . '<br>';
        echo esc_html__( 'BG8_SC_URL:', 'bg8-one-page-checkout' ) . ' ' . esc_html( defined( 'BG8_SC_URL' ) ? BG8_SC_URL : esc_html__( 'Not defined', 'bg8-one-page-checkout' ) ) . '<br>';
        
        // Plugin options
        $options = get_option( 'bg8_sc_options', [] );
        echo esc_html__( 'Options Count:', 'bg8-one-page-checkout' ) . ' ' . esc_html( count( $options ) ) . '<br>';
        
        // Error log check
        $error_log = ini_get( 'error_log' );
        echo esc_html__( 'Error Log:', 'bg8-one-page-checkout' ) . ' ' . esc_html( $error_log ? $error_log : esc_html__( 'Not set', 'bg8-one-page-checkout' ) ) . '<br>';
        
        echo '<br><small>' . esc_html__( 'Remove BG8_SC_DEBUG from wp-config.php to hide this.', 'bg8-one-page-checkout' ) . '</small>';
        echo '</div>';
    }
    
    public static function log_error( $message ) {
        if ( defined( 'BG8_SC_DEBUG' ) && BG8_SC_DEBUG ) {
            // This error_log is intentionally used for debug purposes only
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug functionality only
            error_log( '[BG8 One Page Checkout] ' . $message );
        }
    }
}

// Initialize debug if enabled
BG8_Debug::init();
