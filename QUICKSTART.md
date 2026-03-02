# Quick Start Guide

## Installation Steps

### 1. Download & Install

1. Download or clone the `modern-auth-portal` folder
2. Upload to `/wp-content/plugins/`
3. Activate from WordPress admin: **Plugins > Modern Auth Portal**

### 2. Initial Configuration

1. Go to **Auth Portal > Settings**
2. Configure basic settings:
   - Enable/disable registration
   - Set redirect URL after login
   - Customize brand name and tagline
   - Upload logo

### 3. Create Login Page

1. Create a new WordPress page (e.g., "Login")
2. Add the shortcode: `[modern_auth_login]`
3. Publish the page
4. Copy the page URL to your login link

## Basic Shortcode Usage

### Login Form
```
[modern_auth_login]
```
Displays complete login page with:
- Email/username login
- Registration form
- Password reset
- 2FA verification

### User Profile Page
```
[modern_auth_profile]
```
Allows logged-in users to:
- Edit name and email
- Upload avatar
- Add bio

### Additional Shortcodes
```
[modern_auth_logout]              # Logout button
[modern_auth_welcome]             # Welcome message
[modern_auth_status]              # Login status badge
[modern_auth_change_password]     # Password change form
[modern_auth_reset_password]      # Password reset form
```

## Configuration Guide

### Enable 2FA (Email Verification)

1. Install SMTP plugin ([WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/))
2. Configure SMTP settings for your email provider
3. Go to **Auth Portal > Settings**
4. Check **Enable Email 2FA**
5. Save settings

### Restrict Pages

1. Go to **Auth Portal > Settings**
2. Find **Protected Pages** section
3. Select pages that require login
4. Save settings

### Customize Colors

1. Go to **Auth Portal > Settings**
2. Find **Colors** section
3. Adjust Primary and Secondary colors
4. Save settings

### User Roles & Access

1. Go to **Auth Portal > Settings**
2. Find **Allowed User Roles**
3. Check which roles can access the portal
4. Save settings

### Session Timeout

1. Go to **Auth Portal > Settings**
2. Enable **Session Timeout**
3. Set timeout duration (minutes)
4. Save settings

## Admin Features

### Dashboard
Access at: **Auth Portal > Dashboard**
- View login statistics
- See recent activity
- Browser/device analytics

### Login History
Access at: **Auth Portal > Login History**
- View all logins
- Filter by user or date
- Delete records
- See IP addresses, browsers, devices

### User Approval
1. Go to **Users** in WordPress admin
2. Edit a user assigned to "Dashboard User" role
3. Check **Approve portal access** under "Portal Access"
4. Save user

## Common Tasks

### Create New User Account
Users can self-register if enabled in settings. Alternatively:
1. Go to **Users > Add New**
2. Set role to "Dashboard User"
3. Check **Approve portal access** under Portal Access section

### View User's Login History
1. Go to **Auth Portal > Login History**
2. Filter by user in dropdown
3. View all their logins with details

### Require Email Verification
1. Install & configure SMTP plugin
2. Enable 2FA in settings
3. Users must verify their email on login

### Redirect Users After Login
1. Go to **Auth Portal > Settings**
2. Set **Redirect After Login** URL
3. Users automatically redirected after login

### Custom Login Page Text
1. Go to **Auth Portal > Settings**
2. Customize:
   - **Brand Name** - Site name
   - **Tagline** - Subtitle
   - **Terms & Conditions Text** - Below form
   - **Access Request Notice** - Below terms

## Troubleshooting

### 2FA Emails Not Sending
1. Check SMTP plugin is installed and configured
2. Verify email settings in SMTP plugin
3. Check spam/junk folder
4. Enable logging in `wp-config.php`:
```php
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG', true );
```

### Users Can't Login
1. Check **Allowed User Roles** in settings
2. For Dashboard User role, verify **Approve portal access** is checked
3. Check if registration is enabled

### Logo Not Showing
1. Verify file upload completed
2. Check image URL is accessible
3. Try uploading again

### Colors Not Changing
1. Clear browser cache
2. Clear WordPress cache plugins
3. Re-save settings
4. Check CSS is not overridden by theme

## Next Steps

1. **Customize Branding**: Add logo and colors
2. **Setup Email**: Configure SMTP for 2FA
3. **Add Users**: Create or import users
4. **Create Pages**: Add login page with shortcode
5. **Configure Protection**: Restrict pages
6. **Test Thoroughly**: Login, register, 2FA

## Getting Help

- Check [README.md](README.md) for detailed documentation
- Review [STRUCTURE.md](STRUCTURE.md) for code organization
- Check [CHANGELOG.md](CHANGELOG.md) for updates
- Deploy to live server when ready

## Development

For developers looking to extend the plugin:
- See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines
- Review code comments in `/inc/` files
- Check WordPress hooks system
- Organized code makes extensions easy

---

**You're all set! Start using Modern Auth Portal now.** 🚀
