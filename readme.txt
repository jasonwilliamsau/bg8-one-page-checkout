=== BG8 One Page Checkout ===
Contributors: blackgate
Tags: woocommerce, checkout, ecommerce, accessibility
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Converts WooCommerce checkout into a 3-step, accessible stepper with brand styling and improved UX.

== Description ==

Transform your WooCommerce checkout experience with BG8 One Page Checkout. This plugin converts the standard multi-column checkout into a modern, accessible 3-step process that guides customers through their purchase with visual progress indicators and improved user experience.

= Key Features =

* **3-Step Checkout Process**: Your Details → Recipient → Confirm
* **Visual Progress Indicator**: Clear step-by-step navigation with completion states
* **Accessibility First**: ARIA labels, keyboard navigation, and screen reader support
* **Customizable Branding**: Admin panel to configure colors, labels, and headers
* **Page Builder Compatible**: Works seamlessly with Oxygen, Elementor, Divi, Bricks, and Beaver Builder
* **Performance Optimized**: Pre-paint overlay prevents FOUC (Flash of Unstyled Content)
* **Mobile Responsive**: Optimized for all device sizes
* **WooCommerce Integration**: Full compatibility with WooCommerce features and extensions

= Admin Configuration =

Configure your checkout experience through WooCommerce → BG8 Checkout:

* **Color Settings**: Brand color, primary color, success color, and header text color
* **Tab Labels & Headings**: Customize step labels and section headings
* **Checkout Header**: Set custom title and description (or hide completely)
* **Reset to Defaults**: One-click reset to original settings

= Technical Details =

* Requires WordPress 6.0+
* Requires PHP 7.4+
* Compatible with WooCommerce
* GPL-2.0+ licensed
* No core WordPress/WooCommerce modifications
* Uses WordPress Settings API for secure configuration
* Follows WordPress coding standards and security best practices

= Accessibility Features =

* ARIA labels and roles for screen readers
* Keyboard navigation support
* Focus management on page load
* High contrast color options
* Semantic HTML structure

= Page Builder Compatibility =

The plugin automatically detects page builder environments and disables the loader overlay to provide a seamless editing experience in:
* Oxygen Builder
* Elementor
* Divi Builder
* Bricks Builder
* Beaver Builder

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/bg8-one-page-checkout` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Ensure WooCommerce is installed and activated
4. Configure settings at WooCommerce → BG8 Checkout
5. Visit your checkout page to see the new 3-step experience

== Frequently Asked Questions ==

= Does this plugin modify core WordPress or WooCommerce files? =

No. This plugin uses WordPress hooks and filters to modify the checkout experience without touching core files.

= Is this plugin compatible with page builders? =

Yes! The plugin automatically detects page builder environments and adjusts its behavior accordingly to provide a seamless editing experience.

= Can I customize the colors and labels? =

Absolutely! Use the admin panel at WooCommerce → BG8 Checkout to customize colors, step labels, headings, and the checkout header.

= Does this work with WooCommerce extensions? =

Yes, the plugin is designed to work with most WooCommerce extensions and themes. It preserves all WooCommerce functionality while improving the user experience.

= Is the plugin accessible? =

Yes! The plugin follows WordPress accessibility guidelines with ARIA labels, keyboard navigation, and screen reader support.

== Screenshots ==

1. Modern 3-step checkout with visual progress indicator
2. Admin settings panel for customization
3. Mobile-responsive design

== Changelog ==

= 1.1.6 =
* Comprehensive security hardening for WordPress.org submission
* Proper escaping for all user-facing outputs
* Sanitization of all user inputs including $_GET parameters
* Nonce verification for admin form submissions (via WordPress Settings API)
* All debug output now properly escaped with esc_html() and esc_html__()
* Page builder detection now sanitizes $_GET parameters
* Admin form descriptions now properly escaped
* Potential XSS vulnerabilities in debug output fixed
* Unsanitized $_GET parameter access fixed
* Unescaped admin form field descriptions fixed
* WordPress.org security compliance issues resolved

= 1.1.5 =
* Complete release automation script (`release.sh`)
* Enhanced deployment documentation
* Reset to Defaults button in admin settings
* Fixed version-bump.sh script to correctly update plugin files and package.json

= 1.1.4 =
* Complete release automation script (`release.sh`)
* Enhanced deployment documentation
* Page builder compatibility (Oxygen, Elementor, Divi, Bricks, Beaver Builder)
* Native HTML5 color picker with manual hex input
* CSS variables prefixed with `bg8-` to avoid conflicts
* Admin menu moved from Settings to WooCommerce → BG8 Checkout
* Enhanced CSS injection priority to ensure dynamic values override defaults
* Fixed JavaScript errors and undefined text issues
* Updated plugin header with WordPress.org metadata
* Enhanced code organization with dedicated admin and debug classes
* Removed WordPress color picker assets and JS initialization
* Updated GitHub Actions workflow to remove Node.js dependencies and add permissions
* Primary color now properly applies to active step states
* Admin styling restored after menu location change
* Duplicate color picker interface resolved
* Hard-coded colors replaced with CSS variables
* Page builder editing experience improved (no loader interference)
* GitHub Actions workflow permissions for creating releases

= 1.1.0 =
* Initial release
* 3-step checkout conversion (Your Details → Recipient → Confirm)
* Single-column, accessible stepper design
* Brand styling with CSS variables
* Pre-paint overlay to prevent FOUC
* Focus management on checkout load
* Virtual-only cart handling (skips Recipient step)
* Clickable next tab with validation
* Back button always available
* WooCommerce integration
* Asset enqueuing only on checkout pages

== Upgrade Notice ==

= 1.1.6 =
Security update with comprehensive hardening for WordPress.org compliance. All user inputs and outputs are now properly sanitized and escaped.

= 1.1.5 =
Bug fixes and improved version management.

= 1.1.4 =
Major update with page builder compatibility, enhanced admin interface, and improved security.

= 1.1.0 =
Initial release with 3-step checkout conversion and accessibility features.

== Support ==

For support, feature requests, or bug reports, please visit our [GitHub repository](https://github.com/jasonwilliamsau/bg8-one-page-checkout) or create an issue.

== Credits ==

Developed by [Blackgate](https://blackgate.com.au) - WordPress and WooCommerce specialists.

== License ==

This plugin is licensed under the GPL-2.0+ License.

== Privacy Policy ==

This plugin does not collect, store, or transmit any personal data. All configuration is stored locally in your WordPress database.
