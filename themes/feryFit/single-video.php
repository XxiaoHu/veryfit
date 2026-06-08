<?php

/**
 * Template for Video Single Post
 *
 * @package feryfit
 */

get_header(); ?>
	<?php feryfit_breadcrumb_render(); ?>
<main id="primary" class="site-main">


	<?php
	while (have_posts()) :
		the_post();
	?>
		<div class="post-type-badge"><?php echo esc_html( pll__( 'Sleep & Health', 'feryfit' ) ); ?></div>
		<article id="post-<?php the_ID(); ?>" <?php post_class('video-single-article'); ?>>
			<header class="entry-header">
				<?php the_title('<h1 class="entry-title">', '</h1>'); ?>

				<!-- <div class="entry-meta">
					<span class="post-date"><?php echo get_the_date(); ?></span>
				</div> -->
			</header><!-- .entry-header -->

			<div class="entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __('Pages:', 'feryfit'),
						'after'  => '</div>',
					)
				);
				?>
			</div><!-- .entry-content -->

		</article><!-- #post-<?php the_ID(); ?> -->

	<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if (comments_open() || get_comments_number()) :
			comments_template();
		endif;

	endwhile; // End of the loop.
	?>

</main><!-- #primary -->

<?php
// 调用客服浮窗组件
feryfit_floating_chat(array(
	'whatsapp' => get_option( 'feryfit_whatsapp', '' ),
	'email'    => 'mailto:' . get_option( 'feryfit_email', '' ),
	'facebook' => get_option( 'feryfit_facebook', '' ),
));
?>

<?php
get_sidebar();
get_footer();
?>