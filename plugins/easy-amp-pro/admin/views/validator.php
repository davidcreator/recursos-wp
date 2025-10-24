<?php
/**
 * Easy AMP Pro Validator View
 * 
 * Admin validator page for Easy AMP Pro plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle validation request
$validation_results = array();
$validation_url = '';

if (isset($_POST['validate_url']) && wp_verify_nonce($_POST['easy_amp_pro_validator_nonce'], 'easy_amp_pro_validator')) {
    $validation_url = esc_url_raw($_POST['validation_url']);
    
    if (!empty($validation_url)) {
        // Initialize validator
        $validator = new EasyAMPPro_Validator();
        $validation_results = $validator->validate_page($validation_url);
    }
}

// Get recent validation results
$recent_validations = get_option('easy_amp_pro_recent_validations', array());
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="easy-amp-pro-validator">
        
        <!-- Validation Form -->
        <div class="amp-validator-form">
            <h2><?php _e('Validate AMP Page', 'easy-amp-pro'); ?></h2>
            <form method="post" action="">
                <?php wp_nonce_field('easy_amp_pro_validator', 'easy_amp_pro_validator_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Page URL', 'easy-amp-pro'); ?></th>
                        <td>
                            <input type="url" name="validation_url" value="<?php echo esc_attr($validation_url); ?>" class="regular-text" placeholder="https://example.com/page?amp=1" required />
                            <p class="description"><?php _e('Enter the AMP page URL you want to validate.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('Validate Page', 'easy-amp-pro'), 'primary', 'validate_url'); ?>
            </form>
        </div>
        
        <?php if (!empty($validation_results)) : ?>
        <!-- Validation Results -->
        <div class="amp-validation-results">
            <h2><?php _e('Validation Results', 'easy-amp-pro'); ?></h2>
            
            <div class="amp-validation-summary">
                <div class="amp-validation-status <?php echo $validation_results['is_valid'] ? 'valid' : 'invalid'; ?>">
                    <span class="amp-status-icon">
                        <?php if ($validation_results['is_valid']) : ?>
                            <span class="dashicons dashicons-yes-alt"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-dismiss"></span>
                        <?php endif; ?>
                    </span>
                    <span class="amp-status-text">
                        <?php echo $validation_results['is_valid'] ? __('Valid AMP Page', 'easy-amp-pro') : __('Invalid AMP Page', 'easy-amp-pro'); ?>
                    </span>
                </div>
                
                <div class="amp-validation-stats">
                    <div class="amp-stat">
                        <span class="amp-stat-number"><?php echo count($validation_results['errors']); ?></span>
                        <span class="amp-stat-label"><?php _e('Errors', 'easy-amp-pro'); ?></span>
                    </div>
                    <div class="amp-stat">
                        <span class="amp-stat-number"><?php echo count($validation_results['warnings']); ?></span>
                        <span class="amp-stat-label"><?php _e('Warnings', 'easy-amp-pro'); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($validation_results['errors'])) : ?>
            <div class="amp-validation-errors">
                <h3><?php _e('Errors', 'easy-amp-pro'); ?></h3>
                <div class="amp-validation-list">
                    <?php foreach ($validation_results['errors'] as $error) : ?>
                    <div class="amp-validation-item error">
                        <span class="amp-validation-icon">
                            <span class="dashicons dashicons-dismiss"></span>
                        </span>
                        <div class="amp-validation-content">
                            <h4><?php echo esc_html($error['message']); ?></h4>
                            <?php if (!empty($error['line'])) : ?>
                                <p class="amp-validation-line"><?php printf(__('Line: %d', 'easy-amp-pro'), $error['line']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($error['code'])) : ?>
                                <pre class="amp-validation-code"><?php echo esc_html($error['code']); ?></pre>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($validation_results['warnings'])) : ?>
            <div class="amp-validation-warnings">
                <h3><?php _e('Warnings', 'easy-amp-pro'); ?></h3>
                <div class="amp-validation-list">
                    <?php foreach ($validation_results['warnings'] as $warning) : ?>
                    <div class="amp-validation-item warning">
                        <span class="amp-validation-icon">
                            <span class="dashicons dashicons-warning"></span>
                        </span>
                        <div class="amp-validation-content">
                            <h4><?php echo esc_html($warning['message']); ?></h4>
                            <?php if (!empty($warning['line'])) : ?>
                                <p class="amp-validation-line"><?php printf(__('Line: %d', 'easy-amp-pro'), $warning['line']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($warning['code'])) : ?>
                                <pre class="amp-validation-code"><?php echo esc_html($warning['code']); ?></pre>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($validation_results['is_valid']) : ?>
            <div class="amp-validation-success">
                <h3><?php _e('Validation Passed', 'easy-amp-pro'); ?></h3>
                <p><?php _e('Congratulations! Your AMP page is valid and follows all AMP specifications.', 'easy-amp-pro'); ?></p>
                
                <div class="amp-validation-tips">
                    <h4><?php _e('Performance Tips', 'easy-amp-pro'); ?></h4>
                    <ul>
                        <li><?php _e('Optimize images for faster loading', 'easy-amp-pro'); ?></li>
                        <li><?php _e('Minimize CSS to reduce file size', 'easy-amp-pro'); ?></li>
                        <li><?php _e('Use AMP components for better performance', 'easy-amp-pro'); ?></li>
                        <li><?php _e('Enable caching for faster page loads', 'easy-amp-pro'); ?></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Quick Validation Tools -->
        <div class="amp-quick-validation">
            <h2><?php _e('Quick Validation Tools', 'easy-amp-pro'); ?></h2>
            <div class="amp-validation-tools">
                <div class="amp-tool">
                    <h3><?php _e('Validate Current Posts', 'easy-amp-pro'); ?></h3>
                    <p><?php _e('Validate all published posts with AMP enabled.', 'easy-amp-pro'); ?></p>
                    <button type="button" class="button button-secondary" onclick="validateAllPosts()">
                        <?php _e('Validate All Posts', 'easy-amp-pro'); ?>
                    </button>
                </div>
                
                <div class="amp-tool">
                    <h3><?php _e('Validate Homepage', 'easy-amp-pro'); ?></h3>
                    <p><?php _e('Validate your homepage AMP version.', 'easy-amp-pro'); ?></p>
                    <button type="button" class="button button-secondary" onclick="validateHomepage()">
                        <?php _e('Validate Homepage', 'easy-amp-pro'); ?>
                    </button>
                </div>
                
                <div class="amp-tool">
                    <h3><?php _e('Google AMP Test', 'easy-amp-pro'); ?></h3>
                    <p><?php _e('Test your AMP pages with Google\'s official AMP test tool.', 'easy-amp-pro'); ?></p>
                    <a href="https://search.google.com/test/amp" target="_blank" class="button button-secondary">
                        <?php _e('Open Google AMP Test', 'easy-amp-pro'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($recent_validations)) : ?>
        <!-- Recent Validations -->
        <div class="amp-recent-validations">
            <h2><?php _e('Recent Validations', 'easy-amp-pro'); ?></h2>
            <div class="amp-validation-history">
                <?php foreach (array_slice($recent_validations, 0, 10) as $validation) : ?>
                <div class="amp-validation-history-item">
                    <div class="amp-validation-history-status <?php echo $validation['is_valid'] ? 'valid' : 'invalid'; ?>">
                        <?php if ($validation['is_valid']) : ?>
                            <span class="dashicons dashicons-yes-alt"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-dismiss"></span>
                        <?php endif; ?>
                    </div>
                    <div class="amp-validation-history-content">
                        <h4><a href="<?php echo esc_url($validation['url']); ?>" target="_blank"><?php echo esc_html($validation['url']); ?></a></h4>
                        <p><?php printf(__('Validated: %s', 'easy-amp-pro'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $validation['timestamp'])); ?></p>
                    </div>
                    <div class="amp-validation-history-stats">
                        <span class="amp-error-count"><?php printf(__('%d errors', 'easy-amp-pro'), $validation['error_count']); ?></span>
                        <span class="amp-warning-count"><?php printf(__('%d warnings', 'easy-amp-pro'), $validation['warning_count']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<style>
.easy-amp-pro-validator {
    max-width: 1000px;
}

.amp-validator-form,
.amp-validation-results,
.amp-quick-validation,
.amp-recent-validations {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.amp-validation-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 20px;
}

.amp-validation-status {
    display: flex;
    align-items: center;
}

.amp-validation-status.valid {
    color: #46b450;
}

.amp-validation-status.invalid {
    color: #dc3232;
}

.amp-status-icon {
    margin-right: 10px;
    font-size: 24px;
}

.amp-status-text {
    font-size: 18px;
    font-weight: bold;
}

.amp-validation-stats {
    display: flex;
    gap: 20px;
}

.amp-stat {
    text-align: center;
}

.amp-stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.amp-stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.amp-validation-list {
    space-y: 15px;
}

.amp-validation-item {
    display: flex;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 10px;
}

.amp-validation-item.error {
    border-left: 4px solid #dc3232;
    background: #fef7f7;
}

.amp-validation-item.warning {
    border-left: 4px solid #ffb900;
    background: #fffbf0;
}

.amp-validation-icon {
    margin-right: 15px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.amp-validation-item.error .amp-validation-icon {
    color: #dc3232;
}

.amp-validation-item.warning .amp-validation-icon {
    color: #ffb900;
}

.amp-validation-content {
    flex: 1;
}

.amp-validation-content h4 {
    margin: 0 0 5px 0;
    color: #333;
}

.amp-validation-line {
    margin: 5px 0;
    font-size: 12px;
    color: #666;
}

.amp-validation-code {
    background: #f1f1f1;
    padding: 10px;
    border-radius: 4px;
    font-size: 12px;
    overflow-x: auto;
    margin: 10px 0 0 0;
}

.amp-validation-success {
    background: #f0f8f0;
    border: 1px solid #46b450;
    border-radius: 6px;
    padding: 20px;
}

.amp-validation-tips ul {
    margin: 10px 0;
    padding-left: 20px;
}

.amp-validation-tools {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.amp-tool {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #f8f9fa;
}

.amp-tool h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.amp-tool p {
    margin: 0 0 15px 0;
    color: #666;
}

.amp-validation-history-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.amp-validation-history-item:last-child {
    border-bottom: none;
}

.amp-validation-history-status {
    margin-right: 15px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.amp-validation-history-status.valid {
    background: #46b450;
    color: white;
}

.amp-validation-history-status.invalid {
    background: #dc3232;
    color: white;
}

.amp-validation-history-content {
    flex: 1;
}

.amp-validation-history-content h4 {
    margin: 0 0 5px 0;
}

.amp-validation-history-content p {
    margin: 0;
    font-size: 12px;
    color: #666;
}

.amp-validation-history-stats {
    display: flex;
    gap: 10px;
    font-size: 12px;
}

.amp-error-count {
    color: #dc3232;
}

.amp-warning-count {
    color: #ffb900;
}
</style>

<script>
function validateAllPosts() {
    if (confirm('<?php _e('This will validate all published posts. This may take a while. Continue?', 'easy-amp-pro'); ?>')) {
        // Implementation for bulk validation
        alert('<?php _e('Bulk validation feature coming soon!', 'easy-amp-pro'); ?>');
    }
}

function validateHomepage() {
    var homepageUrl = '<?php echo esc_js(home_url('/?amp=1')); ?>';
    document.querySelector('input[name="validation_url"]').value = homepageUrl;
    document.querySelector('form').submit();
}
</script>