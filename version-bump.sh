#!/bin/bash

# BG8 One Page Checkout - Version Bump Script
# Usage: ./version-bump.sh [patch|minor|major]

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

TYPE=${1:-patch}

echo -e "${BLUE}🔄 BG8 One Page Checkout - Version Bump${NC}"
echo -e "${BLUE}=====================================${NC}"

# Get current version from plugin file (source of truth)
CURRENT_VERSION=$(grep "Version:" bg8-one-page-checkout.php | sed 's/.*Version: *//' | tr -d ' ')
echo -e "${YELLOW}Current version: ${CURRENT_VERSION}${NC}"

# Parse version components
IFS='.' read -ra VERSION_PARTS <<< "$CURRENT_VERSION"
MAJOR=${VERSION_PARTS[0]}
MINOR=${VERSION_PARTS[1]}
PATCH=${VERSION_PARTS[2]}

# Bump version based on type
case "$TYPE" in
    major)
        MAJOR=$((MAJOR + 1))
        MINOR=0
        PATCH=0
        ;;
    minor)
        MINOR=$((MINOR + 1))
        PATCH=0
        ;;
    patch)
        PATCH=$((PATCH + 1))
        ;;
esac

NEW_VERSION="${MAJOR}.${MINOR}.${PATCH}"
echo -e "${GREEN}New version: ${NEW_VERSION}${NC}"

# Update plugin files with new version
echo -e "${BLUE}Updating plugin files...${NC}"
sed -i.bak "s/Version: .*/Version: ${NEW_VERSION}/" bg8-one-page-checkout.php
sed -i.bak "s/define( 'BG8OPC_VERSION', .*/define( 'BG8OPC_VERSION', '${NEW_VERSION}' );/" bg8-one-page-checkout.php
rm -f bg8-one-page-checkout.php.bak

# Update package.json if it exists
if [ -f "package.json" ]; then
    echo -e "${BLUE}Updating package.json...${NC}"
    # Use node to update package.json (more reliable than sed for JSON)
    if command -v node >/dev/null 2>&1; then
        node -e "const fs = require('fs'); const pkg = JSON.parse(fs.readFileSync('package.json')); pkg.version = '${NEW_VERSION}'; fs.writeFileSync('package.json', JSON.stringify(pkg, null, 2) + '\n');"
    else
        # Fallback to sed if node is not available
        sed -i.bak "s/\"version\": \".*\"/\"version\": \"${NEW_VERSION}\"/" package.json
        rm -f package.json.bak
    fi
fi

# Update CHANGELOG.md
echo -e "${BLUE}Updating CHANGELOG.md...${NC}"
sed -i.bak "s/## \[Unreleased\]/## \[${NEW_VERSION}\] - $(date +%Y-%m-%d)/" CHANGELOG.md
sed -i.bak "/^## \[${NEW_VERSION}\] - $(date +%Y-%m-%d)/a\\
\\
## [Unreleased]\\
\\
### Added\\
- TBD\\
\\
### Changed\\
- TBD\\
\\
### Fixed\\
- TBD\\
" CHANGELOG.md

# Update README.md version
echo -e "${BLUE}Updating README.md...${NC}"
sed -i.bak "s/\*\*Version\*\*: .*/\*\*Version\*\*: ${NEW_VERSION}/" README.md

# Update readme.txt stable tag
echo -e "${BLUE}Updating readme.txt stable tag...${NC}"
sed -i.bak "s/Stable tag: .*/Stable tag: ${NEW_VERSION}/" readme.txt

# Verify updates
echo -e "${BLUE}Verifying version updates...${NC}"
if ! grep -q "Stable tag: ${NEW_VERSION}" readme.txt; then
    echo -e "${YELLOW}⚠️  Warning: readme.txt stable tag may not have been updated correctly${NC}"
fi
if ! grep -q "\*\*Version\*\*: ${NEW_VERSION}" README.md; then
    echo -e "${YELLOW}⚠️  Warning: README.md version may not have been updated correctly${NC}"
fi

# Clean up backup files
rm -f CHANGELOG.md.bak README.md.bak readme.txt.bak

echo -e "${GREEN}✅ Version bumped successfully to ${NEW_VERSION}${NC}"
echo ""
echo -e "${BLUE}📋 Next steps:${NC}"
echo -e "   1. Review the changes: git diff"
echo -e "   2. Commit changes: git add . && git commit -m \"Bump version to ${NEW_VERSION}\""
echo -e "   3. Create tag: git tag v${NEW_VERSION}"
echo -e "   4. Push changes: git push && git push --tags"
echo -e "   5. Deploy: ./deploy.sh"
echo ""
echo -e "${BLUE}🚀 Or use: npm run release${NC}"
