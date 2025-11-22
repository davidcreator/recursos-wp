<?php $action = esc_url( home_url( '/' ) ); $value = esc_attr( get_search_query() ); ?>
<form role="search" method="get" class="search-form" action="<?php echo $action; ?>">
<label for="s"><?php esc_html_e('Pesquisar','nosfirnews'); ?></label>
<input type="search" id="s" class="search-field" placeholder="<?php esc_attr_e('Pesquisar…','nosfirnews'); ?>" value="<?php echo $value; ?>" name="s" />
<button type="submit" class="search-submit"><?php esc_html_e('Pesquisar','nosfirnews'); ?></button>
</form>