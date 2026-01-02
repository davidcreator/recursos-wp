<?php
if (!defined('ABSPATH')) exit;
use WPSP\Core\Database;
$db = new Database();
$total_logs = $db->count_security_logs();
$today = date('Y-m-d');
$logs_today = $db->count_security_logs(['start_date' => $today . ' 00:00:00', 'end_date' => $today . ' 23:59:59']);
$recent = $db->get_security_logs(['limit' => 10]);
?>
<div class="wrap">
    <h1><?php _e('Dracaunos Security Dashboard', 'wp-dracaunos-security'); ?></h1>
    <h2><?php _e('Statistics', 'wp-dracaunos-security'); ?></h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th><?php _e('Total Logs', 'wp-dracaunos-security'); ?></th>
                <td><?php echo intval($total_logs); ?></td>
            </tr>
            <tr>
                <th><?php _e('Logs Today', 'wp-dracaunos-security'); ?></th>
                <td><?php echo intval($logs_today); ?></td>
            </tr>
        </tbody>
    </table>
    <h2><?php _e('Recent Security Logs', 'wp-dracaunos-security'); ?></h2>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Date', 'wp-dracaunos-security'); ?></th>
                <th><?php _e('Action', 'wp-dracaunos-security'); ?></th>
                <th><?php _e('User', 'wp-dracaunos-security'); ?></th>
                <th><?php _e('IP', 'wp-dracaunos-security'); ?></th>
                <th><?php _e('Details', 'wp-dracaunos-security'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recent)): ?>
                <?php foreach ($recent as $log): ?>
                    <tr>
                        <td><?php echo esc_html($log->created_at); ?></td>
                        <td><?php echo esc_html($log->action); ?></td>
                        <td><?php echo $log->user_id ? esc_html(get_userdata($log->user_id)->user_login) : '-'; ?></td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                        <td><?php echo esc_html($log->details); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5"><?php _e('No logs found.', 'wp-dracaunos-security'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
