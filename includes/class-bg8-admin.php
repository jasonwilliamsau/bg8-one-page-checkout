<?php
namespace BG8\OnePageCheckout;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Admin {

    const OPTION_GROUP = 'bg8_sc_settings';
    const OPTION_NAME = 'bg8_sc_options';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
    }

    /**
     * Add admin menu page
     */
    public static function add_admin_menu() {
        add_options_page(
            __( 'BG8 One Page Checkout Settings', 'bg8-one-page-checkout' ),
            __( 'BG8 Checkout', 'bg8-one-page-checkout' ),
            'manage_options',
            'bg8-one-page-checkout',
            [ __CLASS__, 'admin_page' ]
        );
    }

    /**
     * Register settings and fields
     */
    public static function register_settings() {
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_NAME,
            [ __CLASS__, 'sanitize_options' ]
        );

        // Colors section
        add_settings_section(
            'bg8_sc_colors',
            __( 'Color Settings', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'colors_section_callback' ],
            'bg8-one-page-checkout'
        );

        add_settings_field(
            'brand_color',
            __( 'Brand Color', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'color_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_colors',
            [ 'field' => 'brand_color', 'default' => '#d4127c' ]
        );

        add_settings_field(
            'primary_color',
            __( 'Primary Color', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'color_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_colors',
            [ 'field' => 'primary_color', 'default' => '#0073aa' ]
        );

        add_settings_field(
            'success_color',
            __( 'Success Color', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'color_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_colors',
            [ 'field' => 'success_color', 'default' => '#00a32a' ]
        );

        // Tab Labels section
        add_settings_section(
            'bg8_sc_labels',
            __( 'Tab Labels & Headings', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'labels_section_callback' ],
            'bg8-one-page-checkout'
        );

        add_settings_field(
            'step_1_label',
            __( 'Step 1 Label', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_labels',
            [ 'field' => 'step_1_label', 'default' => 'Your Details' ]
        );

        add_settings_field(
            'step_1_heading',
            __( 'Step 1 Heading', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_labels',
            [ 'field' => 'step_1_heading', 'default' => 'Enter your billing information' ]
        );

        add_settings_field(
            'step_2_label',
            __( 'Step 2 Label', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_labels',
            [ 'field' => 'step_2_label', 'default' => 'Recipient' ]
        );

        add_settings_field(
            'step_2_heading',
            __( 'Step 2 Heading', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_labels',
            [ 'field' => 'step_2_heading', 'default' => 'Shipping information' ]
        );

        add_settings_field(
            'step_3_label',
            __( 'Step 3 Label', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_labels',
            [ 'field' => 'step_3_label', 'default' => 'Confirm' ]
        );

        add_settings_field(
            'step_3_heading',
            __( 'Step 3 Heading', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_labels',
            [ 'field' => 'step_3_heading', 'default' => 'Review your order' ]
        );
    }

    /**
     * Sanitize options
     */
    public static function sanitize_options( $input ) {
        $sanitized = [];
        
        // Sanitize colors
        $color_fields = [ 'brand_color', 'primary_color', 'success_color' ];
        foreach ( $color_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = sanitize_hex_color( $input[ $field ] );
            }
        }

        // Sanitize text fields
        $text_fields = [ 'step_1_label', 'step_1_heading', 'step_2_label', 'step_2_heading', 'step_3_label', 'step_3_heading' ];
        foreach ( $text_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = sanitize_text_field( $input[ $field ] );
            }
        }

        return $sanitized;
    }

    /**
     * Colors section callback
     */
    public static function colors_section_callback() {
        echo '<p>' . __( 'Configure the color scheme for your checkout steps.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Labels section callback
     */
    public static function labels_section_callback() {
        echo '<p>' . __( 'Customize the labels and headings for each checkout step.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Color field callback
     */
    public static function color_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        
        echo '<input type="color" id="' . esc_attr( $args['field'] ) . '" name="' . self::OPTION_NAME . '[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" />';
        echo '<p class="description">' . sprintf( __( 'Default: %s', 'bg8-one-page-checkout' ), $args['default'] ) . '</p>';
    }

    /**
     * Text field callback
     */
    public static function text_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        
        echo '<input type="text" id="' . esc_attr( $args['field'] ) . '" name="' . self::OPTION_NAME . '[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="regular-text" />';
        echo '<p class="description">' . sprintf( __( 'Default: %s', 'bg8-one-page-checkout' ), $args['default'] ) . '</p>';
    }

    /**
     * Admin page HTML
     */
    public static function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields( self::OPTION_GROUP );
                do_settings_sections( 'bg8-one-page-checkout' );
                submit_button();
                ?>
            </form>

            <div class="bg8-sc-preview">
                <h2><?php _e( 'Preview', 'bg8-one-page-checkout' ); ?></h2>
                <p><?php _e( 'Visit your checkout page to see the changes in action.', 'bg8-one-page-checkout' ); ?></p>
                <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'checkout' ) ); ?>" class="button button-secondary" target="_blank">
                        <?php _e( 'View Checkout Page', 'bg8-one-page-checkout' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public static function enqueue_admin_assets( $hook ) {
        if ( 'settings_page_bg8-one-page-checkout' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        
        wp_add_inline_script( 'wp-color-picker', '
            jQuery(document).ready(function($) {
                $("input[type=\'color\']").wpColorPicker();
            });
        ' );
    }

    /**
     * Get option value with fallback to default
     */
    public static function get_option( $key, $default = '' ) {
        $options = get_option( self::OPTION_NAME, [] );
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }
}
