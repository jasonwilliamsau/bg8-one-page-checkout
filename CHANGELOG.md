# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.1] - 2025-11-06

## [Unreleased]

### Added
- TBD

### Changed
- TBD

### Fixed
- TBD

### Fixed
- Critical bug fix: Loader no longer persists on order-received and order-pay pages
- Plugin assets now only load on main checkout page, excluding order completion pages
- Improved page detection using `is_wc_endpoint_url()` for better WooCommerce compatibility
- Fixed infinite loader blocking user from viewing order confirmation after payment
- Fixed infinite loader on failed order payment retry pages

## [1.2.2] - 2025-10-27

## [1.2.2] - 2025-10-27

### Added
- TBD

### Changed
- TBD

### Fixed
- TBD

### Fixed
- Improved pickup/delivery UX:
  - Changed pickup icon to vase of flowers 💐
  - Pre-select Delivery option by default
  - Right-align Continue button on first step
  - Hide Recipient step when Pickup is selected
  - Update step numbers to reflect hidden steps
  - Fix Back button to navigate to previous visible step
  - Fix Continue button to skip hidden steps
  - Improve step skipping logic for pickup vs delivery

## [1.2.1] - 2025-10-27

### Fixed
- Bootstrap/jQuery console errors in admin page
- Missing color input fields in admin settings  
- Admin asset enqueue now properly registers styles and scripts

## [1.2.1] - 2025-10-27

### Added
- TBD

### Changed
- TBD

### Fixed
- TBD

### Fixed
- Bootstrap/jQuery console errors in admin page
- Missing color input fields in admin settings  
- Admin asset enqueue now properly registers styles and scripts

## [1.2.0] - 2025-10-27

### Added
- Pickup / Delivery First option in admin settings
- Customers can choose between pickup or delivery before entering any information
- Pickup option only requires billing information
- Delivery option requires both billing and shipping information
- Beautiful card-based selection interface for pickup/delivery choice
- Automatic step skipping based on selection
- Mobile-responsive pickup/delivery selection buttons

### Changed
- Enhanced checkout flow to accommodate pickup/delivery selection
- Improved UX by collecting delivery method preference early
- Better support for businesses offering both pickup and delivery options

## [1.1.11] - 2024-01-15

### Added
- Complete release automation script (`release.sh`)
- Enhanced deployment documentation
- Reset to Defaults button in admin settings

## [1.1.5] - 2025-10-23

### Added
- Comprehensive security hardening for WordPress.org submission
- Proper escaping for all user-facing outputs
- Sanitization of all user inputs including $_GET parameters
- Nonce verification for admin form submissions (via WordPress Settings API)

### Changed
- All debug output now properly escaped with esc_html() and esc_html__()
- Page builder detection now sanitizes $_GET parameters
- Admin form descriptions now properly escaped

### Fixed
- Potential XSS vulnerabilities in debug output
- Unsanitized $_GET parameter access
- Unescaped admin form field descriptions
- WordPress.org security compliance issues

### Added
- Complete release automation script (`release.sh`)
- Enhanced deployment documentation
- Reset to Defaults button in admin settings

## [1.1.4] - 2024-01-15

### Added
- Complete release automation script (`release.sh`)
- Enhanced deployment documentation

## [1.1.0] - 2024-01-15

### Added
- Complete admin settings page under WooCommerce menu
- Configurable colors: Brand, Primary, Success, Header Text
- Customizable step labels and headings for all 3 steps
- Optional checkout header with title and description
- Support for additional fields (.woocommerce-additional-fields)
- Page builder compatibility (Oxygen, Elementor, Divi, Bricks, Beaver Builder)
- Native HTML5 color picker with manual hex input
- Debug helper for troubleshooting (BG8_SC_DEBUG constant)
- CSS variables with bg8- prefix to avoid plugin conflicts
- Comprehensive error handling and sanitization

### Changed
- Moved admin menu from Settings to WooCommerce → BG8 Checkout
- Enhanced CSS injection priority to ensure dynamic values override defaults
- Improved admin styling with dark header and clean interface
- Fixed JavaScript errors and undefined text issues
- Updated plugin header with WordPress.org metadata
- Enhanced code organization with dedicated admin and debug classes

### Fixed
- Primary color now properly applies to active step states
- Admin styling restored after menu location change
- Duplicate color picker interface resolved
- Hard-coded colors replaced with CSS variables
- Page builder editing experience improved (no loader interference)

## [1.0.0] - 2024-01-15

### Added
- Initial release
- 3-step checkout conversion (Your Details → Recipient → Confirm)
- Single-column, accessible stepper design
- Brand styling with CSS variables
- Pre-paint overlay to prevent FOUC
- Focus management on checkout load
- Virtual-only cart handling (skips Recipient step)
- Clickable next tab with validation
- Back button always available
- WooCommerce integration
- Asset enqueuing only on checkout pages

### Technical Details
- Requires WordPress 6.0+
- Requires PHP 7.4+
- Compatible with WooCommerce
- GPL-2.0+ licensed
- No core WordPress/WooCommerce modifications