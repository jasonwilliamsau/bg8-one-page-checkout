# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-10-23

## [1.1.0] - 2025-10-23

### Added
- TBD

### Changed
- TBD

### Fixed
- TBD

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