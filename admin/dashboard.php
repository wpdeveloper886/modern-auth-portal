<?php
/**
 * Admin Dashboard Page
 */

if (!defined('WPINC')) {
    die;
}

function map_dashboard_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'map_login_history';
    $total_users = count(get_users());
    $total_logins = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    
    // Last 24 hours logins
    $last_24h = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE login_time >= %s",
        date('Y-m-d H:i:s', strtotime('-24 hours'))
    ));
    
    // Last 7 days logins
    $last_7days = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE login_time >= %s",
        date('Y-m-d H:i:s', strtotime('-7 days'))
    ));
    
    // Recent logins
    $recent_logins = $wpdb->get_results(
        "SELECT h.*, u.display_name, u.user_email 
        FROM $table_name h 
        LEFT JOIN {$wpdb->users} u ON h.user_id = u.ID 
        ORDER BY h.login_time DESC 
        LIMIT 10"
    );
    
    // Browser stats
    $browser_stats = $wpdb->get_results(
        "SELECT browser, COUNT(*) as count 
        FROM $table_name 
        GROUP BY browser 
        ORDER BY count DESC 
        LIMIT 5"
    );
    
    // Device stats
    $device_stats = $wpdb->get_results(
        "SELECT device, COUNT(*) as count 
        FROM $table_name 
        GROUP BY device 
        ORDER BY count DESC"
    );
    
    ?>
    <div class="wrap map-dashboard">
        <h1>Auth Portal Dashboard</h1>
        
        <div class="map-stats-grid">
            <div class="map-stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            
            <div class="map-stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $total_logins; ?></div>
                    <div class="stat-label">Total Logins</div>
                </div>
            </div>
            
            <div class="map-stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $last_24h; ?></div>
                    <div class="stat-label">Last 24 Hours</div>
                </div>
            </div>
            
            <div class="map-stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $last_7days; ?></div>
                    <div class="stat-label">Last 7 Days</div>
                </div>
            </div>
        </div>
        
        <div class="map-dashboard-row">
            <div class="map-dashboard-card" style="flex: 2;">
                <h2>Recent Login Activity</h2>
                <div class="map-table-wrapper">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Time</th>
                                <th>IP Address</th>
                                <th>Browser</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_logins): ?>
                                <?php foreach ($recent_logins as $login): ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($login->display_name); ?></strong></td>
                                        <td><?php echo esc_html($login->user_email); ?></td>
                                        <td><?php echo human_time_diff(strtotime($login->login_time), current_time('timestamp')) . ' ago'; ?></td>
                                        <td><code><?php echo esc_html($login->ip_address); ?></code></td>
                                        <td><span class="map-badge"><?php echo esc_html($login->browser); ?></span></td>
                                        <td><span class="map-badge map-badge-device"><?php echo esc_html($login->device); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;">No login history yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="map-dashboard-card" style="flex: 1;">
                <h2>Browser Stats</h2>
                <div class="map-stats-list">
                    <?php if ($browser_stats): ?>
                        <?php foreach ($browser_stats as $stat): ?>
                            <div class="map-stat-item">
                                <span class="stat-name"><?php echo esc_html($stat->browser); ?></span>
                                <span class="stat-value"><?php echo $stat->count; ?> logins</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No data available</p>
                    <?php endif; ?>
                </div>
                
                <h2 style="margin-top: 30px;">Device Stats</h2>
                <div class="map-stats-list">
                    <?php if ($device_stats): ?>
                        <?php foreach ($device_stats as $stat): ?>
                            <div class="map-stat-item">
                                <span class="stat-name"><?php echo esc_html($stat->device); ?></span>
                                <span class="stat-value"><?php echo $stat->count; ?> logins</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .map-dashboard {
        max-width: 1400px;
        margin: 20px 0;
    }
    
    .map-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .map-stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .map-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 14px;
        color: #64748b;
        margin-top: 5px;
        font-weight: 500;
    }
    
    .map-dashboard-row {
        display: flex;
        gap: 20px;
        margin-top: 30px;
    }
    
    .map-dashboard-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .map-dashboard-card h2 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #1e293b;
        font-weight: 700;
    }
    
    .map-table-wrapper {
        overflow-x: auto;
    }
    
    .map-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .map-badge-device {
        background: #dcfce7;
        color: #15803d;
    }
    
    .map-stats-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .map-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 3px solid #d4ff00;
    }
    
    .stat-name {
        font-weight: 600;
        color: #334155;
    }
    
    .stat-value {
        color: #64748b;
        font-size: 14px;
    }
    
    @media (max-width: 1024px) {
        .map-dashboard-row {
            flex-direction: column;
        }
    }
    </style>
    <?php
}
