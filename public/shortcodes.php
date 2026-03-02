<?php
/**
 * Frontend Shortcodes
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Register Shortcodes
 */
add_shortcode('modern_auth_login', 'map_login_shortcode');
add_shortcode('modern_auth_profile', 'map_profile_shortcode');
add_shortcode('modern_auth_change_password', 'map_change_password_shortcode');
add_shortcode('modern_auth_reset_password', 'map_reset_password_shortcode');
add_shortcode('modern_auth_logout', 'map_logout_shortcode');
add_shortcode('modern_auth_welcome', 'map_welcome_shortcode');
add_shortcode('modern_auth_status', 'map_status_shortcode');

/**
 * Login Shortcode
 */
function map_login_shortcode() {
    if (is_user_logged_in()) {
        return '<div class="map-notice">Already logged in. <a href="' . wp_logout_url(home_url()) . '">Logout</a></div>';
    }
    
    $session_timeout = isset($_GET['session_timeout']) ? true : false;
    $logo = get_option('map_logo_url', '');
    $brand = get_option('map_brand_name', 'Welcome');
    $tagline = get_option('map_tagline', 'Log in to continue');    
    $terms_text = get_option('map_terms_text', 'By logging in, you agree to our Terms & Conditions and Privacy Policy.');
    $access_notice = get_option('map_access_notice', 'Need access? Contact your administrator to request portal access.');
    $unique_id = 'map_' . uniqid();
    $ajax_url = admin_url('admin-ajax.php');
    
    ob_start();
    echo map_get_base_styles($unique_id);
    ?>
    
    <div class="map-wrapper-<?php echo $unique_id; ?>" id="<?php echo esc_attr($unique_id); ?>">
        <div class="map-container">
            <div class="map-card">
                <div class="map-left">
                    <div class="map-bg-pattern">
                        <div class="particle particle-1"></div>
                        <div class="particle particle-2"></div>
                        <div class="particle particle-3"></div>
                        <div class="particle particle-4"></div>
                        <div class="pattern-circle circle-1"></div>
                        <div class="pattern-circle circle-2"></div>
                        <div class="animated-line line-1"></div>
                    </div>
                    
                    <div class="map-content">
                        <?php if ($logo): ?>
                            <img src="<?php echo esc_url($logo); ?>" alt="Logo" class="map-logo">
                        <?php else: ?>
                            <div class="map-brand-icon">
                                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                                    <circle cx="40" cy="40" r="35" stroke="#D4FF00" stroke-width="3"/>
                                    <path d="M40 25V40L50 50" stroke="#D4FF00" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <h1 class="map-title" id="mapTitle_<?php echo $unique_id; ?>"><?php echo esc_html($brand); ?></h1>
                        <p class="map-subtitle" id="mapSubtitle_<?php echo $unique_id; ?>"><?php echo esc_html($tagline); ?></p>
                        <div class="map-decorative-line"></div>
                    </div>
                </div>
                
                <div class="map-right">
                    <div class="map-form-wrapper">
                        <!-- LOGIN FORM -->
                        <div id="loginFormContainer_<?php echo $unique_id; ?>" style="display: block;">
                            <h2>Log In</h2>
                            <?php if ($session_timeout): ?>
                                <div class="map-notice map-warning">Your session expired due to inactivity. Please login again.</div>
                            <?php endif; ?>
                            <div id="mapLoginMsg_<?php echo $unique_id; ?>"></div>
                            <form id="mapLoginForm_<?php echo $unique_id; ?>">
                                <?php wp_nonce_field('map_login', 'map_login_nonce'); ?>
                                <div class="map-field">
                                    <label>Username or Email</label>
                                    <input type="text" name="username" required placeholder="Enter username or email">
                                </div>
                                <div class="map-field">
                                    <label>Password</label>
                                    <input type="password" name="password" required placeholder="Enter password">
                                </div>
                                <label class="map-check">
                                    <input type="checkbox" name="remember">
                                    <span>Remember me</span>
                                </label>
                                <button type="submit" class="map-btn">LOG IN</button>
                            </form>
                            <div class="toggle-link">
                                <a href="#" class="forgot-password-link" data-unique="<?php echo $unique_id; ?>">Forgotten password?</a>
                            </div>
                            
                            <?php if ($terms_text || $access_notice): ?>
                            <div class="map-footer-text">
                                <?php if ($terms_text): ?>
                                    <p><?php echo wp_kses_post($terms_text); ?></p>
                                <?php endif; ?>
                                <?php if ($access_notice): ?>
                                    <p><strong><?php echo wp_kses_post($access_notice); ?></strong></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- RESET PASSWORD FORM -->
                        <div id="resetPasswordContainer_<?php echo $unique_id; ?>" style="display: none;">
                            <h2>Reset Password</h2>
                            <div id="mapResetMsg_<?php echo $unique_id; ?>"></div>
                            <form id="mapResetForm_<?php echo $unique_id; ?>">
                                <?php wp_nonce_field('map_reset_password', 'map_reset_password_nonce'); ?>
                                <div class="map-field">
                                    <label>Email Address</label>
                                    <input type="email" name="email" required placeholder="your@email.com">
                                </div>
                                <button type="submit" class="map-btn">SEND RESET LINK</button>
                            </form>
                            <div class="toggle-link">
                                <a href="#" class="back-to-login-link" data-unique="<?php echo $unique_id; ?>">Back to login</a>
                            </div>
                        </div>
                        
                        <!-- 2FA VERIFICATION FORM -->
                        <div id="twoFAFormContainer_<?php echo $unique_id; ?>" style="display: none;">
                            <h2>Verify Code</h2>
                            <div id="map2FAMsg_<?php echo $unique_id; ?>"></div>
                            <form id="map2FAForm_<?php echo $unique_id; ?>">
                                <?php wp_nonce_field('map_2fa', 'map_2fa_nonce'); ?>
                                <input type="hidden" name="user_id" id="map2FAUserId_<?php echo $unique_id; ?>">
                                <div class="map-field">
                                    <label>Verification Code</label>
                                    <input type="text" name="code" required placeholder="Enter 6-digit code" maxlength="6" pattern="[0-9]{6}" autocomplete="off">
                                    <p style="font-size:12px;color:#666;margin:5px 0 0 0;">Check your email for the verification code. Valid for 5 minutes.</p>
                                </div>
                                <button type="submit" class="map-btn">VERIFY & LOG IN</button>
                            </form>
                            <div class="toggle-link">
                                <a href="#" class="back-to-login-link" data-unique="<?php echo $unique_id; ?>">Back to login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        var uniqueId = <?php echo json_encode($unique_id); ?>;
        var MAP_AJAX_URL = <?php echo json_encode($ajax_url); ?>;
        var brand = <?php echo json_encode($brand); ?>;
        var tagline = <?php echo json_encode($tagline); ?>;
        
        function initAuth() {
            var loginForm = document.getElementById('mapLoginForm_' + uniqueId);
            var twoFAForm = document.getElementById('map2FAForm_' + uniqueId);
            var resetForm = document.getElementById('mapResetForm_' + uniqueId);
            
            // Toggle Functions
            function showResetPassword() {
                document.getElementById('loginFormContainer_' + uniqueId).style.display = 'none';
                document.getElementById('resetPasswordContainer_' + uniqueId).style.display = 'block';
                document.getElementById('twoFAFormContainer_' + uniqueId).style.display = 'none';
                document.getElementById('mapTitle_' + uniqueId).textContent = 'Reset Password';
                document.getElementById('mapSubtitle_' + uniqueId).textContent = "We'll send you a reset link";
            }
            
            function showLogin() {
                document.getElementById('loginFormContainer_' + uniqueId).style.display = 'block';
                document.getElementById('resetPasswordContainer_' + uniqueId).style.display = 'none';
                document.getElementById('twoFAFormContainer_' + uniqueId).style.display = 'none';
                document.getElementById('mapTitle_' + uniqueId).textContent = brand;
                document.getElementById('mapSubtitle_' + uniqueId).textContent = tagline;
                document.getElementById('mapResetMsg_' + uniqueId).innerHTML = '';
                document.getElementById('map2FAMsg_' + uniqueId).innerHTML = '';
            }
            
            function show2FA() {
                document.getElementById('loginFormContainer_' + uniqueId).style.display = 'none';
                document.getElementById('resetPasswordContainer_' + uniqueId).style.display = 'none';
                document.getElementById('twoFAFormContainer_' + uniqueId).style.display = 'block';
                document.getElementById('mapTitle_' + uniqueId).textContent = 'Verify Code';
                document.getElementById('mapSubtitle_' + uniqueId).textContent = 'Check your email for verification';
            }
            
            document.body.addEventListener('click', function(e) {
                if (e.target.classList.contains('forgot-password-link') && e.target.getAttribute('data-unique') === uniqueId) {
                    e.preventDefault();
                    showResetPassword();
                }
                if (e.target.classList.contains('back-to-login-link') && e.target.getAttribute('data-unique') === uniqueId) {
                    e.preventDefault();
                    showLogin();
                }
            });
            
            // Login Form Handler
            if (loginForm && !loginForm.dataset.init) {
                loginForm.dataset.init = 'true';
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = this.querySelector('.map-btn');
                    var msg = document.getElementById('mapLoginMsg_' + uniqueId);
                    btn.disabled = true;
                    btn.textContent = 'LOGGING IN...';
                    
                    var formData = new FormData(this);
                    formData.append('action', 'map_login');
                    
                    fetch(MAP_AJAX_URL, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                if (data.data.require_2fa) {
                                    document.getElementById('map2FAUserId_' + uniqueId).value = data.data.user_id;
                                    show2FA();
                                    document.getElementById('map2FAMsg_' + uniqueId).innerHTML = '<div class="map-notice map-info">' + data.data.message + '</div>';
                                } else {
                                    msg.innerHTML = '<div class="map-notice map-success">Success! Redirecting...</div>';
                                    setTimeout(() => window.location.href = data.data.redirect, 1500);
                                }
                            } else {
                                msg.innerHTML = '<div class="map-notice map-error">' + data.data.message + '</div>';
                            }
                            btn.disabled = false;
                            btn.textContent = 'LOG IN';
                        })
                        .catch(err => {
                            msg.innerHTML = '<div class="map-notice map-error">Connection error. Please try again.</div>';
                            btn.disabled = false;
                            btn.textContent = 'LOG IN';
                        });
                });
            }
            
            // Reset Password Form Handler
            if (resetForm && !resetForm.dataset.init) {
                resetForm.dataset.init = 'true';
                resetForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = this.querySelector('.map-btn');
                    var msg = document.getElementById('mapResetMsg_' + uniqueId);
                    btn.disabled = true;
                    btn.textContent = 'SENDING...';
                    
                    var formData = new FormData(this);
                    formData.append('action', 'map_reset_password');
                    
                    fetch(MAP_AJAX_URL, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                msg.innerHTML = '<div class="map-notice map-success">' + data.data.message + '</div>';
                                resetForm.reset();
                            } else {
                                msg.innerHTML = '<div class="map-notice map-error">' + data.data.message + '</div>';
                            }
                            btn.disabled = false;
                            btn.textContent = 'SEND RESET LINK';
                        })
                        .catch(err => {
                            msg.innerHTML = '<div class="map-notice map-error">Connection error. Please try again.</div>';
                            btn.disabled = false;
                            btn.textContent = 'SEND RESET LINK';
                        });
                });
            }
            
            // 2FA Form Handler
            if (twoFAForm && !twoFAForm.dataset.init) {
                twoFAForm.dataset.init = 'true';
                twoFAForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = this.querySelector('.map-btn');
                    var msg = document.getElementById('map2FAMsg_' + uniqueId);
                    btn.disabled = true;
                    btn.textContent = 'VERIFYING...';
                    
                    var formData = new FormData(this);
                    formData.append('action', 'map_verify_2fa');
                    
                    fetch(MAP_AJAX_URL, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                msg.innerHTML = '<div class="map-notice map-success">Verified! Redirecting...</div>';
                                setTimeout(() => window.location.href = data.data.redirect, 1000);
                            } else {
                                msg.innerHTML = '<div class="map-notice map-error">' + data.data.message + '</div>';
                                btn.disabled = false;
                                btn.textContent = 'VERIFY & LOG IN';
                            }
                        })
                        .catch(err => {
                            msg.innerHTML = '<div class="map-notice map-error">Connection error. Please try again.</div>';
                            btn.disabled = false;
                            btn.textContent = 'VERIFY & LOG IN';
                        });
                });
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAuth);
        } else {
            initAuth();
        }
    })();
    </script>
    
    <?php
    return ob_get_clean();
}

/**
 * Profile Shortcode
 */
function map_profile_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="map-notice map-error">You must be logged in to edit your profile.</div>';
    }
    
    $user = wp_get_current_user();
    $unique_id = 'map_profile_' . uniqid();
    $ajax_url = admin_url('admin-ajax.php');
    $avatar = get_user_meta($user->ID, 'map_avatar', true);
    if (empty($avatar)) {
        $avatar = get_avatar_url($user->ID);
    }
    $brand = get_option('map_brand_name', 'Profile');
    $logo = get_option('map_logo_url', '');
    
    ob_start();
    echo map_get_base_styles($unique_id);
    ?>
    
    <div class="map-wrapper-<?php echo $unique_id; ?>">
        <div class="map-container">
            <div class="map-card">
                <div class="map-left">
                    <div class="map-bg-pattern">
                        <div class="particle particle-1"></div>
                        <div class="particle particle-2"></div>
                        <div class="particle particle-3"></div>
                        <div class="particle particle-4"></div>
                        <div class="pattern-circle circle-1"></div>
                        <div class="pattern-circle circle-2"></div>
                        <div class="animated-line line-1"></div>
                    </div>
                    
                    <div class="map-content">
                        <?php if ($logo): ?>
                            <img src="<?php echo esc_url($logo); ?>" alt="Logo" class="map-logo">
                        <?php endif; ?>
                        <h1 class="map-title">Edit Profile</h1>
                        <p class="map-subtitle">Update your information</p>
                        <div class="map-decorative-line"></div>
                    </div>
                </div>
                
                <div class="map-right">
                    <div class="map-form-wrapper">
                        <h2>Your Profile</h2>
                        <div id="mapProfileMsg_<?php echo $unique_id; ?>"></div>
                        <form id="mapProfileForm_<?php echo $unique_id; ?>" enctype="multipart/form-data">
                            <?php wp_nonce_field('map_profile', 'map_profile_nonce'); ?>
                            
                            <div class="avatar-upload-wrapper">
                                <img src="<?php echo esc_url($avatar); ?>" alt="Avatar" class="current-avatar" id="avatarPreview_<?php echo $unique_id; ?>">
                                <div class="avatar-upload-info">
                                    <label>Change Avatar</label>
                                    <input type="file" name="avatar" accept="image/*" id="avatarInput_<?php echo $unique_id; ?>">
                                </div>
                            </div>
                            
                            <div class="map-field">
                                <label>Full Name</label>
                                <input type="text" name="name" required value="<?php echo esc_attr($user->display_name); ?>" placeholder="Your name">
                            </div>
                            <div class="map-field">
                                <label>Email</label>
                                <input type="email" name="email" required value="<?php echo esc_attr($user->user_email); ?>" placeholder="your@email.com">
                            </div>
                            <div class="map-field">
                                <label>Bio</label>
                                <textarea name="bio" placeholder="Tell us about yourself..."><?php echo esc_textarea($user->description); ?></textarea>
                            </div>
                            <button type="submit" class="map-btn">UPDATE PROFILE</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        var uniqueId = <?php echo json_encode($unique_id); ?>;
        var MAP_AJAX_URL = <?php echo json_encode($ajax_url); ?>;
        
        function initProfile() {
            var form = document.getElementById('mapProfileForm_' + uniqueId);
            var avatarInput = document.getElementById('avatarInput_' + uniqueId);
            var avatarPreview = document.getElementById('avatarPreview_' + uniqueId);
            
            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            }
            
            if (form && !form.dataset.init) {
                form.dataset.init = 'true';
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = this.querySelector('.map-btn');
                    var msg = document.getElementById('mapProfileMsg_' + uniqueId);
                    btn.disabled = true;
                    btn.textContent = 'UPDATING...';
                    
                    var formData = new FormData(this);
                    formData.append('action', 'map_update_profile');
                    
                    fetch(MAP_AJAX_URL, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                msg.innerHTML = '<div class="map-notice map-success">' + data.data.message + '</div>';
                            } else {
                                msg.innerHTML = '<div class="map-notice map-error">' + data.data.message + '</div>';
                            }
                            btn.disabled = false;
                            btn.textContent = 'UPDATE PROFILE';
                        });
                });
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProfile);
        } else {
            initProfile();
        }
    })();
    </script>
    
    <?php
    return ob_get_clean();
}

/**
 * Change Password Shortcode
 */
function map_change_password_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="map-notice map-error">You must be logged in to change password.</div>';
    }
    
    $unique_id = 'map_chgpwd_' . uniqid();
    $ajax_url = admin_url('admin-ajax.php');
    $logo = get_option('map_logo_url', '');
    
    ob_start();
    echo map_get_base_styles($unique_id);
    ?>
    
    <div class="map-wrapper-<?php echo $unique_id; ?>">
        <div class="map-container">
            <div class="map-card">
                <div class="map-left">
                    <div class="map-bg-pattern">
                        <div class="particle particle-1"></div>
                        <div class="particle particle-2"></div>
                        <div class="particle particle-3"></div>
                        <div class="particle particle-4"></div>
                        <div class="pattern-circle circle-1"></div>
                        <div class="pattern-circle circle-2"></div>
                        <div class="animated-line line-1"></div>
                    </div>
                    
                    <div class="map-content">
                        <?php if ($logo): ?>
                            <img src="<?php echo esc_url($logo); ?>" alt="Logo" class="map-logo">
                        <?php endif; ?>
                        <h1 class="map-title">Security</h1>
                        <p class="map-subtitle">Update your password</p>
                        <div class="map-decorative-line"></div>
                    </div>
                </div>
                
                <div class="map-right">
                    <div class="map-form-wrapper">
                        <h2>Change Password</h2>
                        <div id="mapChgPwdMsg_<?php echo $unique_id; ?>"></div>
                        <form id="mapChgPwdForm_<?php echo $unique_id; ?>">
                            <?php wp_nonce_field('map_change_password', 'map_change_password_nonce'); ?>
                            <div class="map-field">
                                <label>Current Password</label>
                                <input type="password" name="current_password" required placeholder="Enter current password">
                            </div>
                            <div class="map-field">
                                <label>New Password</label>
                                <input type="password" name="new_password" required placeholder="Min 8 characters" minlength="8">
                            </div>
                            <div class="map-field">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required placeholder="Re-enter new password">
                            </div>
                            <button type="submit" class="map-btn">CHANGE PASSWORD</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        var uniqueId = <?php echo json_encode($unique_id); ?>;
        var MAP_AJAX_URL = <?php echo json_encode($ajax_url); ?>;
        
        function initChangePassword() {
            var form = document.getElementById('mapChgPwdForm_' + uniqueId);
            
            if (form && !form.dataset.init) {
                form.dataset.init = 'true';
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = this.querySelector('.map-btn');
                    var msg = document.getElementById('mapChgPwdMsg_' + uniqueId);
                    btn.disabled = true;
                    btn.textContent = 'CHANGING...';
                    
                    var formData = new FormData(this);
                    formData.append('action', 'map_change_password');
                    
                    fetch(MAP_AJAX_URL, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                msg.innerHTML = '<div class="map-notice map-success">' + data.data.message + '</div>';
                                form.reset();
                            } else {
                                msg.innerHTML = '<div class="map-notice map-error">' + data.data.message + '</div>';
                            }
                            btn.disabled = false;
                            btn.textContent = 'CHANGE PASSWORD';
                        });
                });
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChangePassword);
        } else {
            initChangePassword();
        }
    })();
    </script>
    
    <?php
    return ob_get_clean();
}

/**
 * Reset Password Shortcode
 */
function map_reset_password_shortcode() {
    if (is_user_logged_in()) {
        return '<div class="map-notice">You are already logged in.</div>';
    }
    
    $unique_id = 'map_reset_' . uniqid();
    $ajax_url = admin_url('admin-ajax.php');
    $logo = get_option('map_logo_url', '');
    
    ob_start();
    echo map_get_base_styles($unique_id);
    ?>
    
    <div class="map-wrapper-<?php echo $unique_id; ?>">
        <div class="map-container">
            <div class="map-card">
                <div class="map-left">
                    <div class="map-bg-pattern">
                        <div class="particle particle-1"></div>
                        <div class="particle particle-2"></div>
                        <div class="particle particle-3"></div>
                        <div class="particle particle-4"></div>
                        <div class="pattern-circle circle-1"></div>
                        <div class="pattern-circle circle-2"></div>
                        <div class="animated-line line-1"></div>
                    </div>
                    
                    <div class="map-content">
                        <?php if ($logo): ?>
                            <img src="<?php echo esc_url($logo); ?>" alt="Logo" class="map-logo">
                        <?php endif; ?>
                        <h1 class="map-title">Reset Password</h1>
                        <p class="map-subtitle">We'll send you a reset link</p>
                        <div class="map-decorative-line"></div>
                    </div>
                </div>
                
                <div class="map-right">
                    <div class="map-form-wrapper">
                        <h2>Forgot Password?</h2>
                        <div id="mapResetMsg_<?php echo $unique_id; ?>"></div>
                        <form id="mapResetForm_<?php echo $unique_id; ?>">
                            <?php wp_nonce_field('map_reset_password', 'map_reset_password_nonce'); ?>
                            <div class="map-field">
                                <label>Email Address</label>
                                <input type="email" name="email" required placeholder="your@email.com">
                            </div>
                            <button type="submit" class="map-btn">SEND RESET LINK</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        var uniqueId = <?php echo json_encode($unique_id); ?>;
        var MAP_AJAX_URL = <?php echo json_encode($ajax_url); ?>;
        
        function initReset() {
            var form = document.getElementById('mapResetForm_' + uniqueId);
            
            if (form && !form.dataset.init) {
                form.dataset.init = 'true';
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = this.querySelector('.map-btn');
                    var msg = document.getElementById('mapResetMsg_' + uniqueId);
                    btn.disabled = true;
                    btn.textContent = 'SENDING...';
                    
                    var formData = new FormData(this);
                    formData.append('action', 'map_reset_password');
                    
                    fetch(MAP_AJAX_URL, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                msg.innerHTML = '<div class="map-notice map-success">' + data.data.message + '</div>';
                                form.reset();
                            } else {
                                msg.innerHTML = '<div class="map-notice map-error">' + data.data.message + '</div>';
                            }
                            btn.disabled = false;
                            btn.textContent = 'SEND RESET LINK';
                        });
                });
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initReset);
        } else {
            initReset();
        }
    })();
    </script>
    
    <?php
    return ob_get_clean();
}

/**
 * Logout Shortcode
 */
function map_logout_shortcode() {
    if (!is_user_logged_in()) return '';
    return '<a href="' . wp_logout_url(home_url()) . '" style="display:inline-block;padding:13px 26px;background:#000000;color:#D4FF00;border-radius:10px;text-decoration:none;font-weight:700;transition:all 0.3s;box-shadow:0 5px 15px rgba(0,0,0,0.2);" onmouseover="this.style.transform=\'translateY(-3px)\';this.style.boxShadow=\'0 10px 25px rgba(0,0,0,0.3)\';" onmouseout="this.style.transform=\'translateY(0)\';this.style.boxShadow=\'0 5px 15px rgba(0,0,0,0.2)\';"> Logout</a>';
}

/**
 * Welcome Shortcode
 */
function map_welcome_shortcode() {
    if (!is_user_logged_in()) return '';
    $user = wp_get_current_user();
    return '<div style="background:#000000;color:#D4FF00;padding:20px 30px;border-radius:12px;border:2px solid #D4FF00;font-weight:700;font-size:18px;box-shadow:0 10px 30px rgba(212,255,0,0.2);">Welcome back, ' . esc_html($user->display_name) . '!</div>';
}

/**
 * Status Shortcode
 */
function map_status_shortcode() {
    if (is_user_logged_in()) {
        return '<span style="display:inline-block;padding:8px 16px;background:#4CAF50;color:white;border-radius:20px;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(76,175,80,0.3);">Logged In</span>';
    } else {
        return '<span style="display:inline-block;padding:8px 16px;background:#ff9800;color:white;border-radius:20px;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(255,152,0,0.3);">Not Logged In</span>';
    }
}
