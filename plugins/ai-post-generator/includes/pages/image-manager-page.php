<?php
/**
 * Página de Gerenciamento de Imagens
 * 
 * Gerencia imagens geradas pela IA, exibe galeria, estatísticas e opções de download/exclusão.
 * CSS e JS são enfileirados em class-ai-post-generator.php
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Obter valores salvos
$image_provider = get_option('aipg_image_provider', 'pollinations');
$image_width = get_option('aipg_image_width', 1920);
$image_height = get_option('aipg_image_height', 1080);

// Obter estatísticas
$total_images = $wpdb->get_var(
    "SELECT COUNT(*)
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
    WHERE p.post_type = 'attachment'
    AND pm.meta_key = '_aipg_generated_image'
    AND pm.meta_value = '1'"
);

$this_month_images = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = 'attachment'
        AND pm.meta_key = '_aipg_generated_image'
        AND pm.meta_value = '1'
        AND MONTH(p.post_date) = %d
        AND YEAR(p.post_date) = %d",
        date('n'),
        date('Y')
    )
);

$provider_stats = $wpdb->get_results(
    "SELECT pm2.meta_value as provider, COUNT(*) as count
    FROM {$wpdb->postmeta} pm
    INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
    WHERE pm.meta_key = '_aipg_generated_image'
    AND pm.meta_value = '1'
    AND pm2.meta_key = '_aipg_image_provider'
    GROUP BY pm2.meta_value
    ORDER BY count DESC"
);

// Paginação
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 15;
$offset = ($current_page - 1) * $per_page;

// Obter imagens
$images_query = $wpdb->prepare(
    "SELECT p.ID, p.post_title, p.post_date, pm.meta_value as provider
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
    WHERE p.post_type = 'attachment'
    AND pm.meta_key = '_aipg_generated_image'
    AND pm.meta_value = '1'
    ORDER BY p.post_date DESC
    LIMIT %d OFFSET %d",
    $per_page,
    $offset
);

$images = $wpdb->get_results($images_query);
$total_pages = ceil($total_images / $per_page);
?>

<div class="wrap aipg-wrap">
    <h1>
        <span class="dashicons dashicons-format-gallery" style="vertical-align: middle;"></span>
        <?php echo esc_html__('Gerenciar Imagens - IA', 'ai-post-generator'); ?>
    </h1>

    <!-- Estatísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Card: Total de Imagens -->
        <div class="aipg-form-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                        <?php echo esc_html__('Total de Imagens', 'ai-post-generator'); ?>
                    </p>
                    <h2 style="margin: 10px 0 0 0; color: white;">
                        <?php echo number_format_i18n($total_images); ?>
                    </h2>
                </div>
                <span class="dashicons dashicons-format-gallery" style="font-size: 40px; opacity: 0.7;"></span>
            </div>
        </div>

        <!-- Card: Este Mês -->
        <div class="aipg-form-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                        <?php echo esc_html__('Este Mês', 'ai-post-generator'); ?>
                    </p>
                    <h2 style="margin: 10px 0 0 0; color: white;">
                        <?php echo number_format_i18n($this_month_images); ?>
                    </h2>
                </div>
                <span class="dashicons dashicons-calendar" style="font-size: 40px; opacity: 0.7;"></span>
            </div>
        </div>

        <!-- Card: Provedor Mais Usado -->
        <div class="aipg-form-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                        <?php echo esc_html__('Provedor Mais Usado', 'ai-post-generator'); ?>
                    </p>
                    <h2 style="margin: 10px 0 0 0; color: white; font-size: 18px;">
                        <?php 
                        if (!empty($provider_stats)) {
                            echo esc_html(ucfirst($provider_stats[0]->provider));
                        } else {
                            echo esc_html__('N/A', 'ai-post-generator');
                        }
                        ?>
                    </h2>
                </div>
                <span class="dashicons dashicons-star-filled" style="font-size: 40px; opacity: 0.7;"></span>
            </div>
        </div>

        <!-- Card: Dimensões -->
        <div class="aipg-form-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: #333;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; opacity: 0.8; font-size: 14px;">
                        <?php echo esc_html__('Dimensões Padrão', 'ai-post-generator'); ?>
                    </p>
                    <h2 style="margin: 10px 0 0 0; color: #333; font-size: 18px;">
                        <?php echo esc_html($image_width . '×' . $image_height); ?>
                    </h2>
                </div>
                <span class="dashicons dashicons-format-image" style="font-size: 40px; opacity: 0.7;"></span>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="aipg-form-card" style="margin-bottom: 30px;">
        <h2><?php echo esc_html__('⚡ Ações Rápidas', 'ai-post-generator'); ?></h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label for="aipg-generate-image-topic">
                    <?php echo esc_html__('Gerar Nova Imagem', 'ai-post-generator'); ?>
                </label>
                <div style="display: flex; gap: 10px;">
                    <input 
                        type="text" 
                        id="aipg-generate-image-topic" 
                        class="regular-text"
                        placeholder="<?php echo esc_attr__('Descreva a imagem...', 'ai-post-generator'); ?>"
                        style="flex: 1;"
                    />
                    <button type="button" class="button button-primary" id="aipg-btn-generate-image">
                        <?php echo esc_html__('Gerar', 'ai-post-generator'); ?>
                    </button>
                </div>
            </div>

            <div>
                <label for="aipg-delete-all">
                    <?php echo esc_html__('Limpeza', 'ai-post-generator'); ?>
                </label>
                <button type="button" class="button button-secondary" id="aipg-btn-refresh-stats">
                    <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                    <?php echo esc_html__('Atualizar Estatísticas', 'ai-post-generator'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="aipg-form-card" style="margin-bottom: 30px;">
        <h2><?php echo esc_html__('🔍 Filtros', 'ai-post-generator'); ?></h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label for="aipg-filter-provider">
                    <?php echo esc_html__('Filtrar por Provedor', 'ai-post-generator'); ?>
                </label>
                <select id="aipg-filter-provider" class="regular-text">
                    <option value=""><?php echo esc_html__('-- Todos --', 'ai-post-generator'); ?></option>
                    <option value="pollinations"><?php echo esc_html__('Pollinations', 'ai-post-generator'); ?></option>
                    <option value="unsplash"><?php echo esc_html__('Unsplash', 'ai-post-generator'); ?></option>
                    <option value="pexels"><?php echo esc_html__('Pexels', 'ai-post-generator'); ?></option>
                    <option value="pixabay"><?php echo esc_html__('Pixabay', 'ai-post-generator'); ?></option>
                    <option value="dall-e"><?php echo esc_html__('DALL-E', 'ai-post-generator'); ?></option>
                    <option value="stability"><?php echo esc_html__('Stability AI', 'ai-post-generator'); ?></option>
                </select>
            </div>

            <div>
                <label for="aipg-filter-date">
                    <?php echo esc_html__('Filtrar por Período', 'ai-post-generator'); ?>
                </label>
                <select id="aipg-filter-date" class="regular-text">
                    <option value=""><?php echo esc_html__('-- Todos --', 'ai-post-generator'); ?></option>
                    <option value="today"><?php echo esc_html__('Hoje', 'ai-post-generator'); ?></option>
                    <option value="week"><?php echo esc_html__('Esta Semana', 'ai-post-generator'); ?></option>
                    <option value="month"><?php echo esc_html__('Este Mês', 'ai-post-generator'); ?></option>
                    <option value="year"><?php echo esc_html__('Este Ano', 'ai-post-generator'); ?></option>
                </select>
            </div>

            <div>
                <label for="aipg-search-image">
                    <?php echo esc_html__('Pesquisar', 'ai-post-generator'); ?>
                </label>
                <input 
                    type="text" 
                    id="aipg-search-image" 
                    class="regular-text"
                    placeholder="<?php echo esc_attr__('Nome da imagem...', 'ai-post-generator'); ?>"
                />
            </div>
        </div>
    </div>

    <!-- Galeria de Imagens -->
    <div class="aipg-form-card">
        <h2><?php echo esc_html__('📸 Galeria de Imagens', 'ai-post-generator'); ?></h2>

        <?php if (!empty($images)): ?>
            <div class="wp-list-table widefat fixed striped">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;"><?php echo esc_html__('Miniatura', 'ai-post-generator'); ?></th>
                            <th><?php echo esc_html__('Nome / Título', 'ai-post-generator'); ?></th>
                            <th><?php echo esc_html__('Provedor', 'ai-post-generator'); ?></th>
                            <th><?php echo esc_html__('Data', 'ai-post-generator'); ?></th>
                            <th style="width: 150px;"><?php echo esc_html__('Ações', 'ai-post-generator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($images as $image): ?>
                            <?php 
                            $thumb_url = wp_get_attachment_image_url($image->ID, 'thumbnail');
                            $full_url = wp_get_attachment_url($image->ID);
                            $edit_link = get_edit_post_link($image->ID, 'raw');
                            $provider = $image->provider ? ucfirst($image->provider) : __('Desconhecido', 'ai-post-generator');
                            $date = date_i18n(get_option('date_format'), strtotime($image->post_date));
                            ?>
                            <tr>
                                <td>
                                    <?php if ($thumb_url): ?>
                                        <a href="<?php echo esc_url($full_url); ?>" target="_blank" rel="noopener">
                                            <img 
                                                src="<?php echo esc_url($thumb_url); ?>" 
                                                alt="<?php echo esc_attr($image->post_title); ?>"
                                                style="max-width: 70px; height: auto; border-radius: 4px;"
                                            />
                                        </a>
                                    <?php else: ?>
                                        <span class="dashicons dashicons-image-flip" style="font-size: 40px;"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($image->post_title); ?></strong>
                                </td>
                                <td>
                                    <span class="aipg-badge aipg-badge-success">
                                        <?php echo esc_html($provider); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo esc_html($date); ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url($full_url); ?>" class="button button-small" download target="_blank" rel="noopener">
                                        <?php echo esc_html__('Baixar', 'ai-post-generator'); ?>
                                    </a>
                                    <a href="<?php echo esc_url($edit_link); ?>" class="button button-small">
                                        <?php echo esc_html__('Editar', 'ai-post-generator'); ?>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="button button-small aipg-delete-image" 
                                        data-id="<?php echo esc_attr($image->ID); ?>"
                                    >
                                        <?php echo esc_html__('Excluir', 'ai-post-generator'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <div style="margin-top: 30px; text-align: center;">
                    <div class="pagination">
                        <?php
                        $big = 999999999;
                        $page_links = paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '?paged=%#%',
                            'prev_text' => __('← Anterior', 'ai-post-generator'),
                            'next_text' => __('Próximo →', 'ai-post-generator'),
                            'total' => $total_pages,
                            'current' => $current_page,
                            'echo' => false
                        ));
                        echo wp_kses_post($page_links);
                        ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; background: #f9f9f9; border-radius: 4px;">
                <span class="dashicons dashicons-format-gallery" style="font-size: 64px; color: #ccc;"></span>
                <p style="font-size: 16px; color: #999; margin-top: 20px;">
                    <?php echo esc_html__('Nenhuma imagem gerada ainda. Comece gerando uma imagem acima!', 'ai-post-generator'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Distribuição de Provedores -->
    <?php if (!empty($provider_stats)): ?>
        <div class="aipg-form-card" style="margin-top: 30px;">
            <h2><?php echo esc_html__('📊 Distribuição de Provedores', 'ai-post-generator'); ?></h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <?php foreach ($provider_stats as $stat): ?>
                    <?php 
                    $percentage = $total_images > 0 ? round(($stat->count / $total_images) * 100) : 0;
                    $provider_name = ucfirst($stat->provider);
                    ?>
                    <div>
                        <p style="margin: 0 0 10px 0; font-weight: 600;">
                            <?php echo esc_html($provider_name); ?>
                        </p>
                        <div style="background: #eee; border-radius: 4px; overflow: hidden; height: 24px;">
                            <div 
                                style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); 
                                        height: 100%; 
                                        width: <?php echo esc_attr($percentage); ?>%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        color: white;
                                        font-size: 12px;
                                        font-weight: 600;"
                            >
                                <?php if ($percentage > 5): ?>
                                    <?php echo esc_html($percentage . '%'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <small style="color: #999;">
                            <?php echo esc_html($stat->count . ' ' . __('imagens', 'ai-post-generator')); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Informações -->
    <div class="notice notice-info" style="margin-top: 30px;">
        <p>
            <strong><?php echo esc_html__('ℹ️ Dicas:', 'ai-post-generator'); ?></strong><br>
            <?php echo esc_html__('• Clique em "Baixar" para salvar a imagem no seu computador', 'ai-post-generator'); ?><br>
            <?php echo esc_html__('• Use "Editar" para modificar descrição ou tags alternativas', 'ai-post-generator'); ?><br>
            <?php echo esc_html__('• As imagens são automaticamente vinculadas aos posts gerados', 'ai-post-generator'); ?><br>
            <?php echo esc_html__('• Você pode regenerar imagens na página de geração de posts', 'ai-post-generator'); ?>
        </p>
    </div>
</div>

<?php wp_nonce_field('aipg_generate_post', 'aipg_nonce', false); ?>