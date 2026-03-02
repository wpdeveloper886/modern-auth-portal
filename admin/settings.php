<?php
/**
 * Admin Settings Page
 */

if (!defined('WPINC')) {
    die;
}

function map_settings_page() {
    if (isset($_POST['map_reset_colors']) && check_admin_referer('map_settings', 'map_nonce')) {
        update_option('map_primary_color', '#D4FF00');
        update_option('map_secondary_color', '#000000');
        echo '<div class="notice notice-success"><p><strong>Colors Reset!</strong></p></div>';
    }
    
    if (isset($_POST['map_save']) && check_admin_referer('map_settings', 'map_nonce')) {
        update_option('map_enable_registration', isset($_POST['enable_registration']) ? '1' : '0');
        update_option('map_require_approval', isset($_POST['require_approval']) ? '1' : '0');
        update_option('map_enable_2fa', isset($_POST['enable_2fa']) ? '1' : '0');
        update_option('map_session_timeout', isset($_POST['session_timeout']) ? '1' : '0');
        update_option('map_session_timeout_minutes', intval($_POST['session_timeout_minutes']));
        update_option('map_redirect_after_login', sanitize_text_field($_POST['redirect_url']));
        update_option('map_primary_color', sanitize_hex_color($_POST['primary_color']));
        update_option('map_secondary_color', sanitize_hex_color($_POST['secondary_color']));
        update_option('map_brand_name', sanitize_text_field($_POST['brand_name']));
        update_option('map_tagline', sanitize_text_field($_POST['tagline']));
        update_option('map_terms_text', wp_kses_post($_POST['terms_text']));
        update_option('map_access_notice', wp_kses_post($_POST['access_notice']));
        update_option('map_restricted_pages', array_map('intval', (array)($_POST['restricted_pages'] ?? [])));
        update_option('map_allowed_roles', array_map('sanitize_text_field', (array)($_POST['allowed_roles'] ?? [])));
        
        if (!empty($_FILES['logo_upload']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $upload = wp_handle_upload($_FILES['logo_upload'], ['test_form' => false]);
            if (!isset($upload['error'])) {
                update_option('map_logo_url', $upload['url']);
            }
        }
        
        echo '<div class="notice notice-success"><p><strong>Settings saved!</strong></p></div>';
    }
    
    $settings = [
        'enable_reg' => get_option('map_enable_registration', '1'),
        'require_approval' => get_option('map_require_approval', '0'),
        'enable_2fa' => get_option('map_enable_2fa', '1'),
        'session_timeout' => get_option('map_session_timeout', '1'),
        'timeout_minutes' => get_option('map_session_timeout_minutes', '20'),
        'redirect' => get_option('map_redirect_after_login', home_url()),
        'primary' => get_option('map_primary_color', '#D4FF00'),
        'secondary' => get_option('map_secondary_color', '#000000'),
        'restricted' => get_option('map_restricted_pages', []),
        'logo' => get_option('map_logo_url', ''),
        'brand' => get_option('map_brand_name', 'Welcome'),
        'tagline' => get_option('map_tagline', 'Log in to continue'),
        'terms_text' => get_option('map_terms_text', 'By logging in, you agree to our Terms & Conditions and Privacy Policy.'),
        'access_notice' => get_option('map_access_notice', 'Need access? Contact your administrator to request portal access.'),
        'allowed_roles' => get_option('map_allowed_roles', ['administrator', 'editor', 'author', 'contributor', 'subscriber', MAP_ROLE])
    ];
    
    $all_pages = get_pages();
    $all_roles = wp_roles()->roles;
    $portal_users = count(get_users());
    ?>
    <div class="wrap map-admin">
        <h1>Auth Portal Settings</h1>
        
        <div class="map-stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $portal_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo count($settings['restricted']); ?></div>
                <div class="stat-label">Protected Pages</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">7</div>
                <div class="stat-label">Shortcodes</div>
            </div>
        </div>
        
        <form method="post" enctype="multipart/form-data" class="map-form">
            <?php wp_nonce_field('map_settings', 'map_nonce'); ?>
            
            <div class="map-section">
                <h2>Branding</h2>
                <table class="form-table">
                    <tr>
                        <th>Logo</th>
                        <td>
                            <?php if ($settings['logo']): ?>
                                <img src="<?php echo esc_url($settings['logo']); ?>" style="max-width:200px;margin-bottom:10px;display:block;">
                            <?php endif; ?>
                            <input type="file" name="logo_upload" accept="image/*">
                        </td>
                    </tr>
                    <tr>
                        <th>Brand Name</th>
                        <td><input type="text" name="brand_name" value="<?php echo esc_attr($settings['brand']); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>Tagline</th>
                        <td><input type="text" name="tagline" value="<?php echo esc_attr($settings['tagline']); ?>" class="regular-text"></td>
                    </tr>
                </table>
                
                <h3>Colors</h3>
                <div class="color-grid">
                    <div class="color-item">
                        <label>Primary</label>
                        <input type="color" name="primary_color" value="<?php echo esc_attr($settings['primary']); ?>">
                        <span><?php echo $settings['primary']; ?></span>
                    </div>
                    <div class="color-item">
                        <label>Secondary</label>
                        <input type="color" name="secondary_color" value="<?php echo esc_attr($settings['secondary']); ?>">
                        <span><?php echo $settings['secondary']; ?></span>
                    </div>
                </div>
                
                <p style="margin-top:20px;">
                    <button type="submit" name="map_reset_colors" class="button" style="background:#dc3545;color:white;border:none;">
                        Reset Colors
                    </button>
                </p>
            </div>
            
            <div class="map-section">
                <h2>Settings</h2>
                <table class="form-table">
                    <tr>
                        <th>Enable Registration</th>
                        <td><label><input type="checkbox" name="enable_registration" value="1" <?php checked($settings['enable_reg'], '1'); ?>> Allow new users</label></td>
                    </tr>
                    <tr>
                        <th>Require Approval</th>
                        <td><label><input type="checkbox" name="require_approval" value="1" <?php checked($settings['require_approval'], '1'); ?>> Admin approval required</label></td>
                    </tr>
                    <tr>
                        <th>Enable Email 2FA</th>
                        <td><label><input type="checkbox" name="enable_2fa" value="1" <?php checked($settings['enable_2fa'], '1'); ?>> Require email verification code on login (5 minutes validity)</label></td>
                    </tr>
                    <tr>
                        <th>Session Timeout</th>
                        <td>
                            <label><input type="checkbox" name="session_timeout" value="1" <?php checked($settings['session_timeout'], '1'); ?>> Enable automatic logout after inactivity</label>
                            <br><br>
                            <label>Timeout Duration (minutes): 
                                <input type="number" name="session_timeout_minutes" value="<?php echo esc_attr($settings['timeout_minutes']); ?>" min="5" max="120" style="width: 80px;">
                            </label>
                            <p class="description">Users will be logged out after this many minutes of inactivity (default: 20 minutes)</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Allowed User Roles</th>
                        <td>
                            <?php foreach ($all_roles as $role_key => $role_info): ?>
                                <label style="display:block;margin:5px 0;">
                                    <input type="checkbox" name="allowed_roles[]" value="<?php echo esc_attr($role_key); ?>" <?php echo in_array($role_key, $settings['allowed_roles']) ? 'checked' : ''; ?>>
                                    <?php echo esc_html($role_info['name']); ?>
                                </label>
                            <?php endforeach; ?>
                            <p class="description">Select which user roles can access the portal</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Redirect After Login</th>
                        <td><input type="text" name="redirect_url" value="<?php echo esc_attr($settings['redirect']); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>Protected Pages</th>
                        <td>
                            <select name="restricted_pages[]" multiple size="8" style="width:100%;max-width:400px;">
                                <?php foreach ($all_pages as $page): ?>
                                    <option value="<?php echo $page->ID; ?>" <?php echo in_array($page->ID, $settings['restricted']) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="map-section">
                <h2>Login Page Text</h2>
                <table class="form-table">
                    <tr>
                        <th>Terms & Conditions Text</th>
                        <td>
                            <textarea name="terms_text" rows="3" class="large-text"><?php echo esc_textarea($settings['terms_text']); ?></textarea>
                            <p class="description">Displayed below the login form</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Access Request Notice</th>
                        <td>
                            <textarea name="access_notice" rows="3" class="large-text"><?php echo esc_textarea($settings['access_notice']); ?></textarea>
                            <p class="description">Displayed below terms text</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="map-section">
                <h2>Available Shortcodes</h2>
                <div class="shortcode-grid">
                    <div class="shortcode-card">
                        <strong>Login</strong>
                        <code>[modern_auth_login]</code>
                    </div>
                    <div class="shortcode-card">
                        <strong>Profile</strong>
                        <code>[modern_auth_profile]</code>
                    </div>
                    <div class="shortcode-card">
                        <strong>Change Password</strong>
                        <code>[modern_auth_change_password]</code>
                    </div>
                    <div class="shortcode-card">
                        <strong>Reset Password</strong>
                        <code>[modern_auth_reset_password]</code>
                    </div>
                    <div class="shortcode-card">
                        <strong>Logout</strong>
                        <code>[modern_auth_logout]</code>
                    </div>
                    <div class="shortcode-card">
                        <strong>Welcome</strong>
                        <code>[modern_auth_welcome]</code>
                    </div>
                    <div class="shortcode-card">
                        <strong>Status</strong>
                        <code>[modern_auth_status]</code>
                    </div>
                </div>
            </div>
            
            <p class="submit">
                <button type="submit" name="map_save" class="button button-primary button-hero">Save All Settings</button>
            </p>
        </form>
        
        <?php if (get_option('map_show_email_notice', '1') == '1'): ?>
        <div class="map-section" id="emailNoticeSection">
            <h2>Email Configuration Notice 
                <button type="button" onclick="mapDismissEmailNotice()" class="button button-small" style="float:right;">Dismiss</button>
            </h2>
            <div style="background:#fff3cd;border-left:4px solid #ffc107;padding:15px;border-radius:5px;">
                <p><strong>Important:</strong> For 2FA emails to work, you need to configure SMTP on your WordPress site.</p>
                <p>Recommended plugins:</p>
                <ul style="margin-left:20px;">
                    <li><strong>WP Mail SMTP</strong> - <a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank">Download</a></li>
                    <li><strong>Easy WP SMTP</strong> - <a href="https://wordpress.org/plugins/easy-wp-smtp/" target="_blank">Download</a></li>
                </ul>
                <p>Without SMTP configuration, emails may not be delivered by your hosting provider.</p>
            </div>
        </div>
        <script>
        function mapDismissEmailNotice() {
            if (confirm('Hide this notice permanently?')) {
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=map_dismiss_email_notice&nonce=<?php echo wp_create_nonce("map_dismiss"); ?>'
                }).then(() => {
                    document.getElementById('emailNoticeSection').style.display = 'none';
                });
            }
        }
        </script>
        <?php endif; ?>
    </div>
    
    <style>
    .map-admin{max-width:1200px}
    .map-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin:20px 0}
    .stat-box{background:linear-gradient(135deg,#000 0%,#1a1a1a 100%);color:#d4ff00;padding:30px;border-radius:12px;text-align:center;border:2px solid #d4ff00;box-shadow:0 8px 25px rgba(212,255,0,0.2)}
    .stat-number{font-size:48px;font-weight:800;color:#d4ff00}
    .stat-label{font-size:14px;opacity:0.9;margin-top:5px;color:#fff}
    .map-section{background:white;padding:30px;margin:20px 0;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.05);border:2px solid #f0f0f0}
    .map-section h2{margin-top:0;border-bottom:3px solid #d4ff00;padding-bottom:15px;color:#000}
    .color-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:20px;margin-top:15px}
    .color-item{text-align:center;background:#f8f9fa;padding:20px;border-radius:10px;border:2px solid #e0e0e0}
    .color-item label{display:block;font-weight:700;margin-bottom:12px;color:#000;font-size:13px}
    .color-item input[type="color"]{width:80px;height:50px;border:3px solid #d4ff00;border-radius:8px;cursor:pointer}
    .color-item span{display:block;margin-top:8px;font-family:monospace;font-size:12px;color:#666}
    .shortcode-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px}
    .shortcode-card{background:linear-gradient(135deg,#000 0%,#1a1a1a 100%);padding:20px;border-radius:8px;border:2px solid #d4ff00;text-align:center}
    .shortcode-card strong{color:#d4ff00;font-size:15px;display:block;margin-bottom:10px}
    .shortcode-card code{display:block;margin-top:10px;background:#d4ff00;color:#000;padding:10px 8px;border-radius:6px;font-size:13px;font-weight:700}
    .button-primary.button-hero{background:linear-gradient(135deg,#000 0%,#1a1a1a 100%)!important;border:2px solid #d4ff00!important;color:#d4ff00!important}
    </style>
    <?php
}
