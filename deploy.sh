#!/bin/bash

# BG8 One Page Checkout - WordPress Plugin Deploy Script
# This script creates a deployable ZIP file for WordPress installation

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PLUGIN_NAME="bg8-one-page-checkout"
PLUGIN_SLUG="bg8-one-page-checkout"
VERSION=$(grep "Version:" bg8-one-page-checkout.php | sed 's/.*Version: *//' | tr -d ' ')
BUILD_DIR="build"
DIST_DIR="dist"

echo -e "${BLUE}🚀 BG8 One Page Checkout - WordPress Plugin Deploy Script${NC}"
echo -e "${BLUE}======================================================${NC}"
echo ""

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "bg8-one-page-checkout.php" ]; then
    print_error "bg8-one-page-checkout.php not found. Please run this script from the plugin root directory."
    exit 1
fi

# Display current version
print_info "Current plugin version: ${VERSION}"

# Clean previous builds
print_info "Cleaning previous builds..."
rm -rf "${BUILD_DIR}" "${DIST_DIR}"
mkdir -p "${BUILD_DIR}" "${DIST_DIR}"

# Create plugin directory structure
PLUGIN_BUILD_DIR="${BUILD_DIR}/${PLUGIN_SLUG}"
mkdir -p "${PLUGIN_BUILD_DIR}"

print_info "Copying plugin files..."

# Copy main plugin files
cp bg8-one-page-checkout.php "${PLUGIN_BUILD_DIR}/"
cp README.md "${PLUGIN_BUILD_DIR}/"
cp readme.txt "${PLUGIN_BUILD_DIR}/"
cp CHANGELOG.md "${PLUGIN_BUILD_DIR}/"
cp LICENSE "${PLUGIN_BUILD_DIR}/"

# Copy directories
cp -r assets "${PLUGIN_BUILD_DIR}/"
cp -r includes "${PLUGIN_BUILD_DIR}/"
cp -r languages "${PLUGIN_BUILD_DIR}/"

# Create .gitignore for the build (exclude development files)
cat > "${PLUGIN_BUILD_DIR}/.gitignore" << EOF
# WordPress
wp-config.php
wp-content/uploads/
wp-content/blogs.dir/
wp-content/upgrade/
wp-content/backup-db/
wp-content/advanced-cache.php
wp-content/wp-cache-config.php
wp-content/cache/
wp-content/cache/supercache/

# Plugin specific
*.log
*.tmp
.DS_Store
Thumbs.db

# IDE
.vscode/
.idea/
*.swp
*.swo
*~

# Node modules (if using build tools)
node_modules/
npm-debug.log
yarn-error.log

# Build artifacts
dist/
build/
*.zip
*.tar.gz

# Environment files
.env
.env.local
.env.production

# Composer
vendor/
composer.lock
EOF

# Create uninstall.php for clean removal
cat > "${PLUGIN_BUILD_DIR}/uninstall.php" << EOF
<?php
/**
 * Uninstall script for BG8 One Page Checkout
 * 
 * This file is executed when the plugin is uninstalled (deleted) via WordPress admin.
 * It removes all plugin data and settings.
 * 
 * @package BG8_OnePageCheckout
 * @version ${VERSION}
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
delete_option('bg8_sc_options');

// Remove any transients
delete_transient('bg8_sc_version_check');

// Clear any cached data
wp_cache_flush();

// Note: We don't remove user meta or post meta as it might be needed for other purposes
// If you need to remove specific data, add it here
EOF

# Create index.php files for security
find "${PLUGIN_BUILD_DIR}" -type d -exec sh -c 'echo "<?php\n// Silence is golden." > "$1/index.php"' _ {} \;

# Validate plugin structure
print_info "Validating plugin structure..."

# Check required files exist
REQUIRED_FILES=(
    "${PLUGIN_BUILD_DIR}/bg8-one-page-checkout.php"
    "${PLUGIN_BUILD_DIR}/README.md"
    "${PLUGIN_BUILD_DIR}/CHANGELOG.md"
    "${PLUGIN_BUILD_DIR}/LICENSE"
    "${PLUGIN_BUILD_DIR}/assets/css/checkout.css"
    "${PLUGIN_BUILD_DIR}/assets/js/checkout.js"
    "${PLUGIN_BUILD_DIR}/includes/class-bg8-one-page-checkout.php"
    "${PLUGIN_BUILD_DIR}/includes/class-bg8-admin.php"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        print_error "Required file missing: $file"
        exit 1
    fi
done

print_status "Plugin structure validation passed"

# Check PHP syntax
print_info "Checking PHP syntax..."
php -l "${PLUGIN_BUILD_DIR}/bg8-one-page-checkout.php" > /dev/null
php -l "${PLUGIN_BUILD_DIR}/includes/class-bg8-one-page-checkout.php" > /dev/null
php -l "${PLUGIN_BUILD_DIR}/includes/class-bg8-admin.php" > /dev/null
print_status "PHP syntax check passed"

# Create ZIP file
ZIP_NAME="${PLUGIN_SLUG}-${VERSION}.zip"
ZIP_PATH="${DIST_DIR}/${ZIP_NAME}"

print_info "Creating ZIP file: ${ZIP_NAME}"

cd "${BUILD_DIR}"
zip -r "../${ZIP_PATH}" "${PLUGIN_SLUG}" -x "*.DS_Store" "*/.*" > /dev/null
cd ..

# Verify ZIP was created
if [ ! -f "${ZIP_PATH}" ]; then
    print_error "Failed to create ZIP file"
    exit 1
fi

# Get file size
ZIP_SIZE=$(du -h "${ZIP_PATH}" | cut -f1)

print_status "ZIP file created successfully: ${ZIP_NAME} (${ZIP_SIZE})"

# Create release notes
RELEASE_NOTES_FILE="${DIST_DIR}/release-notes-${VERSION}.md"
cat > "${RELEASE_NOTES_FILE}" << EOF
# BG8 One Page Checkout v${VERSION} Release Notes

## Installation Instructions

1. Download the \`${ZIP_NAME}\` file
2. Go to your WordPress admin → Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Activate the plugin
5. Go to Settings → BG8 Checkout to configure colors and labels

## What's New in v${VERSION}

$(grep -A 20 "## \[Unreleased\]" CHANGELOG.md | grep -A 20 "### Added" | head -20)

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- WooCommerce plugin (any version)

## Support

- GitHub Issues: https://github.com/jasonwilliamsau/bg8-one-page-checkout/issues
- Documentation: https://github.com/jasonwilliamsau/bg8-one-page-checkout/wiki

## Changelog

See CHANGELOG.md for complete version history.
EOF

print_status "Release notes created: release-notes-${VERSION}.md"

# Create checksums
print_info "Generating checksums..."
CHECKSUM_FILE="${DIST_DIR}/checksums-${VERSION}.txt"
if command -v md5sum >/dev/null 2>&1; then
    md5sum "${ZIP_PATH}" > "${CHECKSUM_FILE}"
    sha256sum "${ZIP_PATH}" >> "${CHECKSUM_FILE}"
else
    # macOS fallback
    md5 -q "${ZIP_PATH}" | sed "s/^/$(basename "${ZIP_PATH}") /" > "${CHECKSUM_FILE}"
    shasum -a 256 "${ZIP_PATH}" >> "${CHECKSUM_FILE}"
fi
print_status "Checksums generated: checksums-${VERSION}.txt"

# Display summary
echo ""
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo -e "${GREEN}=====================================${NC}"
echo ""
echo -e "${BLUE}📦 Generated Files:${NC}"
echo -e "   • ${ZIP_NAME} (${ZIP_SIZE})"
echo -e "   • release-notes-${VERSION}.md"
echo -e "   • checksums-${VERSION}.txt"
echo ""
echo -e "${BLUE}📁 Location:${NC} ${DIST_DIR}/"
echo ""
echo -e "${BLUE}🚀 Next Steps:${NC}"
echo -e "   1. Test the ZIP file on a WordPress site"
echo -e "   2. Upload to WordPress.org plugin directory"
echo -e "   3. Create a GitHub release with the ZIP file"
echo ""
echo -e "${BLUE}📋 Installation Commands:${NC}"
echo -e "   # Install via WP-CLI:"
echo -e "   wp plugin install ${ZIP_PATH} --activate"
echo ""
echo -e "   # Manual installation:"
echo -e "   # Upload ${ZIP_NAME} via WordPress admin → Plugins → Add New → Upload Plugin"
echo ""

# Clean up build directory
rm -rf "${BUILD_DIR}"

print_status "Build cleanup completed"
print_status "Deployment ready! 🚀"
