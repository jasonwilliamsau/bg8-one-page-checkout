# BG8 One Page Checkout - Deployment Guide

This guide explains how to deploy and release the BG8 One Page Checkout WordPress plugin.

## 🚀 Quick Start

### Deploy Current Version
```bash
./deploy.sh
```

### Version Bump & Release
```bash
# Patch version (1.1.0 → 1.1.1)
./version-bump.sh patch

# Minor version (1.1.0 → 1.2.0)
./version-bump.sh minor

# Major version (1.1.0 → 2.0.0)
./version-bump.sh major
```

### Complete Release Process
```bash
# Automated release (recommended)
./release.sh patch   # 1.1.0 → 1.1.1
./release.sh minor   # 1.1.0 → 1.2.0
./release.sh major   # 1.1.0 → 2.0.0

# Or using NPM
npm run release
```

## 📦 Deployment Script (`deploy.sh`)

The deployment script creates a WordPress-ready ZIP file with proper structure and validation.

### Features
- ✅ **Version Detection**: Automatically reads version from plugin header
- ✅ **Structure Validation**: Ensures all required files are present
- ✅ **PHP Syntax Check**: Validates PHP syntax before packaging
- ✅ **Security**: Adds `index.php` files to prevent directory browsing
- ✅ **Clean Package**: Excludes development files and creates clean ZIP
- ✅ **Checksums**: Generates MD5 and SHA256 checksums
- ✅ **Release Notes**: Auto-generates release notes from CHANGELOG
- ✅ **Uninstall Script**: Includes proper uninstall.php for clean removal

### Generated Files
```
dist/
├── bg8-one-page-checkout-1.1.0.zip    # WordPress plugin ZIP
├── release-notes-1.1.0.md            # Release notes
└── checksums-1.1.0.txt               # File checksums
```

### ZIP Structure
```
bg8-one-page-checkout/
├── bg8-one-page-checkout.php         # Main plugin file
├── README.md                         # Documentation
├── CHANGELOG.md                      # Version history
├── LICENSE                           # GPL-2.0+ license
├── uninstall.php                     # Clean uninstall script
├── assets/
│   ├── css/checkout.css              # Frontend styles
│   ├── js/checkout.js                # Frontend scripts
│   └── screenshots/                  # Plugin screenshots
└── includes/
    ├── class-bg8-one-page-checkout.php # Main plugin class
    └── class-bg8-admin.php           # Admin functionality
```

## 🔄 Version Management

### Complete Release Automation (`release.sh`)
The `release.sh` script handles the entire release process automatically:

```bash
./release.sh patch   # 1.1.0 → 1.1.1
./release.sh minor   # 1.1.0 → 1.2.0
./release.sh major   # 1.1.0 → 2.0.0
```

**What it does:**
- ✅ Bumps version in plugin header and constant
- ✅ Updates CHANGELOG.md (converts [Unreleased] to version)
- ✅ Commits all changes with release message
- ✅ Creates git tag
- ✅ Pushes changes and tags to GitHub
- ✅ Runs deployment script
- ✅ Validates git repository state

### Manual Version Bump
```bash
# Update version in plugin header
sed -i 's/Version: 1.1.0/Version: 1.1.1/' bg8-one-page-checkout.php
sed -i "s/define( 'BG8_SC_VERSION', '1.1.0' );/define( 'BG8_SC_VERSION', '1.1.1' );/" bg8-one-page-checkout.php

# Update CHANGELOG.md
# Update README.md
```

### Automated Version Bump (`version-bump.sh`)
```bash
./version-bump.sh patch   # 1.1.0 → 1.1.1
./version-bump.sh minor   # 1.1.0 → 1.2.0
./version-bump.sh major   # 1.1.0 → 2.0.0
```

### NPM Scripts
```bash
npm run version:patch    # Bump patch version + git tag
npm run version:minor    # Bump minor version + git tag
npm run version:major    # Bump major version + git tag
npm run release         # Deploy + commit + push
npm run test           # Validate PHP syntax
```

## 🤖 Automated Releases (GitHub Actions)

The repository includes GitHub Actions workflow for automated releases:

### Trigger Release
```bash
# Create and push a tag
git tag v1.1.1
git push origin v1.1.1

# Or use GitHub UI: Actions → WordPress Plugin Release → Run workflow
```

### Workflow Features
- ✅ **PHP Validation**: Checks syntax before release
- ✅ **Version Sync**: Updates plugin version from tag
- ✅ **Package Creation**: Runs deploy script
- ✅ **GitHub Release**: Creates release with ZIP file
- ✅ **Artifact Upload**: Saves build artifacts

## 📋 Release Checklist

### Before Release
- [ ] Update CHANGELOG.md with new features/fixes
- [ ] Test plugin on WordPress site
- [ ] Validate PHP syntax: `npm run test`
- [ ] Run deployment: `./deploy.sh`
- [ ] Test ZIP installation

### Release Process (Automated)
- [ ] Run: `./release.sh [patch|minor|major]`
- [ ] Script handles everything automatically

### Release Process (Manual)
- [ ] Bump version: `./version-bump.sh [patch|minor|major]`
- [ ] Review changes: `git diff`
- [ ] Commit changes: `git add . && git commit -m "Release v1.1.1"`
- [ ] Create tag: `git tag v1.1.1`
- [ ] Push changes: `git push && git push --tags`
- [ ] Deploy: `./deploy.sh`

### Post-Release
- [ ] Upload to WordPress.org plugin directory
- [ ] Create GitHub release
- [ ] Update documentation
- [ ] Announce release

## 🛠️ Development Commands

```bash
# Validate plugin
npm run test

# Deploy without version bump
./deploy.sh

# Full release process
npm run release

# Check plugin structure
unzip -l dist/bg8-one-page-checkout-*.zip

# Install via WP-CLI
wp plugin install dist/bg8-one-page-checkout-*.zip --activate
```

## 📁 File Structure

```
bg8-one-page-checkout/
├── .github/workflows/release.yml    # GitHub Actions
├── .gitignore                       # Git ignore rules
├── CHANGELOG.md                     # Version history
├── LICENSE                          # GPL-2.0+ license
├── README.md                        # Documentation
├── bg8-one-page-checkout.php        # Main plugin file
├── deploy.sh                        # Deployment script
├── version-bump.sh                  # Version bump script
├── release.sh                       # Complete release automation
├── package.json                     # NPM configuration
├── assets/
│   ├── css/checkout.css
│   ├── js/checkout.js
│   └── screenshots/
├── includes/
│   ├── class-bg8-one-page-checkout.php
│   └── class-bg8-admin.php
└── dist/                            # Generated files
    ├── *.zip
    ├── *.md
    └── *.txt
```

## 🔧 Troubleshooting

### Common Issues

**PHP Syntax Errors**
```bash
php -l bg8-one-page-checkout.php
php -l includes/class-bg8-one-page-checkout.php
php -l includes/class-bg8-admin.php
```

**Missing Files**
```bash
# Check required files exist
ls -la bg8-one-page-checkout.php README.md CHANGELOG.md LICENSE
ls -la assets/css/checkout.css assets/js/checkout.js
ls -la includes/class-bg8-one-page-checkout.php includes/class-bg8-admin.php
```

**ZIP Installation Issues**
```bash
# Test ZIP structure
unzip -t dist/bg8-one-page-checkout-*.zip

# Check file permissions
unzip -l dist/bg8-one-page-checkout-*.zip
```

### Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/jasonwilliamsau/bg8-one-page-checkout/issues)
- **Documentation**: [Plugin documentation](https://github.com/jasonwilliamsau/bg8-one-page-checkout/wiki)
- **Email**: [support@blackgate.com.au](mailto:support@blackgate.com.au)
