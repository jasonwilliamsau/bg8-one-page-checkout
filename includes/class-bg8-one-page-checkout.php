<?php
namespace BG8\OnePageCheckout;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Plugin {

    public static function init() {
        // Load admin functionality
        if ( is_admin() ) {
            require_once BG8_SC_DIR . 'includes/class-bg8-admin.php';
            Admin::init();
            return;
        }

        // Only run on frontend and when WooCommerce is active
        if ( ! function_exists( 'is_checkout' ) ) {
            return;
        }

        add_action( 'wp_head', [ __CLASS__, 'prepaint_inline_flag' ], 1 );
        add_action( 'wp_head', [ __CLASS__, 'inject_custom_css' ], 999 );
        add_action( 'wp_head', [ __CLASS__, 'inject_header_config' ], 3 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    /**
     * Adds a pre-paint class to <html> on checkout to avoid FOUC and show overlay.
     */
    public static function prepaint_inline_flag() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }
        
        // Skip loader in page builders and admin
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Page builder detection, not form processing
        if ( is_admin() || 
             ( isset( $_GET['ct_builder'] ) && sanitize_text_field( wp_unslash( $_GET['ct_builder'] ) ) ) || // Oxygen Builder
             ( isset( $_GET['elementor-preview'] ) && sanitize_text_field( wp_unslash( $_GET['elementor-preview'] ) ) ) || // Elementor
             ( isset( $_GET['et_fb'] ) && sanitize_text_field( wp_unslash( $_GET['et_fb'] ) ) ) || // Divi Builder
             ( isset( $_GET['bricks'] ) && sanitize_text_field( wp_unslash( $_GET['bricks'] ) ) ) || // Bricks Builder
             ( isset( $_GET['fl_builder'] ) && sanitize_text_field( wp_unslash( $_GET['fl_builder'] ) ) ) ) { // Beaver Builder
            return;
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        
        echo '<script>document.documentElement.classList.add("wc-prep");</script>';
    }

    /**
     * Inject custom CSS variables based on admin settings
     */
    public static function inject_custom_css() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }

        try {
            $brand_color = self::get_option( 'brand_color', '#d4127c' );
            $primary_color = self::get_option( 'primary_color', '#0073aa' );
            $success_color = self::get_option( 'success_color', '#00a32a' );
            $header_text_color = self::get_option( 'header_text_color', '#ffffff' );

            echo '<style id="bg8-sc-custom-vars">';
            echo ':root {';
            echo '--bg8-brand: ' . esc_attr( $brand_color ) . ';';
            echo '--bg8-primary: ' . esc_attr( $primary_color ) . ';';
            echo '--bg8-success: ' . esc_attr( $success_color ) . ';';
            echo '--bg8-header-text: ' . esc_attr( $header_text_color ) . ';';
            echo '}';
            echo '</style>';
        } catch ( Exception $e ) {
            // Silently fail if there's an error with options
            // This error_log is intentionally used for debug purposes only
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug functionality only
            error_log( 'BG8 One Page Checkout CSS injection error: ' . $e->getMessage() );
        }
    }

    /**
     * Get option value with fallback to default (frontend-safe)
     */
    public static function get_option( $key, $default = '' ) {
        $options = get_option( 'bg8_sc_options', [] );
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }

    /**
     * Inject header configuration for JavaScript
     */
    public static function inject_header_config() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }

        try {
            $checkout_title = self::get_option( 'checkout_title', 'Checkout' );
            $checkout_description = self::get_option( 'checkout_description', 'Complete your purchase in 3 simple steps' );
            $pickup_delivery_first = self::get_option( 'pickup_delivery_first', false );
            
            // Only show header if at least one field has content
            $show_header = !empty( $checkout_title ) || !empty( $checkout_description );
            
            echo '<script id="bg8-sc-header-config">';
            echo 'window.bg8CheckoutConfig = {';
            echo 'title: ' . json_encode( $checkout_title ) . ',';
            echo 'description: ' . json_encode( $checkout_description ) . ',';
            echo 'showHeader: ' . ( $show_header ? 'true' : 'false' ) . ',';
            echo 'pickupDeliveryFirst: ' . ( $pickup_delivery_first ? 'true' : 'false' );
            echo '};';
            echo '</script>';
        } catch ( Exception $e ) {
            // Silently fail if there's an error with options
            // This error_log is intentionally used for debug purposes only
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug functionality only
            error_log( 'BG8 One Page Checkout header config error: ' . $e->getMessage() );
        }
    }

    /**
     * Enqueue CSS and JS assets on checkout only.
     */
    public static function enqueue_assets() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }

        // Styles
        wp_enqueue_style(
            'bg8-sc-checkout',
            BG8_SC_URL . 'assets/css/checkout.css',
            [],
            BG8_SC_VERSION
        );

        // Script
        wp_enqueue_script(
            'bg8-sc-checkout',
            BG8_SC_URL . 'assets/js/checkout.js',
            [],
            BG8_SC_VERSION,
            true
        );
    }
}
