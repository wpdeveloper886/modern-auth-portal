<?php
/**
 * Admin Login History Page
 */

if (!defined('WPINC')) {
    die;
}

function map_login_history_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'map_login_history';
    
    // Pagination
    $per_page = 50;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Filters
    $where = "1=1";
    $filter_user = isset($_GET['filter_user']) ? intval($_GET['filter_user']) : 0;
    $filter_days = isset($_GET['filter_days']) ? intval($_GET['filter_days']) : 0;
    
    if ($filter_user > 0) {
        $where .= " AND h.user_id = " . $filter_user;
    }
    
    if ($filter_days > 0) {
        $date = date('Y-m-d H:i:s', strtotime("-{$filter_days} days"));
        $where .= $wpdb->prepare(" AND h.login_time >= %s", $date);
    }
    
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name h WHERE $where");
    $total_pages = ceil($total_items / $per_page);
    
    $logins = $wpdb->get_results(
        "SELECT h.*, u.display_name, u.user_email 
        FROM $table_name h 
        LEFT JOIN {$wpdb->users} u ON h.user_id = u.ID 
        WHERE $where
        ORDER BY h.login_time DESC 
        LIMIT $per_page OFFSET $offset"
    );
    
    $all_users = get_users(['fields' => ['ID', 'display_name']]);
    
    ?>
    <div class="wrap">
        <h1>Complete Login History</h1>
        
        <div class="map-filters" style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="get" action="" style="display: flex; gap: 10px; flex: 1; align-items: center;">
                <input type="hidden" name="page" value="map-login-history">
                
                <select name="filter_user" style="min-width: 200px;">
                    <option value="0">All Users</option>
                    <?php foreach ($all_users as $u): ?>
                        <option value="<?php echo $u->ID; ?>" <?php selected($filter_user, $u->ID); ?>>
                            <?php echo esc_html($u->display_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="filter_days" style="min-width: 150px;">
                    <option value="0">All Time</option>
                    <option value="1" <?php selected($filter_days, 1); ?>>Last 24 Hours</option>
                    <option value="7" <?php selected($filter_days, 7); ?>>Last 7 Days</option>
                    <option value="30" <?php selected($filter_days, 30); ?>>Last 30 Days</option>
                    <option value="90" <?php selected($filter_days, 90); ?>>Last 90 Days</option>
                </select>
                
                <button type="submit" class="button button-primary">Filter</button>
                <a href="?page=map-login-history" class="button">Reset</a>
            </form>
            
            <div style="margin-left: auto;">
                <button type="button" id="deleteSelectedBtn" class="button button-secondary" style="background: #dc3545; color: white; border: none;" disabled>
                    Delete Selected
                </button>
            </div>
        </div>
        
        <div class="map-table-card" style="background: white; padding: 20px; border-radius: 8px;">
            <form id="loginHistoryForm">
                <table class="wp-list-table widefat fixed striped" id="loginHistoryTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th style="width: 60px;">ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Login Time</th>
                            <th>IP Address</th>
                            <th>Browser</th>
                            <th>Device</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logins): ?>
                            <?php foreach ($logins as $login): ?>
                                <tr data-id="<?php echo $login->id; ?>">
                                    <td>
                                        <input type="checkbox" class="row-checkbox" value="<?php echo $login->id; ?>">
                                    </td>
                                    <td><?php echo $login->id; ?></td>
                                    <td><strong><?php echo esc_html($login->display_name); ?></strong></td>
                                    <td><?php echo esc_html($login->user_email); ?></td>
                                    <td>
                                        <?php echo date('M d, Y h:i A', strtotime($login->login_time)); ?>
                                        <br>
                                        <small style="color: #666;"><?php echo human_time_diff(strtotime($login->login_time), current_time('timestamp')) . ' ago'; ?></small>
                                    </td>
                                    <td><code><?php echo esc_html($login->ip_address); ?></code></td>
                                    <td><span class="map-badge"><?php echo esc_html($login->browser); ?></span></td>
                                    <td><span class="map-badge map-badge-device"><?php echo esc_html($login->device); ?></span></td>
                                    <td>
                                        <button type="button" class="button button-small delete-single" data-id="<?php echo $login->id; ?>" style="color: #dc3545;">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" style="text-align:center;">No login history found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
            
            <?php if ($total_pages > 1): ?>
                <div class="tablenav" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo number_format($total_items); ?> items | Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                    </div>
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links([
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo; Previous',
                            'next_text' => 'Next &raquo;',
                            'total' => $total_pages,
                            'current' => $current_page
                        ]);
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <p style="margin-top: 20px; color: #666;">
            <strong>Total Records:</strong> <?php echo number_format($total_items); ?> logins
        </p>
    </div>
    
    <style>
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
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Select all checkbox
        $('#selectAll').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
            updateDeleteButton();
        });
        
        // Individual checkbox
        $('.row-checkbox').on('change', function() {
            updateDeleteButton();
            $('#selectAll').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox').length);
        });
        
        // Update delete button state
        function updateDeleteButton() {
            var checked = $('.row-checkbox:checked').length;
            $('#deleteSelectedBtn').prop('disabled', checked === 0);
            if (checked > 0) {
                $('#deleteSelectedBtn').text('Delete Selected (' + checked + ')');
            } else {
                $('#deleteSelectedBtn').text('Delete Selected');
            }
        }
        
        // Delete selected records
        $('#deleteSelectedBtn').on('click', function() {
            var ids = [];
            $('.row-checkbox:checked').each(function() {
                ids.push($(this).val());
            });
            
            if (ids.length === 0) return;
            
            if (!confirm('Are you sure you want to delete ' + ids.length + ' record(s)?')) return;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'map_delete_login_history',
                    nonce: '<?php echo wp_create_nonce("map_delete_history"); ?>',
                    ids: ids
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        });
        
        // Delete single record
        $('.delete-single').on('click', function() {
            var id = $(this).data('id');
            
            if (!confirm('Are you sure you want to delete this record?')) return;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'map_delete_login_history',
                    nonce: '<?php echo wp_create_nonce("map_delete_history"); ?>',
                    ids: [id]
                },
                success: function(response) {
                    if (response.success) {
                        $('tr[data-id="' + id + '"]').fadeOut(300, function() {
                            $(this).remove();
                            updateDeleteButton();
                        });
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        });
    });
    </script>
    <?php
}
