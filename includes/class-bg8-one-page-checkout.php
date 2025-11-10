<?php
namespace BG8OPC\OnePageCheckout;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Plugin {

    public static function init() {
        // Load admin functionality
        if ( is_admin() ) {
            require_once BG8OPC_DIR . 'includes/class-bg8-admin.php';
            Admin::init();
            return;
        }

        // Only run on frontend and when WooCommerce is active
        if ( ! function_exists( 'is_checkout' ) ) {
            return;
        }

        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ], 10 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'add_inline_prepaint_script' ], 1 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'add_inline_custom_css' ], 11 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'add_inline_header_config' ], 11 );
    }

    /**
     * Adds a pre-paint class to <html> on checkout to avoid FOUC and show overlay.
     */
    public static function add_inline_prepaint_script() {
        // Only add loader on main checkout page, not order-received or order-pay pages
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }
        if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) { return; }
        
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
        
        // Register a placeholder script handle for the prepaint inline script
        wp_register_script( 'bg8opc-prepaint', '', [], BG8OPC_VERSION, false );
        wp_enqueue_script( 'bg8opc-prepaint' );
        wp_add_inline_script( 'bg8opc-prepaint', 'document.documentElement.classList.add("wc-prep");' );
    }

    /**
     * Add inline custom CSS variables based on admin settings
     */
    public static function add_inline_custom_css() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }
        if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) { return; }

        try {
            $brand_color = self::get_option( 'brand_color', '#d4127c' );
            $primary_color = self::get_option( 'primary_color', '#0073aa' );
            $success_color = self::get_option( 'success_color', '#00a32a' );
            $header_text_color = self::get_option( 'header_text_color', '#ffffff' );

            $custom_css = ':root {';
            $custom_css .= '--bg8-brand: ' . esc_attr( $brand_color ) . ';';
            $custom_css .= '--bg8-primary: ' . esc_attr( $primary_color ) . ';';
            $custom_css .= '--bg8-success: ' . esc_attr( $success_color ) . ';';
            $custom_css .= '--bg8-header-text: ' . esc_attr( $header_text_color ) . ';';
            $custom_css .= '}';

            wp_add_inline_style( 'bg8opc-checkout', $custom_css );
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
        $options = get_option( 'bg8opc_options', [] );
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }

    /**
     * Add inline header configuration for JavaScript
     */
    public static function add_inline_header_config() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }
        if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) { return; }

        try {
            $checkout_title = self::get_option( 'checkout_title', 'Checkout' );
            $checkout_description = self::get_option( 'checkout_description', 'Complete your purchase in 3 simple steps' );
            $pickup_delivery_first = self::get_option( 'pickup_delivery_first', false );
            $tab_order = self::get_option( 'tab_order', 'delivery,billing,shipping,payment' );
            $pickup_delivery_heading = self::get_option( 'pickup_delivery_heading', 'How would you like to receive your order?' );
            $pickup_delivery_description = self::get_option( 'pickup_delivery_description', '' );
            $pickup_delivery_icon = self::get_option( 'pickup_delivery_icon', '' );
            
            // Only show header if at least one field has content
            $show_header = !empty( $checkout_title ) || !empty( $checkout_description );
            
            $config_script = 'window.bg8CheckoutConfig = {';
            $config_script .= 'title: ' . wp_json_encode( $checkout_title ) . ',';
            $config_script .= 'description: ' . wp_json_encode( $checkout_description ) . ',';
            $config_script .= 'showHeader: ' . ( $show_header ? 'true' : 'false' ) . ',';
            $config_script .= 'pickupDeliveryFirst: ' . ( $pickup_delivery_first ? 'true' : 'false' ) . ',';
            $config_script .= 'tabOrder: ' . wp_json_encode( $tab_order ) . ',';
            $config_script .= 'pickupDeliveryHeading: ' . wp_json_encode( $pickup_delivery_heading ) . ',';
            $config_script .= 'pickupDeliveryDescription: ' . wp_json_encode( $pickup_delivery_description ) . ',';
            $config_script .= 'pickupDeliveryIcon: ' . wp_json_encode( $pickup_delivery_icon );
            $config_script .= '};';

            wp_add_inline_script( 'bg8opc-checkout', $config_script, 'before' );
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
        if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) { return; }

        // Styles
        wp_enqueue_style(
            'bg8opc-checkout',
            BG8OPC_URL . 'assets/css/checkout.css',
            [],
            BG8OPC_VERSION
        );

        // Script
        wp_enqueue_script(
            'bg8opc-checkout',
            BG8OPC_URL . 'assets/js/checkout.js',
            [],
            BG8OPC_VERSION,
            true
        );
    }
}
