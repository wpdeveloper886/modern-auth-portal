<?php
/**
 * Admin Menu and Functions
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Admin Menu
 */
add_action('admin_menu', 'map_admin_menu');
function map_admin_menu() {
    add_menu_page(
        'Auth Portal',
        'Auth Portal',
        'manage_options',
        'modern-auth-portal',
        'map_dashboard_page',
        'dashicons-lock',
        30
    );
    
    add_submenu_page(
        'modern-auth-portal',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'modern-auth-portal',
        'map_dashboard_page'
    );
    
    add_submenu_page(
        'modern-auth-portal',
        'Settings',
        'Settings',
        'manage_options',
        'map-settings',
        'map_settings_page'
    );
    
    add_submenu_page(
        'modern-auth-portal',
        'Login History',
        'Login History',
        'manage_options',
        'map-login-history',
        'map_login_history_page'
    );
}
