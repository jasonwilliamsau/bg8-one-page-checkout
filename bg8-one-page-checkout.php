<?php
/**
 * Plugin Name: BG8 One Page Checkout
 * Plugin URI: https://github.com/jasonwilliamsau/bg8-one-page-checkout
 * Description: Converts WooCommerce checkout into a 3-step, single-column, accessible stepper with brand styling, pre-paint overlay, and improved UX. Includes admin configuration for colors and labels.
 * Version: 1.5.2
 * Author: Blackgate
 * Author URI: https://blackgate.com.au
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bg8-one-page-checkout
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * 
 * @package BG8OPC_OnePageCheckout
 * @version 1.1.0
 * @author Blackgate
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'BG8OPC_VERSION', '1.5.2' );
define( 'BG8OPC_DIR', plugin_dir_path( __FILE__ ) );
define( 'BG8OPC_URL', plugin_dir_url( __FILE__ ) );

// Bootstrap
require_once BG8OPC_DIR . 'includes/class-bg8-one-page-checkout.php';

// Load debug helper if debug mode is enabled
if ( defined( 'BG8OPC_DEBUG' ) && BG8OPC_DEBUG ) {
    require_once BG8OPC_DIR . 'includes/class-bg8-debug.php';
}

add_action( 'plugins_loaded', function() {
    \BG8OPC\OnePageCheckout\Plugin::init();
});
