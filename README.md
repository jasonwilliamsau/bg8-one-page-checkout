# BG8 One Page Checkout

A lightweight WooCommerce enhancement that converts the default checkout into a 3‑step, single‑column, accessible stepper with brand styling, pre‑paint overlay, and admin configuration options.

## Features

### Core Functionality
- **3-Step Process**: Your Details → Recipient → Confirm (contains shipping methods, totals, coupon, payment)
- **Smart Navigation**: Virtual-only carts automatically skip Recipient step
- **User-Friendly Controls**: Next tab is clickable (with validation), Back always available
- **Accessibility**: Focus management and keyboard navigation support
- **Performance**: Pre-paint overlay prevents flash-of-unstyled-content (FOUC)

### Admin Configuration
- **Color Customization**: Configure brand, primary, and success colors via color picker
- **Label Customization**: Customize step labels and headings for each checkout step
- **Live Preview**: See changes immediately on your checkout page
- **WordPress Settings API**: Secure, standardized configuration interface

### Technical Features
- **No Core Edits**: Enqueues assets only on `is_checkout()` pages
- **CSS Variables**: Dynamic color injection based on admin settings
- **Namespace Organization**: Clean, maintainable code structure
- **WordPress Standards**: Follows WordPress coding standards and best practices

## Installation

### From WordPress.org (Coming Soon)
1. Go to **Plugins → Add New**
2. Search for "BG8 One Page Checkout"
3. Click **Install Now** and **Activate**

### Manual Installation
1. Download the plugin ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Upload the ZIP file and click **Install Now**
4. Click **Activate**

### Via FTP
1. Upload the `bg8-one-page-checkout` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress

## Configuration

### Admin Settings
1. Go to **Settings → BG8 Checkout**
2. Configure your colors using the color pickers
3. Customize step labels and headings
4. Click **Save Changes**
5. Visit your checkout page to see the changes

### Color Settings
- **Brand Color**: Primary brand color (default: `#d4127c`)
- **Primary Color**: Secondary accent color (default: `#0073aa`)
- **Success Color**: Success/confirmation color (default: `#00a32a`)

### Label Customization
- **Step 1**: "Your Details" / "Enter your billing information"
- **Step 2**: "Recipient" / "Shipping information"
- **Step 3**: "Confirm" / "Review your order"

## Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 7.4 or higher
- **WooCommerce**: Any version (plugin automatically detects checkout pages)
- **Browser**: Modern browsers with CSS Grid support

## Development

### File Structure
```
bg8-one-page-checkout/
├── assets/
│   ├── css/
│   │   └── checkout.css
│   └── js/
│       └── checkout.js
├── includes/
│   ├── class-bg8-one-page-checkout.php
│   └── class-bg8-admin.php
├── bg8-one-page-checkout.php
├── README.md
└── CHANGELOG.md
```

### Hooks and Filters
The plugin uses WordPress standards and can be extended using:
- WordPress Settings API for configuration
- Standard WordPress hooks (`wp_head`, `wp_enqueue_scripts`)
- WooCommerce checkout hooks

### Customization
- **Colors**: Use admin settings or override CSS variables
- **Labels**: Configure via admin interface
- **Styling**: Override CSS in your theme or child theme
- **Behavior**: Extend JavaScript functionality in `checkout.js`

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Support

- **Documentation**: [GitHub Wiki](https://github.com/jasonwilliamsau/bg8-one-page-checkout/wiki)
- **Issues**: [GitHub Issues](https://github.com/jasonwilliamsau/bg8-one-page-checkout/issues)
- **Email**: [support@blackgate.com.au](mailto:support@blackgate.com.au)

## License

This plugin is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.

## Credits

Developed by [Blackgate](https://blackgate.com.au) - WordPress and WooCommerce specialists.

---

**Version**: 1.3.2
**Last Updated**: January 2024  
**Tested up to**: WordPress 6.4
