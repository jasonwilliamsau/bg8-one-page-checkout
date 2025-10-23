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

        add_action( 'wp_head', [ __CLASS__, 'prepaint_inline_flag' ], 1 );
        add_action( 'wp_head', [ __CLASS__, 'inject_custom_css' ], 2 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    /**
     * Adds a pre-paint class to <html> on checkout to avoid FOUC and show overlay.
     */
    public static function prepaint_inline_flag() {
        if ( function_exists('is_checkout') && is_checkout() ) {
            echo '<script>document.documentElement.classList.add("wc-prep");</script>';
        }
    }

    /**
     * Inject custom CSS variables based on admin settings
     */
    public static function inject_custom_css() {
        if ( ! function_exists('is_checkout') || ! is_checkout() ) { return; }

        $brand_color = Admin::get_option( 'brand_color', '#d4127c' );
        $primary_color = Admin::get_option( 'primary_color', '#0073aa' );
        $success_color = Admin::get_option( 'success_color', '#00a32a' );

        echo '<style id="bg8-sc-custom-vars">';
        echo ':root {';
        echo '--brand: ' . esc_attr( $brand_color ) . ';';
        echo '--primary: ' . esc_attr( $primary_color ) . ';';
        echo '--success: ' . esc_attr( $success_color ) . ';';
        echo '}';
        echo '</style>';
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
