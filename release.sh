#!/bin/bash

# BG8 One Page Checkout - Complete Release Script
# Handles version bump, changelog, commit, tag, push, and deploy

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    print_error "Not in a git repository"
    exit 1
fi

# Check if there are uncommitted changes
if ! git diff-index --quiet HEAD --; then
    print_error "You have uncommitted changes. Please commit or stash them first."
    exit 1
fi

# Check if we're on main branch
current_branch=$(git branch --show-current)
if [ "$current_branch" != "main" ]; then
    print_warning "You're not on the main branch (currently on: $current_branch)"
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Get release type
if [ -z "$1" ]; then
    echo "Usage: $0 [patch|minor|major]"
    echo ""
    echo "Examples:"
    echo "  $0 patch   # 1.1.0 → 1.1.1"
    echo "  $0 minor   # 1.1.0 → 1.2.0"
    echo "  $0 major   # 1.1.0 → 2.0.0"
    exit 1
fi

RELEASE_TYPE="$1"

# Validate release type
if [[ ! "$RELEASE_TYPE" =~ ^(patch|minor|major)$ ]]; then
    print_error "Invalid release type. Use: patch, minor, or major"
    exit 1
fi

print_status "🚀 Starting release process..."

# Get current version
current_version=$(grep "Version:" bg8-one-page-checkout.php | sed 's/.*Version: //' | sed 's/ .*//')
print_status "Current version: $current_version"

# Bump version using version-bump.sh
print_status "Bumping version ($RELEASE_TYPE)..."
./version-bump.sh "$RELEASE_TYPE"

# Get new version
new_version=$(grep "Version:" bg8-one-page-checkout.php | sed 's/.*Version: //' | sed 's/ .*//')
print_status "New version: $new_version"

# Update CHANGELOG.md if it has [Unreleased] section
if grep -q "## \[Unreleased\]" CHANGELOG.md; then
    print_status "Updating CHANGELOG.md..."
    sed -i.bak "s/## \[Unreleased\]/## [$new_version] - $(date +%Y-%m-%d)/" CHANGELOG.md
    rm CHANGELOG.md.bak
fi

# Stage all changes
print_status "Staging changes..."
git add .

# Commit changes
commit_message="Release v$new_version"
print_status "Committing changes: $commit_message"
git commit -m "$commit_message"

# Create tag
tag_name="v$new_version"
print_status "Creating tag: $tag_name"
git tag -a "$tag_name" -m "Release $tag_name"

# Push changes and tags
print_status "Pushing changes and tags..."
git push origin main
git push origin "$tag_name"

# Deploy
print_status "Deploying plugin..."
./deploy.sh

print_success "🎉 Release $tag_name completed successfully!"
print_status "📦 ZIP file: dist/bg8-one-page-checkout-$new_version.zip"
print_status "🏷️  Tag: $tag_name"
print_status "📝 Next steps:"
print_status "   • Upload to WordPress.org plugin directory"
print_status "   • Create GitHub release"
print_status "   • Announce release"
