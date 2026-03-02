<?php
/**
 * AJAX Handlers
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Login AJAX Handler
 */
add_action('wp_ajax_nopriv_map_login', 'map_process_login');
add_action('wp_ajax_map_login', 'map_process_login');

function map_process_login() {
    check_ajax_referer('map_login', 'map_login_nonce');
    
    $username_or_email = sanitize_text_field($_POST['username']);
    $password = $_POST['password'];
    
    if (is_email($username_or_email)) {
        $user = get_user_by('email', $username_or_email);
    } else {
        $user = get_user_by('login', $username_or_email);
    }
    
    if (!$user || !wp_check_password($password, $user->user_pass, $user->ID)) {
        wp_send_json_error(['message' => 'Invalid credentials']);
    }
    
    $allowed_roles = get_option('map_allowed_roles', ['administrator', 'editor', 'author', 'contributor', 'subscriber', MAP_ROLE]);
    $user_roles = $user->roles;
    $has_access = false;
    
    foreach ($user_roles as $role) {
        if (in_array($role, $allowed_roles)) {
            $has_access = true;
            break;
        }
    }
    
    if (!$has_access) {
        wp_send_json_error(['message' => 'Access denied. Your user role is not authorized.']);
    }
    
    if (get_option('map_require_approval') == '1' && in_array(MAP_ROLE, $user->roles)) {
        $approved = get_user_meta($user->ID, 'map_approved', true);
        if ($approved !== '1' && !in_array('administrator', $user->roles)) {
            wp_send_json_error(['message' => 'Account pending approval']);
        }
    }
    
    if (get_option('map_enable_2fa') == '1') {
        $code = map_generate_2fa_code();
        
        set_transient('map_2fa_code_' . $user->ID, $code, 300);
        set_transient('map_2fa_user_' . $user->ID, $user->ID, 300);
        
        $email_sent = map_send_2fa_email($user->ID, $code);
        
        if ($email_sent) {
            wp_send_json_success([
                'require_2fa' => true,
                'user_id' => $user->ID,
                'message' => 'Verification code sent to ' . $user->user_email
            ]);
        } else {
            wp_send_json_error([
                'message' => 'Email service error. Please contact administrator.'
            ]);
        }
    } else {
        wp_set_auth_cookie($user->ID, isset($_POST['remember']));
        map_track_login($user->user_login, $user);
        wp_send_json_success(['redirect' => get_option('map_redirect_after_login', home_url())]);
    }
}

/**
 * 2FA Verification AJAX Handler
 */
add_action('wp_ajax_nopriv_map_verify_2fa', 'map_process_verify_2fa');

function map_process_verify_2fa() {
    check_ajax_referer('map_2fa', 'map_2fa_nonce');
    
    $user_id = intval($_POST['user_id']);
    $code = sanitize_text_field($_POST['code']);
    
    $stored_code = get_transient('map_2fa_code_' . $user_id);
    
    error_log('MAP 2FA Verify - User: ' . $user_id . ' | Input: ' . $code . ' | Stored: ' . $stored_code);
    
    if (!$stored_code) {
        wp_send_json_error(['message' => 'Verification code expired. Please login again.']);
    }
    
    if ($code !== $stored_code) {
        wp_send_json_error(['message' => 'Invalid verification code. Please try again.']);
    }
    
    delete_transient('map_2fa_code_' . $user_id);
    delete_transient('map_2fa_user_' . $user_id);
    
    $user = get_userdata($user_id);
    wp_set_auth_cookie($user_id, true);
    
    if ($user) {
        map_track_login($user->user_login, $user);
    }
    
    wp_send_json_success(['redirect' => get_option('map_redirect_after_login', home_url())]);
}

/**
 * Registration AJAX Handler
 */
add_action('wp_ajax_nopriv_map_register', 'map_process_register');

function map_process_register() {
    check_ajax_referer('map_register', 'map_register_nonce');
    
    if (get_option('map_enable_registration') != '1') {
        wp_send_json_error(['message' => 'Registration disabled']);
    }
    
    $name = sanitize_text_field($_POST['name']);
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($username) || strlen($username) < 3) {
        wp_send_json_error(['message' => 'Username must be at least 3 characters']);
    }
    
    if (username_exists($username)) {
        wp_send_json_error(['message' => 'Username already taken']);
    }
    
    if (strlen($password) < 8) {
        wp_send_json_error(['message' => 'Password must be 8+ characters']);
    }
    
    if (email_exists($email)) {
        wp_send_json_error(['message' => 'Email already registered']);
    }
    
    $user_id = wp_create_user($username, $password, $email);
    
    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
    }
    
    $name_parts = explode(' ', $name, 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
    
    wp_update_user([
        'ID' => $user_id,
        'display_name' => $name,
        'first_name' => $first_name,
        'last_name' => $last_name
    ]);
    
    $user = new WP_User($user_id);
    $user->set_role(MAP_ROLE);
    
    if (get_option('map_require_approval') == '1') {
        update_user_meta($user_id, 'map_approved', '0');
        wp_send_json_success(['message' => 'Registration successful! Awaiting approval.']);
    } else {
        update_user_meta($user_id, 'map_approved', '1');
        wp_send_json_success(['message' => 'Registration successful! You can now login.']);
    }
}

/**
 * Update Profile AJAX Handler
 */
add_action('wp_ajax_map_update_profile', 'map_process_update_profile');

function map_process_update_profile() {
    check_ajax_referer('map_profile', 'map_profile_nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }
    
    $user_id = get_current_user_id();
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $bio = sanitize_textarea_field($_POST['bio']);
    
    $email_exists = email_exists($email);
    if ($email_exists && $email_exists != $user_id) {
        wp_send_json_error(['message' => 'Email already in use']);
    }
    
    $name_parts = explode(' ', $name, 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
    
    $result = wp_update_user([
        'ID' => $user_id,
        'display_name' => $name,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_email' => $email,
        'description' => $bio
    ]);
    
    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }
    
    if (!empty($_FILES['avatar']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['avatar'], ['test_form' => false]);
        
        if (!isset($upload['error'])) {
            update_user_meta($user_id, 'map_avatar', $upload['url']);
        }
    }
    
    wp_send_json_success(['message' => 'Profile updated successfully!']);
}

/**
 * Change Password AJAX Handler
 */
add_action('wp_ajax_map_change_password', 'map_process_change_password');

function map_process_change_password() {
    check_ajax_referer('map_change_password', 'map_change_password_nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }
    
    $user_id = get_current_user_id();
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $user = get_userdata($user_id);
    
    if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
        wp_send_json_error(['message' => 'Current password is incorrect']);
    }
    
    if (strlen($new_password) < 8) {
        wp_send_json_error(['message' => 'New password must be at least 8 characters']);
    }
    
    if ($new_password !== $confirm_password) {
        wp_send_json_error(['message' => 'Passwords do not match']);
    }
    
    wp_set_password($new_password, $user_id);
    wp_set_auth_cookie($user_id);
    
    wp_send_json_success(['message' => 'Password changed successfully!']);
}

/**
 * Reset Password AJAX Handler
 */
add_action('wp_ajax_nopriv_map_reset_password', 'map_process_reset_password');

function map_process_reset_password() {
    check_ajax_referer('map_reset_password', 'map_reset_password_nonce');
    
    $email = sanitize_email($_POST['email']);
    
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_send_json_error(['message' => 'No account found with that email']);
    }
    
    $key = get_password_reset_key($user);
    
    if (is_wp_error($key)) {
        wp_send_json_error(['message' => $key->get_error_message()]);
    }
    
    $reset_url = add_query_arg([
        'action' => 'rp',
        'key' => $key,
        'login' => rawurlencode($user->user_login)
    ], wp_login_url());
    
    $message = "Hi " . $user->display_name . ",\n\n";
    $message .= "You requested a password reset. Click the link below:\n\n";
    $message .= $reset_url . "\n\n";
    $message .= "If you didn't request this, ignore this email.\n\n";
    $message .= "Thanks!";
    
    $sent = wp_mail($email, 'Password Reset Request', $message);
    
    if ($sent) {
        wp_send_json_success(['message' => 'Password reset link sent to your email!']);
    } else {
        wp_send_json_error(['message' => 'Failed to send email']);
    }
}

/**
 * Dismiss Email Notice
 */
add_action('wp_ajax_map_dismiss_email_notice', 'map_dismiss_email_notice');
function map_dismiss_email_notice() {
    check_ajax_referer('map_dismiss', 'nonce');
    update_option('map_show_email_notice', '0');
    wp_send_json_success();
}

/**
 * Delete Login History
 */
add_action('wp_ajax_map_delete_login_history', 'map_delete_login_history');
function map_delete_login_history() {
    check_ajax_referer('map_delete_history', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'map_login_history';
    
    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
    
    if (empty($ids)) {
        wp_send_json_error(['message' => 'No records selected']);
    }
    
    $placeholders = implode(',', array_fill(0, count($ids), '%d'));
    $deleted = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", $ids));
    
    if ($deleted) {
        wp_send_json_success(['message' => $deleted . ' record(s) deleted successfully']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete records']);
    }
}
