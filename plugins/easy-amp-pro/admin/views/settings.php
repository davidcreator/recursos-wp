<?php
/**
 * Easy AMP Pro Settings View
 * 
 * Admin settings page for Easy AMP Pro plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit']) && wp_verify_nonce($_POST['easy_amp_pro_nonce'], 'easy_amp_pro_settings')) {
    $settings = array(
        'enable_amp' => isset($_POST['enable_amp']) ? 1 : 0,
        'amp_theme' => sanitize_text_field($_POST['amp_theme']),
        'enable_analytics' => isset($_POST['enable_analytics']) ? 1 : 0,
        'analytics_id' => sanitize_text_field($_POST['analytics_id']),
        'enable_adsense' => isset($_POST['enable_adsense']) ? 1 : 0,
        'adsense_publisher_id' => sanitize_text_field($_POST['adsense_publisher_id']),
        'enable_social_sharing' => isset($_POST['enable_social_sharing']) ? 1 : 0,
        'remove_incompatible' => isset($_POST['remove_incompatible']) ? 1 : 0,
        'optimize_images' => isset($_POST['optimize_images']) ? 1 : 0,
        'minify_css' => isset($_POST['minify_css']) ? 1 : 0,
        'cache_amp_pages' => isset($_POST['cache_amp_pages']) ? 1 : 0
    );
    
    update_option('easy_amp_pro_settings', $settings);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'easy-amp-pro') . '</p></div>';
}

// Get current settings
$settings = get_option('easy_amp_pro_settings', array());
$enable_amp = isset($settings['enable_amp']) ? $settings['enable_amp'] : 0;
$amp_theme = isset($settings['amp_theme']) ? $settings['amp_theme'] : 'default';
$enable_analytics = isset($settings['enable_analytics']) ? $settings['enable_analytics'] : 0;
$analytics_id = isset($settings['analytics_id']) ? $settings['analytics_id'] : '';
$enable_adsense = isset($settings['enable_adsense']) ? $settings['enable_adsense'] : 0;
$adsense_publisher_id = isset($settings['adsense_publisher_id']) ? $settings['adsense_publisher_id'] : '';
$enable_social_sharing = isset($settings['enable_social_sharing']) ? $settings['enable_social_sharing'] : 0;
$remove_incompatible = isset($settings['remove_incompatible']) ? $settings['remove_incompatible'] : 0;
$optimize_images = isset($settings['optimize_images']) ? $settings['optimize_images'] : 0;
$minify_css = isset($settings['minify_css']) ? $settings['minify_css'] : 0;
$cache_amp_pages = isset($settings['cache_amp_pages']) ? $settings['cache_amp_pages'] : 0;
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('easy_amp_pro_settings', 'easy_amp_pro_nonce'); ?>
        
        <div class="easy-amp-pro-settings">
            
            <!-- General Settings -->
            <div class="amp-settings-section">
                <h2><?php _e('General Settings', 'easy-amp-pro'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable AMP', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_amp" value="1" <?php checked($enable_amp, 1); ?> />
                                <?php _e('Enable AMP for your website', 'easy-amp-pro'); ?>
                            </label>
                            <p class="description"><?php _e('This will enable AMP (Accelerated Mobile Pages) functionality for your website.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('AMP Theme', 'easy-amp-pro'); ?></th>
                        <td>
                            <select name="amp_theme">
                                <option value="default" <?php selected($amp_theme, 'default'); ?>><?php _e('Default', 'easy-amp-pro'); ?></option>
                                <option value="minimal" <?php selected($amp_theme, 'minimal'); ?>><?php _e('Minimal', 'easy-amp-pro'); ?></option>
                                <option value="modern" <?php selected($amp_theme, 'modern'); ?>><?php _e('Modern', 'easy-amp-pro'); ?></option>
                                <option value="news" <?php selected($amp_theme, 'news'); ?>><?php _e('News', 'easy-amp-pro'); ?></option>
                            </select>
                            <p class="description"><?php _e('Choose the AMP theme style for your pages.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Analytics Settings -->
            <div class="amp-settings-section">
                <h2><?php _e('Analytics Settings', 'easy-amp-pro'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable Analytics', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_analytics" value="1" <?php checked($enable_analytics, 1); ?> />
                                <?php _e('Enable Google Analytics for AMP pages', 'easy-amp-pro'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Analytics ID', 'easy-amp-pro'); ?></th>
                        <td>
                            <input type="text" name="analytics_id" value="<?php echo esc_attr($analytics_id); ?>" class="regular-text" placeholder="UA-XXXXXXXX-X or G-XXXXXXXXXX" />
                            <p class="description"><?php _e('Enter your Google Analytics tracking ID.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- AdSense Settings -->
            <div class="amp-settings-section">
                <h2><?php _e('AdSense Settings', 'easy-amp-pro'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable AdSense', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_adsense" value="1" <?php checked($enable_adsense, 1); ?> />
                                <?php _e('Enable Google AdSense for AMP pages', 'easy-amp-pro'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('AdSense Publisher ID', 'easy-amp-pro'); ?></th>
                        <td>
                            <input type="text" name="adsense_publisher_id" value="<?php echo esc_attr($adsense_publisher_id); ?>" class="regular-text" placeholder="pub-XXXXXXXXXXXXXXXX" />
                            <p class="description"><?php _e('Enter your Google AdSense Publisher ID.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Social Settings -->
            <div class="amp-settings-section">
                <h2><?php _e('Social Settings', 'easy-amp-pro'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Social Sharing', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_social_sharing" value="1" <?php checked($enable_social_sharing, 1); ?> />
                                <?php _e('Enable social sharing buttons on AMP pages', 'easy-amp-pro'); ?>
                            </label>
                            <p class="description"><?php _e('This will add social sharing buttons for Facebook, Twitter, LinkedIn, and WhatsApp.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Optimization Settings -->
            <div class="amp-settings-section">
                <h2><?php _e('Optimization Settings', 'easy-amp-pro'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Remove Incompatible Elements', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="remove_incompatible" value="1" <?php checked($remove_incompatible, 1); ?> />
                                <?php _e('Automatically remove elements that are not AMP compatible', 'easy-amp-pro'); ?>
                            </label>
                            <p class="description"><?php _e('This will remove JavaScript, forms, and other elements that are not allowed in AMP.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Optimize Images', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="optimize_images" value="1" <?php checked($optimize_images, 1); ?> />
                                <?php _e('Convert images to amp-img and optimize for AMP', 'easy-amp-pro'); ?>
                            </label>
                            <p class="description"><?php _e('This will automatically convert img tags to amp-img and add required attributes.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Minify CSS', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="minify_css" value="1" <?php checked($minify_css, 1); ?> />
                                <?php _e('Minify CSS for better performance', 'easy-amp-pro'); ?>
                            </label>
                            <p class="description"><?php _e('This will compress CSS to reduce file size and improve loading speed.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Cache AMP Pages', 'easy-amp-pro'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="cache_amp_pages" value="1" <?php checked($cache_amp_pages, 1); ?> />
                                <?php _e('Enable caching for AMP pages', 'easy-amp-pro'); ?>
                            </label>
                            <p class="description"><?php _e('This will cache AMP pages for faster loading times.', 'easy-amp-pro'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>

<style>
.easy-amp-pro-settings {
    max-width: 800px;
}

.amp-settings-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.amp-settings-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    color: #333;
}

.form-table th {
    width: 200px;
    font-weight: 600;
}

.form-table td {
    padding-left: 20px;
}

.form-table input[type="text"],
.form-table select {
    width: 100%;
    max-width: 400px;
}

.form-table .description {
    margin-top: 5px;
    font-style: italic;
    color: #666;
}

.form-table label {
    display: flex;
    align-items: center;
    font-weight: normal;
}

.form-table input[type="checkbox"] {
    margin-right: 8px;
}
</style>