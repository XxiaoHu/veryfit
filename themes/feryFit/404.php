<?php
/**
 * The 404 Page Template
 *
 * @package feryfit
 */

get_header(); ?>

<main id="primary" class="site-main">
	<?php feryfit_breadcrumb_render(); ?>

	<div class="error-404 not-found">
		<div class="page-content">
			<h1 class="error-404__title">404</h1>
			<p class="error-404__message"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'feryfit' ); ?></p>
			<p class="error-404__description"><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search or go back to the homepage?', 'feryfit' ); ?></p>

			<div class="error-404__actions">
				<a href="<?php echo home_url('/'); ?>" class="error-404__button">
					<?php esc_html_e( 'Go Back Home', 'feryfit' ); ?>
				</a>
			</div>
		</div><!-- .page-content -->
	</div><!-- .error-404 -->

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
