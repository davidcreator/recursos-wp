<?php
if ( post_password_required() ) return;
?>
<div id="comments" class="comments-area">
<?php if ( have_comments() ) : ?>
<h2 class="comments-title"><?php printf( esc_html__( '%1$s comentário(s)', 'nosfirnews' ), get_comments_number() ); ?></h2>
<ol class="comment-list"><?php wp_list_comments( [ 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48, 'format' => 'html5', 'reply_text' => __( 'Responder', 'nosfirnews' ) ] ); ?></ol>
<?php the_comments_navigation(); ?>
<?php endif; ?>
<?php comment_form(); ?>
</div>
