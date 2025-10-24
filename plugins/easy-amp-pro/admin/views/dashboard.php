<?php
/**
 * Easy AMP Pro Dashboard View
 * 
 * Admin dashboard page for Easy AMP Pro plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get plugin settings
$settings = get_option('easy_amp_pro_settings', array());
$amp_enabled = isset($settings['enable_amp']) ? $settings['enable_amp'] : false;

// Get AMP statistics
$total_posts = wp_count_posts()->publish;
$amp_posts = get_posts(array(
    'post_type' => 'any',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_amp_enabled',
            'value' => '1',
            'compare' => '='
        )
    ),
    'fields' => 'ids'
));
$amp_posts_count = count($amp_posts);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="easy-amp-pro-dashboard">
        
        <!-- Status Cards -->
        <div class="amp-status-cards">
            <div class="amp-card">
                <div class="amp-card-icon">
                    <span class="dashicons dashicons-smartphone"></span>
                </div>
                <div class="amp-card-content">
                    <h3><?php _e('AMP Status', 'easy-amp-pro'); ?></h3>
                    <p class="amp-status <?php echo $amp_enabled ? 'enabled' : 'disabled'; ?>">
                        <?php echo $amp_enabled ? __('Enabled', 'easy-amp-pro') : __('Disabled', 'easy-amp-pro'); ?>
                    </p>
                </div>
            </div>
            
            <div class="amp-card">
                <div class="amp-card-icon">
                    <span class="dashicons dashicons-admin-post"></span>
                </div>
                <div class="amp-card-content">
                    <h3><?php _e('Total Posts', 'easy-amp-pro'); ?></h3>
                    <p class="amp-number"><?php echo number_format($total_posts); ?></p>
                </div>
            </div>
            
            <div class="amp-card">
                <div class="amp-card-icon">
                    <span class="dashicons dashicons-performance"></span>
                </div>
                <div class="amp-card-content">
                    <h3><?php _e('AMP Posts', 'easy-amp-pro'); ?></h3>
                    <p class="amp-number"><?php echo number_format($amp_posts_count); ?></p>
                </div>
            </div>
            
            <div class="amp-card">
                <div class="amp-card-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="amp-card-content">
                    <h3><?php _e('Coverage', 'easy-amp-pro'); ?></h3>
                    <p class="amp-percentage">
                        <?php echo $total_posts > 0 ? round(($amp_posts_count / $total_posts) * 100, 1) : 0; ?>%
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="amp-quick-actions">
            <h2><?php _e('Quick Actions', 'easy-amp-pro'); ?></h2>
            <div class="amp-actions-grid">
                <a href="<?php echo admin_url('admin.php?page=easy-amp-pro-settings'); ?>" class="amp-action-button">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e('Settings', 'easy-amp-pro'); ?>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=easy-amp-pro-validator'); ?>" class="amp-action-button">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e('Validate AMP', 'easy-amp-pro'); ?>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=easy-amp-pro-optimizer'); ?>" class="amp-action-button">
                    <span class="dashicons dashicons-performance"></span>
                    <?php _e('Optimize', 'easy-amp-pro'); ?>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=easy-amp-pro-templates'); ?>" class="amp-action-button">
                    <span class="dashicons dashicons-layout"></span>
                    <?php _e('Templates', 'easy-amp-pro'); ?>
                </a>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="amp-recent-activity">
            <h2><?php _e('Recent AMP Activity', 'easy-amp-pro'); ?></h2>
            <div class="amp-activity-list">
                <?php
                // Get recent AMP posts
                $recent_amp_posts = get_posts(array(
                    'post_type' => 'any',
                    'posts_per_page' => 5,
                    'meta_query' => array(
                        array(
                            'key' => '_amp_enabled',
                            'value' => '1',
                            'compare' => '='
                        )
                    ),
                    'orderby' => 'modified',
                    'order' => 'DESC'
                ));
                
                if (!empty($recent_amp_posts)) :
                    foreach ($recent_amp_posts as $post) :
                        $amp_url = get_permalink($post->ID) . '?amp=1';
                ?>
                    <div class="amp-activity-item">
                        <div class="amp-activity-icon">
                            <span class="dashicons dashicons-admin-post"></span>
                        </div>
                        <div class="amp-activity-content">
                            <h4><a href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a></h4>
                            <p><?php printf(__('Modified: %s', 'easy-amp-pro'), get_the_modified_date('', $post->ID)); ?></p>
                        </div>
                        <div class="amp-activity-actions">
                            <a href="<?php echo esc_url($amp_url); ?>" target="_blank" class="button button-small">
                                <?php _e('View AMP', 'easy-amp-pro'); ?>
                            </a>
                        </div>
                    </div>
                <?php
                    endforeach;
                else :
                ?>
                    <div class="amp-no-activity">
                        <p><?php _e('No AMP posts found. Enable AMP for your posts to see activity here.', 'easy-amp-pro'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- AMP Validation Status -->
        <div class="amp-validation-status">
            <h2><?php _e('AMP Validation Status', 'easy-amp-pro'); ?></h2>
            <div class="amp-validation-summary">
                <div class="amp-validation-item">
                    <span class="amp-validation-icon valid">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </span>
                    <span class="amp-validation-text"><?php _e('Valid AMP Pages', 'easy-amp-pro'); ?></span>
                    <span class="amp-validation-count"><?php echo $amp_posts_count; ?></span>
                </div>
                
                <div class="amp-validation-item">
                    <span class="amp-validation-icon warning">
                        <span class="dashicons dashicons-warning"></span>
                    </span>
                    <span class="amp-validation-text"><?php _e('Pages with Warnings', 'easy-amp-pro'); ?></span>
                    <span class="amp-validation-count">0</span>
                </div>
                
                <div class="amp-validation-item">
                    <span class="amp-validation-icon error">
                        <span class="dashicons dashicons-dismiss"></span>
                    </span>
                    <span class="amp-validation-text"><?php _e('Invalid AMP Pages', 'easy-amp-pro'); ?></span>
                    <span class="amp-validation-count">0</span>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
.easy-amp-pro-dashboard {
    max-width: 1200px;
}

.amp-status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.amp-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.amp-card-icon {
    margin-right: 15px;
    font-size: 24px;
    color: #0073aa;
}

.amp-card-content h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #666;
}

.amp-card-content .amp-number,
.amp-card-content .amp-percentage {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.amp-status.enabled {
    color: #46b450;
    font-weight: bold;
}

.amp-status.disabled {
    color: #dc3232;
    font-weight: bold;
}

.amp-quick-actions,
.amp-recent-activity,
.amp-validation-status {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.amp-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.amp-action-button {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.amp-action-button:hover {
    background: #e9ecef;
    border-color: #0073aa;
    color: #0073aa;
}

.amp-action-button .dashicons {
    margin-right: 10px;
    font-size: 18px;
}

.amp-activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.amp-activity-item:last-child {
    border-bottom: none;
}

.amp-activity-icon {
    margin-right: 15px;
    color: #0073aa;
}

.amp-activity-content {
    flex: 1;
}

.amp-activity-content h4 {
    margin: 0 0 5px 0;
}

.amp-activity-content p {
    margin: 0;
    color: #666;
    font-size: 13px;
}

.amp-validation-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.amp-validation-item:last-child {
    border-bottom: none;
}

.amp-validation-icon {
    margin-right: 15px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.amp-validation-icon.valid {
    background: #46b450;
    color: white;
}

.amp-validation-icon.warning {
    background: #ffb900;
    color: white;
}

.amp-validation-icon.error {
    background: #dc3232;
    color: white;
}

.amp-validation-text {
    flex: 1;
}

.amp-validation-count {
    font-weight: bold;
    color: #333;
}

.amp-no-activity {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}
</style>