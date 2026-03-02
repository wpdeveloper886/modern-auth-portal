# Modern Auth Portal - Complete Edition v4.0

A professional, feature-rich WordPress authentication system with login, registration, profile management, password reset, email 2FA, session timeout, and login history tracking.

## Features

✅ **Complete Authentication System**
- Professional login form with email/username support
- User registration with approval workflow
- Password reset functionality
- Password change option
- Profile management with avatar upload

✅ **Security Features**
- Email-based Two-Factor Authentication (2FA)
- Session timeout with automatic logout
- Login history tracking with IP address and device info
- User role-based access control
- CSRF protection with WordPress nonces

✅ **Admin Dashboard**
- Real-time login statistics
- Login history with filtering and search
- Browser and device statistics
- Recent activity overview
- User management and approval system

✅ **Customization**
- Custom branding (logo, colors, text)
- Page protection and restricted access
- Redirect after login
- Email notification configuration
- Role-based access control

✅ **Advanced Features**
- Login tracking (browser, device, IP address, time)
- Session timeout management
- User approval system
- Custom user roles
- Email notifications for 2FA

## Installation

1. Upload the `modern-auth-portal` folder to `/wp-content/plugins/`
2. Activate the plugin from WordPress admin panel
3. Navigate to **Auth Portal** > **Settings** to configure
4. Create a page and add the `[modern_auth_login]` shortcode

## Available Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[modern_auth_login]` | Main login form with registration and password reset |
| `[modern_auth_profile]` | User profile editing form |
| `[modern_auth_change_password]` | Password change form (logged-in users only) |
| `[modern_auth_reset_password]` | Password reset form |
| `[modern_auth_logout]` | Logout button |
| `[modern_auth_welcome]` | Welcome message (logged-in users only) |
| `[modern_auth_status]` | Login status badge |

## Settings

Access plugin settings from WordPress admin: **Auth Portal** > **Settings**

### Branding
- **Logo**: Upload custom logo for login page
- **Brand Name**: Site/brand name
- **Tagline**: Subtitle for login page
- **Colors**: Customize primary and secondary colors

### General Settings
- **Enable Registration**: Allow new user registrations
- **Require Approval**: Admin approval before access
- **Enable 2FA**: Email verification codes on login
- **Session Timeout**: Automatic logout after inactivity
- **Redirect URL**: Where to send users after login
- **Protected Pages**: Restrict page access to logged-in users
- **Allowed Roles**: Which user roles can access the portal

### Email Configuration

For 2FA emails to work properly, install one of these SMTP plugins:
- [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/)
- [Easy WP SMTP](https://wordpress.org/plugins/easy-wp-smtp/)

## File Structure

```
modern-auth-portal/
├── modern-auth-portal.php      # Main plugin file
├── README.md                   # This file
├── inc/
│   ├── functions-database.php  # Database setup
│   ├── functions-utils.php     # Utility functions
│   ├── functions-core.php      # Core functionality
│   ├── functions-ajax.php      # AJAX handlers
│   └── functions-admin.php     # Admin menu setup
├── admin/
│   ├── dashboard.php           # Dashboard page
│   ├── login-history.php       # Login history page
│   └── settings.php            # Settings page
├── public/
│   └── shortcodes.php          # Frontend shortcodes
├── assets/
│   ├── css/                    # CSS files
│   └── js/                     # JavaScript files
└── languages/                  # Translation files
```

## Database Tables

The plugin creates one custom table:
- `wp_map_login_history` - Stores login records with IP, browser, device info

## Security Notes

- All inputs are sanitized using WordPress sanitization functions
- AJAX requests are protected with nonces
- 2FA codes expire after 5 minutes
- Passwords are hashed with WordPress password functions
- Login history can be cleared from admin panel

## Requirements

- WordPress 5.0+
- PHP 7.2+
- MySQL 5.7+

## Author

**Kamran Rasool**

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Changelog

### Version 4.0.0
- Complete rewrite with proper plugin structure
- Organized code into modular files
- Improved admin dashboard
- Enhanced security features
- Better email templates for 2FA

## Support

For issues or feature requests, please contact the author.

## Credits

Built with WordPress best practices and security standards in mind.
