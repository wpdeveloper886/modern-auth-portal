# Plugin Structure Overview

## Complete Directory Structure

```
modern-auth-portal/
│
├── modern-auth-portal.php          (Main plugin entry point)
├── README.md                       (Plugin documentation)
├── CHANGELOG.md                    (Version history)
├── CONTRIBUTING.md                 (Contribution guidelines)
├── composer.json                   (PHP dependencies)
├── .gitignore                      (Git ignore rules)
│
├── inc/                            (Core plugin functions)
│   ├── functions-database.php      (DB setup & activation hooks)
│   ├── functions-utils.php         (Utility functions & styles)
│   ├── functions-core.php          (Core functionality)
│   ├── functions-ajax.php          (AJAX handlers)
│   └── functions-admin.php         (Admin menu setup)
│
├── admin/                          (Admin pages & features)
│   ├── dashboard.php               (Dashboard overview)
│   ├── login-history.php           (Login history page)
│   └── settings.php                (Settings configuration)
│
├── public/                         (Frontend features)
│   └── shortcodes.php              (All shortcode implementations)
│
├── assets/                         (Static assets)
│   ├── css/                        (CSS files - reserved)
│   └── js/                         (JavaScript files - reserved)
│
└── languages/                      (Translation files - reserved)
```

## File Descriptions

### Core Files

#### `modern-auth-portal.php` (Main Plugin File)
- Plugin header with metadata
- Activation/Deactivation hooks
- Constants definition
- File includes for all modules

#### `inc/functions-database.php` (Database)
- Plugin activation setup
- Database table creation
- Default options setup
- Database table verification

#### `inc/functions-utils.php` (Utilities)
- Client IP detection
- User agent parsing (browser/device)
- 2FA code generation
- Email sending function
- Base CSS styles for shortcodes

#### `inc/functions-core.php` (Core)
- Session timeout management
- Login history tracking
- Page restriction functionality
- User approval system
- Last login column in user list

#### `inc/functions-ajax.php` (AJAX)
- Login handler
- 2FA verification handler
- Registration handler
- Profile update handler
- Password change handler
- Password reset handler
- Email notice dismissal
- Login history deletion

#### `inc/functions-admin.php` (Admin Setup)
- Admin menu registration
- Submenu items

### Admin Pages

#### `admin/dashboard.php`
- Overview statistics
- Recent login activity
- Browser statistics
- Device statistics
- Login analytics

#### `admin/login-history.php`
- Complete login history table
- Filtering (user, date range)
- Pagination
- Individual/batch deletion
- Export capabilities

#### `admin/settings.php`
- Branding configuration (logo, name, colors)
- General settings (registration, 2FA, timeout)
- Email configuration notice
- Protected pages setup
- User role management
- Redirect URL configuration
- Text customization

### Frontend Features

#### `public/shortcodes.php`
- `[modern_auth_login]` - Login form with 2FA & password reset
- `[modern_auth_profile]` - Profile editing
- `[modern_auth_change_password]` - Password change
- `[modern_auth_reset_password]` - Password reset
- `[modern_auth_logout]` - Logout button
- `[modern_auth_welcome]` - Welcome message
- `[modern_auth_status]` - Login status badge

## Key Features by Location

### Authentication (`inc/functions-ajax.php`)
- Email/username login
- 2FA email verification
- User registration
- Password reset
- Password change

### Security (`inc/functions-core.php`)
- Session timeout
- Page protection
- User approval system
- Login tracking

### Admin Interface (`admin/*.php`)
- Dashboard statistics
- Login history management
- Settings configuration
- User approval management

### User Interface (`public/shortcodes.php`)
- Responsive login form
- Profile management
- Password management
- Status display

## Database Schema

### Custom Table: `wp_map_login_history`

```sql
CREATE TABLE wp_map_login_history (
    id                  bigint(20) PRIMARY KEY AUTO_INCREMENT,
    user_id             bigint(20) NOT NULL,
    login_time          datetime NOT NULL,
    ip_address          varchar(100) NOT NULL,
    user_agent          text NOT NULL,
    browser             varchar(100),
    device              varchar(100),
    KEY user_id         (user_id),
    KEY login_time      (login_time)
);
```

## Options (WordPress Settings)

```php
// Configuration
map_enable_registration        // Enable/disable registration
map_require_approval           // Require admin approval
map_redirect_after_login       // URL after login
map_primary_color              // Primary color (#D4FF00)
map_secondary_color            // Secondary color (#000000)
map_restricted_pages           // Array of page IDs
map_logo_url                   // Custom logo URL
map_brand_name                 // Brand name
map_tagline                    // Login page tagline
map_enable_2fa                 // Enable 2FA
map_terms_text                 // Terms text on login
map_access_notice              // Access notice text
map_allowed_roles              // Array of allowed roles
map_session_timeout            // Enable session timeout
map_session_timeout_minutes    // Timeout duration
map_show_email_notice          // Show email config notice
```

## User Meta (User Metadata)

```php
map_approved              // User approval status
map_avatar                // Custom avatar URL
map_last_login            // Last login timestamp
map_last_activity         // Last activity timestamp
```

## Hooks & Actions

### Activation Hooks
- `register_activation_hook` - Plugin activation
- `register_deactivation_hook` - Plugin deactivation

### WordPress Hooks
- `init` - Initialization (color check, DB table, session)
- `admin_menu` - Admin menu registration
- `wp_login` - Manual login tracking
- `template_redirect` - Page protection
- `manage_users_columns` - Add last login column
- `manage_users_custom_column` - Display last login

### AJAX Hooks
- `wp_ajax_nopriv_map_login` - Public login
- `wp_ajax_nopriv_map_verify_2fa` - Public 2FA
- `wp_ajax_nopriv_map_register` - Public registration
- `wp_ajax_nopriv_map_reset_password` - Public password reset
- `wp_ajax_map_update_profile` - Update profile
- `wp_ajax_map_change_password` - Change password
- `wp_ajax_map_dismiss_email_notice` - Dismiss notice
- `wp_ajax_map_delete_login_history` - Delete history

## Security Features

1. **Nonce Verification** - All AJAX and forms use WordPress nonces
2. **Input Sanitization** - All inputs sanitized with WordPress functions
3. **Output Escaping** - All outputs properly escaped
4. **Password Hashing** - WordPress password functions used
5. **Role-Based Access** - Role checking for admin pages
6. **2FA Protection** - Email verification codes (5-minute expiry)
7. **Session Management** - Configurable timeout

## Performance Considerations

1. **Database Indexing** - Login history table indexed by user_id and login_time
2. **Lazy Loading** - Assets only loaded when shortcodes used
3. **Transient Cache** - 2FA codes stored as transients
4. **Query Optimization** - Limited results pagination on history page

## Next Steps for Development

1. Add unit tests (`tests/` folder)
2. Create i18n translation files (`languages/` folder)
3. Add CSS/JS assets (`assets/` folder)
4. Implement admin notice system
5. Add backup/export functionality
6. Create user documentation

---

**This structure is production-ready and follows WordPress best practices!**
