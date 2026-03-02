<?php
/**
 * Database and Activation Functions
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Plugin Activation Hook
 */
function map_activate() {
    global $wpdb;
    
    // Add custom role
    add_role(MAP_ROLE, 'Dashboard User', ['read' => true]);
    
    // Create login history table
    $table_name = $wpdb->prefix . 'map_login_history';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        login_time datetime NOT NULL,
        ip_address varchar(100) NOT NULL,
        user_agent text NOT NULL,
        browser varchar(100) DEFAULT NULL,
        device varchar(100) DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY login_time (login_time)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Set default options
    $defaults = [
        'map_enable_registration' => '1',
        'map_require_approval' => '0',
        'map_redirect_after_login' => home_url(),
        'map_primary_color' => '#D4FF00',
        'map_secondary_color' => '#000000',
        'map_restricted_pages' => [],
        'map_logo_url' => '',
        'map_brand_name' => 'Welcome',
        'map_tagline' => 'Log in to continue your session',
        'map_enable_2fa' => '1',
        'map_terms_text' => 'By logging in, you agree to our Terms & Conditions and Privacy Policy.',
        'map_access_notice' => 'Need access? Contact your administrator to request portal access.',
        'map_allowed_roles' => ['administrator', 'editor', 'author', 'contributor', 'subscriber', MAP_ROLE],
        'map_session_timeout' => '1',
        'map_session_timeout_minutes' => '20',
        'map_show_email_notice' => '1'
    ];
    
    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value, false);
        }
    }
    
    flush_rewrite_rules();
}

/**
 * Plugin Deactivation Hook
 */
function map_deactivate() {
    flush_rewrite_rules();
}

/**
 * Ensure Database Table Exists
 */
add_action('init', 'map_ensure_db_table');
function map_ensure_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'map_login_history';
    
    // Check if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    
    if (!$table_exists) {
        // Table doesn't exist, create it
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            login_time datetime NOT NULL,
            ip_address varchar(100) NOT NULL,
            user_agent text NOT NULL,
            browser varchar(100) DEFAULT NULL,
            device varchar(100) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY login_time (login_time)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        error_log('MAP: Login history table created automatically');
    }
}
