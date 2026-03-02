<?php
/**
 * Core Functionality
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Force Color Check
 */
add_action('init', 'map_force_color_check');
function map_force_color_check() {
    $current_primary = get_option('map_primary_color');
    $current_secondary = get_option('map_secondary_color');
    
    if ($current_primary !== '#D4FF00') {
        update_option('map_primary_color', '#D4FF00');
    }
    if ($current_secondary !== '#000000') {
        update_option('map_secondary_color', '#000000');
    }
}

/**
 * Session Timeout Functionality
 */
add_action('init', 'map_check_session_timeout');
function map_check_session_timeout() {
    if (!is_user_logged_in()) return;
    if (get_option('map_session_timeout') != '1') return;
    
    $timeout_minutes = intval(get_option('map_session_timeout_minutes', 20));
    $timeout_seconds = $timeout_minutes * 60;
    
    $last_activity = get_user_meta(get_current_user_id(), 'map_last_activity', true);
    
    if ($last_activity && (time() - $last_activity) > $timeout_seconds) {
        wp_logout();
        wp_redirect(home_url() . '?session_timeout=1');
        exit;
    }
    
    update_user_meta(get_current_user_id(), 'map_last_activity', time());
}

/**
 * Login History Tracking
 */
add_action('wp_login', 'map_track_login', 10, 2);
function map_track_login($user_login, $user) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'map_login_history';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    
    // Parse browser and device
    $browser = map_get_browser($user_agent);
    $device = map_get_device($user_agent);
    
    $result = $wpdb->insert(
        $table_name,
        [
            'user_id' => $user->ID,
            'login_time' => current_time('mysql'),
            'ip_address' => map_get_client_ip(),
            'user_agent' => $user_agent,
            'browser' => $browser,
            'device' => $device
        ],
        ['%d', '%s', '%s', '%s', '%s', '%s']
    );
    
    if ($result === false) {
        error_log('MAP Login Tracking FAILED for user ' . $user->ID . ' | Error: ' . $wpdb->last_error);
    } else {
        error_log('MAP Login Tracking SUCCESS for user ' . $user->ID . ' (' . $user->user_login . ') | Browser: ' . $browser . ' | Device: ' . $device);
    }
    
    // Update last login time
    update_user_meta($user->ID, 'map_last_login', current_time('mysql'));
    update_user_meta($user->ID, 'map_last_activity', time());
}

/**
 * Add Last Login Column to Users List
 */
add_filter('manage_users_columns', 'map_add_last_login_column');
function map_add_last_login_column($columns) {
    $columns['last_login'] = 'Last Login';
    return $columns;
}

add_filter('manage_users_custom_column', 'map_show_last_login_column', 10, 3);
function map_show_last_login_column($value, $column_name, $user_id) {
    if ($column_name == 'last_login') {
        $last_login = get_user_meta($user_id, 'map_last_login', true);
        if ($last_login) {
            $time_diff = human_time_diff(strtotime($last_login), current_time('timestamp'));
            return '<span style="color:#2271b1;font-weight:600;">' . $time_diff . ' ago</span><br><small style="color:#666;">' . date('M d, Y h:i A', strtotime($last_login)) . '</small>';
        }
        return '<span style="color:#999;">Never</span>';
    }
    return $value;
}

/**
 * Page Restriction
 */
add_action('template_redirect', 'map_restrict_pages');

function map_restrict_pages() {
    if (is_user_logged_in()) return;
    
    $restricted = get_option('map_restricted_pages', []);
    if (empty($restricted)) return;
    
    if (is_page($restricted)) {
        add_filter('the_content', 'map_replace_with_login_form');
    }
}

function map_replace_with_login_form($content) {
    return do_shortcode('[modern_auth_login]');
}

/**
 * User Approval Fields
 */
add_action('show_user_profile', 'map_user_approval_field');
add_action('edit_user_profile', 'map_user_approval_field');

function map_user_approval_field($user) {
    if (!in_array(MAP_ROLE, $user->roles)) return;
    $approved = get_user_meta($user->ID, 'map_approved', true);
    ?>
    <h3>Portal Access</h3>
    <table class="form-table">
        <tr>
            <th>Approval Status</th>
            <td>
                <label>
                    <input type="checkbox" name="map_approved" value="1" <?php checked($approved, '1'); ?>>
                    <strong>Approve portal access</strong>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'map_save_approval');
add_action('edit_user_profile_update', 'map_save_approval');

function map_save_approval($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;
    $approved = isset($_POST['map_approved']) ? '1' : '0';
    update_user_meta($user_id, 'map_approved', $approved);
}
