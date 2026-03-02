# Changelog

## [4.0.0] - 2026-03-02

### Added
- Complete plugin restructure with modular architecture
- Separated code into logical files (database, admin, frontend, utilities, AJAX)
- Professional admin dashboard with statistics
- Login history page with filtering and export capabilities
- Email-based 2FA (Two-Factor Authentication)
- Session timeout functionality
- User approval system
- User role-based access control
- Login tracking with browser and device detection
- User profile management
- Password change functionality
- Password reset via email
- Page restriction for logged-out users
- Multiple shortcodes for login, profile, logout, etc.
- Custom branding options (logo, colors, text)
- Comprehensive documentation

### Changed
- Improved code organization and readability
- Better error handling and logging
- Enhanced email templates for 2FA
- More responsive admin dashboard
- Optimized database queries

### Fixed
- Fixed 2FA code expiration
- Improved login tracking accuracy
- Fixed email notification delivery
- Better form validation

### Security
- Added CSRF protection with nonces
- Improved input sanitization
- Better password handling
- Secure session management

## [3.0.0] - Previous Version

### Features
- Basic login/registration
- Basic admin panel
- Simple user management

---

## Installation & Upgrade

### For Version 4.0.0

1. Download the latest release
2. Deactivate the old plugin version if upgrading
3. Upload the `modern-auth-portal` folder to `/wp-content/plugins/`
4. Activate the plugin
5. Run setup from **Auth Portal > Settings**

### Database Migration

The plugin automatically handles database table creation. No manual migration needed.

---

## Version Support

- **Current Stable**: 4.0.0
- **PHP Required**: 7.2+
- **WordPress Required**: 5.0+

---

## Notes

For detailed feature information, please refer to [README.md](README.md)
