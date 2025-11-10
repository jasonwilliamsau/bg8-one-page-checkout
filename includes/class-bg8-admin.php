<?php
namespace BG8OPC\OnePageCheckout;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Admin {

    const OPTION_GROUP = 'bg8opc_settings';
    const OPTION_NAME = 'bg8opc_options';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
    }

    /**
     * Add admin menu page
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
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
            '', // Empty title to prevent duplicate headings
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

        add_settings_field(
            'header_text_color',
            __( 'Header Text Color', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'color_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_colors',
            [ 'field' => 'header_text_color', 'default' => '#ffffff' ]
        );

        // Tab Labels section
        add_settings_section(
            'bg8_sc_labels',
            '', // Empty title to prevent duplicate headings
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

        // Header section
        add_settings_section(
            'bg8_sc_header',
            '', // Empty title to prevent duplicate headings
            [ __CLASS__, 'header_section_callback' ],
            'bg8-one-page-checkout'
        );

        add_settings_field(
            'checkout_title',
            __( 'Checkout Title', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_header',
            [ 'field' => 'checkout_title', 'default' => 'Checkout' ]
        );

        add_settings_field(
            'checkout_description',
            __( 'Checkout Description', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_header',
            [ 'field' => 'checkout_description', 'default' => 'Complete your purchase in 3 simple steps' ]
        );

        // Checkout Options section
        add_settings_section(
            'bg8_sc_options',
            '', // Empty title to prevent duplicate headings
            [ __CLASS__, 'options_section_callback' ],
            'bg8-one-page-checkout'
        );

        add_settings_field(
            'pickup_delivery_first',
            __( 'Pickup / Delivery First', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'checkbox_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_options',
            [ 'field' => 'pickup_delivery_first', 'default' => false ]
        );

        // Tab Order section
        add_settings_section(
            'bg8_sc_tab_order',
            '', // Empty title to prevent duplicate headings
            [ __CLASS__, 'tab_order_section_callback' ],
            'bg8-one-page-checkout'
        );

        add_settings_field(
            'tab_order',
            __( 'Tab Order', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'tab_order_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_tab_order',
            [ 'field' => 'tab_order', 'default' => 'delivery,billing,shipping,payment' ]
        );

        // Pickup/Delivery Tab Customization section
        add_settings_section(
            'bg8_sc_pickup_delivery',
            '', // Empty title to prevent duplicate headings
            [ __CLASS__, 'pickup_delivery_section_callback' ],
            'bg8-one-page-checkout'
        );

        add_settings_field(
            'pickup_delivery_heading',
            __( 'Tab Heading', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_pickup_delivery',
            [ 'field' => 'pickup_delivery_heading', 'default' => 'How would you like to receive your order?', 'required' => true ]
        );

        add_settings_field(
            'pickup_delivery_description',
            __( 'Tab Description', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'textarea_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_pickup_delivery',
            [ 'field' => 'pickup_delivery_description', 'default' => '', 'required' => false ]
        );

        add_settings_field(
            'pickup_delivery_icon',
            __( 'Tab Icon', 'bg8-one-page-checkout' ),
            [ __CLASS__, 'text_field_callback' ],
            'bg8-one-page-checkout',
            'bg8_sc_pickup_delivery',
            [ 'field' => 'pickup_delivery_icon', 'default' => '', 'required' => false ]
        );
    }

    /**
     * Sanitize options
     */
    public static function sanitize_options( $input ) {
        $sanitized = [];
        
        // Sanitize colors
        $color_fields = [ 'brand_color', 'primary_color', 'success_color', 'header_text_color' ];
        foreach ( $color_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = sanitize_hex_color( $input[ $field ] );
            }
        }

        // Sanitize text fields
        $text_fields = [ 'step_1_label', 'step_1_heading', 'step_2_label', 'step_2_heading', 'step_3_label', 'step_3_heading', 'checkout_title', 'checkout_description', 'pickup_delivery_heading', 'pickup_delivery_icon' ];
        foreach ( $text_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = sanitize_text_field( $input[ $field ] );
            }
        }

        // Sanitize textarea fields
        if ( isset( $input['pickup_delivery_description'] ) ) {
            $sanitized['pickup_delivery_description'] = sanitize_textarea_field( $input['pickup_delivery_description'] );
        }

        // Sanitize tab order
        if ( isset( $input['tab_order'] ) ) {
            $tab_order = sanitize_text_field( $input['tab_order'] );
            // Validate tab order contains valid tabs
            $valid_tabs = [ 'delivery', 'billing', 'shipping', 'payment' ];
            $tabs = array_map( 'trim', explode( ',', $tab_order ) );
            $tabs = array_filter( $tabs, function( $tab ) use ( $valid_tabs ) {
                return in_array( strtolower( $tab ), $valid_tabs, true );
            } );
            // Ensure all required tabs are present
            if ( count( $tabs ) === count( $valid_tabs ) && count( array_unique( $tabs ) ) === count( $valid_tabs ) ) {
                $sanitized['tab_order'] = implode( ',', $tabs );
            } else {
                // Use default if invalid
                $sanitized['tab_order'] = 'delivery,billing,shipping,payment';
            }
        }

        // Sanitize checkbox fields
        if ( isset( $input['pickup_delivery_first'] ) ) {
            $sanitized['pickup_delivery_first'] = (bool) $input['pickup_delivery_first'];
        } else {
            $sanitized['pickup_delivery_first'] = false;
        }

        // Validate required fields
        if ( isset( $sanitized['pickup_delivery_heading'] ) && empty( trim( $sanitized['pickup_delivery_heading'] ) ) ) {
            $sanitized['pickup_delivery_heading'] = 'How would you like to receive your order?';
        }

        return $sanitized;
    }

    /**
     * Colors section callback
     */
    public static function colors_section_callback() {
        echo '<div class="bg8-section-title">' . esc_html__( 'Color Settings', 'bg8-one-page-checkout' ) . '</div>';
        echo '<p>' . esc_html__( 'Configure the color scheme for your checkout steps.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Labels section callback
     */
    public static function labels_section_callback() {
        echo '<div class="bg8-section-title">' . esc_html__( 'Tab Labels & Headings', 'bg8-one-page-checkout' ) . '</div>';
        echo '<p>' . esc_html__( 'Customize the labels and headings for each checkout step.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Header section callback
     */
    public static function header_section_callback() {
        echo '<div class="bg8-section-title">' . esc_html__( 'Checkout Header', 'bg8-one-page-checkout' ) . '</div>';
        echo '<p>' . esc_html__( 'Customize the checkout page title and description. Leave both blank to hide the header completely.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Options section callback
     */
    public static function options_section_callback() {
        echo '<div class="bg8-section-title">' . esc_html__( 'Checkout Options', 'bg8-one-page-checkout' ) . '</div>';
        echo '<p>' . esc_html__( 'Configure how the checkout flow works.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Tab order section callback
     */
    public static function tab_order_section_callback() {
        echo '<div class="bg8-section-title">' . esc_html__( 'Tab Order', 'bg8-one-page-checkout' ) . '</div>';
        echo '<p>' . esc_html__( 'Specify the order of tabs: delivery, billing, shipping, payment. Enter comma-separated values (e.g., delivery,billing,shipping,payment).', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Pickup/Delivery section callback
     */
    public static function pickup_delivery_section_callback() {
        echo '<div class="bg8-section-title">' . esc_html__( 'Pickup/Delivery Tab Customization', 'bg8-one-page-checkout' ) . '</div>';
        echo '<p>' . esc_html__( 'Customize the content of the pickup/delivery tab. Heading is required; description and icon are optional.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Color field callback
     */
    public static function color_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        
        echo '<input type="color" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="bg8-color-picker" />';
        // translators: %s is the default color value
        echo '<p class="bg8-description">' . sprintf( esc_html__( 'Default: %s', 'bg8-one-page-checkout' ), esc_html( $args['default'] ) ) . '</p>';
    }

    /**
     * Text field callback
     */
    public static function text_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        $required = isset( $args['required'] ) && $args['required'];
        
        echo '<input type="text" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="bg8-text-input" ' . ( $required ? 'required' : '' ) . ' />';
        // translators: %s is the default text value
        if ( ! empty( $args['default'] ) ) {
            echo '<p class="bg8-description">' . sprintf( esc_html__( 'Default: %s', 'bg8-one-page-checkout' ), esc_html( $args['default'] ) ) . '</p>';
        }
        if ( $required ) {
            echo '<p class="bg8-description">' . esc_html__( 'This field is required.', 'bg8-one-page-checkout' ) . '</p>';
        }
    }

    /**
     * Textarea field callback
     */
    public static function textarea_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        $required = isset( $args['required'] ) && $args['required'];
        
        echo '<textarea id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" class="bg8-textarea-input" rows="4" ' . ( $required ? 'required' : '' ) . '>' . esc_textarea( $value ) . '</textarea>';
        // translators: %s is the default text value
        if ( ! empty( $args['default'] ) ) {
            echo '<p class="bg8-description">' . sprintf( esc_html__( 'Default: %s', 'bg8-one-page-checkout' ), esc_html( $args['default'] ) ) . '</p>';
        }
        if ( $required ) {
            echo '<p class="bg8-description">' . esc_html__( 'This field is required.', 'bg8-one-page-checkout' ) . '</p>';
        }
    }

    /**
     * Tab order field callback
     */
    public static function tab_order_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        
        echo '<input type="text" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="bg8-text-input" placeholder="delivery,billing,shipping,payment" />';
        echo '<p class="bg8-description">' . esc_html__( 'Enter comma-separated tab names: delivery, billing, shipping, payment. All four tabs must be included.', 'bg8-one-page-checkout' ) . '</p>';
        // translators: %s is the default tab order
        echo '<p class="bg8-description">' . sprintf( esc_html__( 'Default: %s', 'bg8-one-page-checkout' ), esc_html( $args['default'] ) ) . '</p>';
    }

    /**
     * Checkbox field callback
     */
    public static function checkbox_field_callback( $args ) {
        $options = get_option( self::OPTION_NAME, [] );
        $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        
        echo '<input type="checkbox" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="1" ' . checked( $value, true, false ) . ' class="bg8-checkbox" />';
        echo '<label for="' . esc_attr( $args['field'] ) . '">' . esc_html__( 'Show pickup/delivery selection before billing information', 'bg8-one-page-checkout' ) . '</label>';
        echo '<p class="bg8-description">' . esc_html__( 'Enable this to let customers choose pickup or delivery first. Pickup only requires billing info; delivery requires both billing and shipping info.', 'bg8-one-page-checkout' ) . '</p>';
        echo '<p class="bg8-description">' . esc_html__( 'The plugin will automatically detect your shipping methods. Pickup methods (local_pickup, click & collect) and delivery methods (flat rate, table rate, etc.) are identified automatically.', 'bg8-one-page-checkout' ) . '</p>';
    }

    /**
     * Shipping method field callback
     */
    public static function shipping_method_field_callback( $args ) {
        // Initialize output buffer to capture any errors
        ob_start();
        
        try {
            $options = get_option( self::OPTION_NAME, [] );
            $value = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
            
            echo '<input type="text" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="bg8-text-input" placeholder="e.g. local_pickup:100" />';
            
            if ( $args['type'] === 'pickup' ) {
                echo '<p class="bg8-description">' . esc_html__( 'Enter the rate ID for pickup (e.g., "local_pickup:100"). Leave empty for auto-detect.', 'bg8-one-page-checkout' ) . '</p>';
                echo '<p class="bg8-description">' . esc_html__( 'To find the rate ID, inspect the shipping method radio button value on the checkout page.', 'bg8-one-page-checkout' ) . '</p>';
            } else {
                echo '<p class="bg8-description">' . esc_html__( 'Enter the rate ID for delivery (e.g., "flat_rate:50"). Leave empty for auto-detect.', 'bg8-one-page-checkout' ) . '</p>';
                echo '<p class="bg8-description">' . esc_html__( 'To find the rate ID, inspect the shipping method radio button value on the checkout page.', 'bg8-one-page-checkout' ) . '</p>';
            }
            
            // Output the buffer (content is already escaped at point of creation)
            $output = ob_get_clean();
            echo wp_kses_post( $output );
            
        } catch ( Exception $e ) {
            ob_end_clean();
            echo '<input type="text" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="" class="bg8-text-input" placeholder="e.g. local_pickup:100" />';
            echo '<p class="bg8-description" style="color: #dc3232;">' . esc_html__( 'Error loading field. Enter rate ID manually or leave empty for auto-detect.', 'bg8-one-page-checkout' ) . '</p>';
        } catch ( Error $e ) {
            ob_end_clean();
            echo '<input type="text" id="' . esc_attr( $args['field'] ) . '" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $args['field'] ) . ']" value="" class="bg8-text-input" placeholder="e.g. local_pickup:100" />';
            echo '<p class="bg8-description" style="color: #dc3232;">' . esc_html__( 'Error loading field. Enter rate ID manually or leave empty for auto-detect.', 'bg8-one-page-checkout' ) . '</p>';
        }
    }

    /**
     * Get available shipping methods
     */
    private static function get_shipping_methods() {
        $methods = [];
        
        // Check if WooCommerce is active and loaded
        if ( ! function_exists( 'WC' ) || ! class_exists( 'WC_Shipping_Zones' ) ) {
            return $methods;
        }
        
        try {
            // Get all shipping zones
            $zones = WC_Shipping_Zones::get_zones();
            
            if ( ! is_array( $zones ) ) {
                return $methods;
            }
            
            // Add methods from each zone
            foreach ( $zones as $zone ) {
                if ( ! isset( $zone['shipping_methods'] ) || ! is_array( $zone['shipping_methods'] ) ) {
                    continue;
                }
                
                foreach ( $zone['shipping_methods'] as $method ) {
                    if ( ! is_object( $method ) ) {
                        continue;
                    }
                    
                    // Check if method is enabled
                    $enabled = isset( $method->enabled ) ? $method->enabled : 'no';
                    if ( $enabled !== 'yes' ) {
                        continue;
                    }
                    
                    // Get instance_id and method id
                    $instance_id = property_exists( $method, 'instance_id' ) ? $method->instance_id : null;
                    $method_id = property_exists( $method, 'id' ) ? $method->id : null;
                    
                    if ( empty( $instance_id ) || empty( $method_id ) ) {
                        continue;
                    }
                    
                    // Build the rate ID
                    $rate_id = $method_id . ':' . $instance_id;
                    
                    // Get zone name
                    $zone_name = isset( $zone['zone_name'] ) ? $zone['zone_name'] : __( 'Zone', 'bg8-one-page-checkout' );
                    
                    // Get method title
                    $method_title = $zone_name . ' - ';
                    if ( method_exists( $method, 'get_title' ) ) {
                        $method_title .= $method->get_title();
                    } else if ( isset( $method->title ) ) {
                        $method_title .= $method->title;
                    } else {
                        $method_title .= $method_id;
                    }
                    
                    $methods[ $rate_id ] = $method_title;
                }
            }
            
            // Add methods from default zone (zone 0)
            if ( class_exists( 'WC_Shipping_Zone' ) ) {
                $default_zone = new WC_Shipping_Zone( 0 );
                
                if ( method_exists( $default_zone, 'get_shipping_methods' ) ) {
                    $default_methods = $default_zone->get_shipping_methods( true );
                    
                    if ( is_array( $default_methods ) ) {
                        foreach ( $default_methods as $method ) {
                            if ( ! is_object( $method ) ) {
                                continue;
                            }
                            
                            $enabled = isset( $method->enabled ) ? $method->enabled : 'no';
                            if ( $enabled !== 'yes' ) {
                                continue;
                            }
                            
                            $instance_id = property_exists( $method, 'instance_id' ) ? $method->instance_id : null;
                            $method_id = property_exists( $method, 'id' ) ? $method->id : null;
                            
                            if ( empty( $instance_id ) || empty( $method_id ) ) {
                                continue;
                            }
                            
                            $rate_id = $method_id . ':' . $instance_id;
                            
                            $method_title = __( 'Default Zone', 'bg8-one-page-checkout' ) . ' - ';
                            if ( method_exists( $method, 'get_title' ) ) {
                                $method_title .= $method->get_title();
                            } else if ( isset( $method->title ) ) {
                                $method_title .= $method->title;
                            } else {
                                $method_title .= $method_id;
                            }
                            
                            $methods[ $rate_id ] = $method_title;
                        }
                    }
                }
            }
        } catch ( Exception $e ) {
            // Log the error but don't crash
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'BG8 One Page Checkout: Error getting shipping methods - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            // Catch PHP 7+ errors too
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'BG8 One Page Checkout: Fatal error getting shipping methods - ' . $e->getMessage() );
            }
        }
        
        return $methods;
    }

    /**
     * Admin page HTML
     */
    public static function admin_page() {
        ?>
        <div class="wrap bg8-admin-page">
            <div class="bg8-admin-wrapper">
                <div class="bg8-admin-header">
                    <h1 class="bg8-admin-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
                </div>
                
                <div class="bg8-admin-content">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields( self::OPTION_GROUP );
                        do_settings_sections( 'bg8-one-page-checkout' );
                        ?>
                        <button type="submit" class="bg8-button-primary">Save Changes</button>
                        <button type="button" class="bg8-button-secondary" onclick="resetToDefaults()">Reset to Defaults</button>
                    </form>

                    <div class="bg8-preview-section">
                        <div class="bg8-preview-title"><?php esc_html_e( 'Preview', 'bg8-one-page-checkout' ); ?></div>
                        <p><?php esc_html_e( 'Visit your checkout page to see the changes in action.', 'bg8-one-page-checkout' ); ?></p>
                        <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'checkout' ) ); ?>" class="bg8-button-secondary" target="_blank">
                                <?php esc_html_e( 'View Checkout Page', 'bg8-one-page-checkout' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public static function enqueue_admin_assets( $hook ) {
        if ( 'woocommerce_page_bg8-one-page-checkout' !== $hook ) {
            return;
        }

        // Register and enqueue admin stylesheet with inline styles
        wp_register_style( 'bg8opc-admin-style', '' );
        wp_enqueue_style( 'bg8opc-admin-style' );
        
        // Add custom admin styling
        wp_add_inline_style( 'bg8opc-admin-style', '
            /* Hide admin notices on our page */
            .bg8-admin-page .notice {
                display: none !important;
            }
            
            .bg8-admin-wrapper {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                overflow: hidden;
                margin-top: 20px;
            }
            .bg8-admin-header {
                background: #23282d;
                color: white;
                padding: 20px;
                border-radius: 8px 8px 0 0;
            }
            .bg8-admin-header * {
                color: white !important;
            }
            .bg8-admin-title {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
                color: white !important;
            }
            .bg8-admin-content {
                padding: 30px;
            }
            .bg8-section-title {
                font-size: 18px;
                font-weight: bold;
                color: #23282d;
                margin: 30px 0 15px 0;
                padding-bottom: 10px;
                border-bottom: 2px solid #d4127c;
            }
            .bg8-section-title:first-child {
                margin-top: 0;
            }
            .bg8-form-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            .bg8-form-table th {
                text-align: left;
                padding: 15px 0;
                font-weight: 600;
                color: #23282d;
                border-bottom: 1px solid #e1e1e1;
                width: 200px;
            }
            .bg8-form-table td {
                padding: 15px 0;
                border-bottom: 1px solid #e1e1e1;
            }
            .bg8-color-picker {
                width: 60px;
                height: 40px;
                border: 2px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
            }
            .bg8-text-input,
            .bg8-textarea-input,
            .bg8-select {
                width: 300px;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            .bg8-textarea-input {
                width: 100%;
                max-width: 600px;
                resize: vertical;
            }
            .bg8-description {
                font-size: 12px;
                color: #666;
                margin-top: 5px;
            }
            .bg8-checkbox {
                margin-right: 8px;
            }
            .bg8-button-primary {
                background: #d4127c;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 4px;
                font-size: 14px;
                font-weight: bold;
                cursor: pointer;
            }
            .bg8-button-primary:hover {
                background: #b80e6a;
            }
            .bg8-preview-section {
                background: #f9f9f9;
                padding: 20px;
                border-radius: 8px;
                margin-top: 30px;
            }
            .bg8-preview-title {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .bg8-button-secondary {
                background: #f1f1f1;
                color: #23282d;
                border: 1px solid #ddd;
                padding: 8px 16px;
                border-radius: 4px;
                font-size: 12px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
            }
        ' );
        
        // Native HTML5 color picker - no JavaScript needed
        
        // Register and enqueue admin script
        wp_register_script( 'bg8opc-admin-script', '', array(), BG8OPC_VERSION, true );
        wp_enqueue_script( 'bg8opc-admin-script' );
        
        // Add reset to defaults functionality
        wp_add_inline_script( 'bg8opc-admin-script', '
            function resetToDefaults() {
                if (confirm("Are you sure you want to reset all settings to their default values? This cannot be undone.")) {
                    // Reset color fields
                    document.getElementById("brand_color").value = "#d4127c";
                    document.getElementById("primary_color").value = "#0073aa";
                    document.getElementById("success_color").value = "#00a32a";
                    document.getElementById("header_text_color").value = "#ffffff";
                    
                    // Reset text fields
                    document.getElementById("step_1_label").value = "Your Details";
                    document.getElementById("step_1_heading").value = "Enter your billing information";
                    document.getElementById("step_2_label").value = "Recipient";
                    document.getElementById("step_2_heading").value = "Shipping information";
                    document.getElementById("step_3_label").value = "Confirm";
                    document.getElementById("step_3_heading").value = "Review your order";
                    document.getElementById("checkout_title").value = "Checkout";
                    document.getElementById("checkout_description").value = "Complete your purchase in 3 simple steps";
                    
                    // Reset checkbox
                    document.getElementById("pickup_delivery_first").checked = false;
                    
                    // Reset tab order
                    document.getElementById("tab_order").value = "delivery,billing,shipping,payment";
                    
                    // Reset pickup/delivery customization
                    document.getElementById("pickup_delivery_heading").value = "How would you like to receive your order?";
                    document.getElementById("pickup_delivery_description").value = "";
                    document.getElementById("pickup_delivery_icon").value = "";
                    
                    alert("Settings have been reset to defaults. Click Save Changes to apply them.");
                }
            }
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
