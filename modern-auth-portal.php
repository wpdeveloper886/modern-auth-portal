<?php
/**
 * Plugin Name: Modern Auth Portal - Complete Edition v4.0
 * Plugin URI: https://solvetrics.com/
 * Description: Complete authentication system with Login, Register, Edit Profile, Reset Password, Change Password, Email 2FA, Session Timeout, Login History & Professional Dashboard
 * Version: 4.0.0
 * Author: Kamran Rasool Developer
 * Author URI: https://solvetrics.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: modern-auth-portal
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('MAP_VERSION', '4.0.0');
define('MAP_ROLE', 'dashboard_user');
define('MAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MAP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load plugin files
require_once MAP_PLUGIN_DIR . 'inc/functions-database.php';
require_once MAP_PLUGIN_DIR . 'inc/functions-utils.php';
require_once MAP_PLUGIN_DIR . 'inc/functions-core.php';
require_once MAP_PLUGIN_DIR . 'inc/functions-ajax.php';
require_once MAP_PLUGIN_DIR . 'inc/functions-admin.php';
require_once MAP_PLUGIN_DIR . 'admin/dashboard.php';
require_once MAP_PLUGIN_DIR . 'admin/login-history.php';
require_once MAP_PLUGIN_DIR . 'admin/settings.php';
require_once MAP_PLUGIN_DIR . 'public/shortcodes.php';

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'map_activate');
register_deactivation_hook(__FILE__, 'map_deactivate');
