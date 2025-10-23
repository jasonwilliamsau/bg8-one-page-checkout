# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Admin configuration page for colors and tab labels
- WordPress Settings API integration
- Custom CSS variable injection based on admin settings
- Color picker for brand, primary, and success colors
- Customizable step labels and headings

### Changed
- Enhanced plugin structure with proper admin/frontend separation
- Improved code organization with dedicated admin class

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
