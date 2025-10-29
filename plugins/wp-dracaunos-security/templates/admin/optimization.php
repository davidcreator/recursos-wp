<?php
if (!defined('ABSPATH')) exit;

// Processar formulário
if (isset($_POST['wpsp_save_optimization'])) {
    check_admin_referer('wpsp_optimization');
    
    $checkboxes = [
        'wpsp_remove_wp_version',
        'wpsp_remove_meta_generator',
        'wpsp_disable_emojis',
        'wpsp_minify_html',
        'wpsp_minify_css',
        'wpsp_minify_js',
        'wpsp_remove_feed_links',
        'wpsp_remove_rest_api_links',
        'wpsp_remove_oembed',
        'wpsp_remove_canonical'
    ];
    
    foreach ($checkboxes as $checkbox) {
        update_option($checkbox, isset($_POST[$checkbox]) ? 1 : 0);
    }
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'wp-security-pro') . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php _e('Optimization Settings', 'wp-security-pro'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('wpsp_optimization'); ?>
        
        <h2><?php _e('Header Cleanup', 'wp-security-pro'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('Remove WordPress Version', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_remove_wp_version" value="1" <?php checked(1, get_option('wpsp_remove_wp_version', 1)); ?> />
                            <?php _e('Remove WordPress version from header and feeds', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Remove Meta Generator', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_remove_meta_generator" value="1" <?php checked(1, get_option('wpsp_remove_meta_generator', 1)); ?> />
                            <?php _e('Remove generator meta tags', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Disable Emojis', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_disable_emojis" value="1" <?php checked(1, get_option('wpsp_disable_emojis', 1)); ?> />
                            <?php _e('Remove emoji scripts and styles', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Remove Feed Links', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_remove_feed_links" value="1" <?php checked(1, get_option('wpsp_remove_feed_links', 0)); ?> />
                            <?php _e('Remove RSS feed links from header', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Remove REST API Links', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_remove_rest_api_links" value="1" <?php checked(1, get_option('wpsp_remove_rest_api_links', 0)); ?> />
                            <?php _e('Remove REST API links from header', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Remove oEmbed', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_remove_oembed" value="1" <?php checked(1, get_option('wpsp_remove_oembed', 0)); ?> />
                            <?php _e('Remove oEmbed discovery links', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Remove Canonical Link', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_remove_canonical" value="1" <?php checked(1, get_option('wpsp_remove_canonical', 0)); ?> />
                            <?php _e('Remove canonical link tag (not recommended if using SEO plugins)', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php _e('Minification', 'wp-security-pro'); ?></h2>
        <div class="notice notice-info inline">
            <p><?php _e('Minification can improve load times but may cause issues with some themes/plugins. Test thoroughly after enabling.', 'wp-security-pro'); ?></p>
        </div>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('Minify HTML', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_minify_html" value="1" <?php checked(1, get_option('wpsp_minify_html', 0)); ?> />
                            <?php _e('Enable HTML minification', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Minify CSS', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_minify_css" value="1" <?php checked(1, get_option('wpsp_minify_css', 0)); ?> />
                            <?php _e('Enable inline CSS minification', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Minify JavaScript', 'wp-security-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="wpsp_minify_js" value="1" <?php checked(1, get_option('wpsp_minify_js', 0)); ?> />
                            <?php _e('Enable inline JavaScript minification', 'wp-security-pro'); ?>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(__('Save Optimization Settings', 'wp-security-pro'), 'primary', 'wpsp_save_optimization'); ?>
    </form>
</div>