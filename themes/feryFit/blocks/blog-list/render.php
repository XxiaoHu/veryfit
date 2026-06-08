<?php
/**
 * Blog List Block Renderer
 *
 * @package feryfit
 */

$desktop_margin = isset( $attributes['desktopTopMargin'] ) ? $attributes['desktopTopMargin'] : 78;
$mobile_margin = isset( $attributes['mobileTopMargin'] ) ? $attributes['mobileTopMargin'] : 40;
$posts_per_category = isset( $attributes['postsPerCategory'] ) ? $attributes['postsPerCategory'] : 6;
$category1_title = isset( $attributes['category1Title'] ) ? $attributes['category1Title'] : 'Setup & Pairing';
$category2_title = isset( $attributes['category2Title'] ) ? $attributes['category2Title'] : 'Daily Use';

// Fixed categories with dynamic titles
$categories = array(
	array(
		'name' => $category1_title,
		'slug' => 'setup-pairing',
		'tag_class' => 'blog-list__category-tag--red',
		'icon_src' => get_template_directory_uri() . '/assets/images/peidui2.png',
	),
	array(
		'name' => $category2_title,
		'slug' => 'daily-use',
		'tag_class' => 'blog-list__category-tag--black',
		'icon_src' => get_template_directory_uri() . '/assets/images/log1.png',
	),
);

$wrapper_attributes = get_block_wrapper_attributes( array(
	'style' => sprintf( 'margin-top: %spx; --blog-list-mobile-margin-top: %spx;', esc_attr( $desktop_margin ), esc_attr( $mobile_margin ) ),
) );

// Helper function to limit text length
if ( ! function_exists( 'feryfit_blog_limit_title' ) ) {
	function feryfit_blog_limit_title( $title, $length = 40 ) {
		$title = strip_tags( $title );
		if ( strlen( $title ) > $length ) {
			$title = mb_substr( $title, 0, $length ) . ' ........... ';
		}
		return $title;
	}
}

?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="blog-list">
		<div class="blog-list__container">
			<?php foreach ( $categories as $category ) : ?>
				<?php
				// Get posts for this category by slug
				$args = array(
					'post_type' => 'blog',
					'posts_per_page' => $posts_per_category,
					'tax_query' => array(
						array(
							'taxonomy' => 'blog_category',
							'field' => 'slug',
							'terms' => $category['slug'],
						),
					),
					'orderby' => array(
					'meta_value_num' => 'DESC',
					'post_modified' => 'DESC',
				),
				'meta_key' => '_blog_is_pinned',
					'post_status' => 'publish',
					'no_found_rows' => true,
				);

				$posts = get_posts( $args );
				?>

				<div class="blog-list__card">
					<div class="blog-list__card-header">
						<span class="blog-list__category-tag <?php echo esc_attr( $category['tag_class'] ); ?>">
							<?php echo esc_html( $category['name'] ); ?>
						</span>
						<span class="blog-list__card-icon">
							<img src="<?php echo esc_url( $category['icon_src'] ); ?>" alt="" />
						</span>
					</div>
					
					<?php if ( ! empty( $posts ) ) : ?>
						<ul class="blog-list__items">
							<?php foreach ( $posts as $post ) : ?>
								<li class="blog-list__item">
									<a href="<?php echo esc_url( get_site_url() . '/index.php?post_type=blog&p=' . $post->ID ); ?>">
										<span class="blog-list__item-title">
											<?php echo esc_html( feryfit_blog_limit_title( get_the_title( $post ), 30 ) ); ?>
										</span>
										<span class="blog-list__item-date">
											<?php echo esc_html( get_the_date( 'Y-m-d', $post ) ); ?>
										</span>
										<span class="blog-list__item-arrow">›</span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<ul class="blog-list__items">
							<li class="blog-list__item">
								<span class="blog-list__item-title">How does the FANY Luna smartwatch measure ................</span>
								<span class="blog-list__item-date">2025-11-18</span>
								<span class="blog-list__item-arrow">›</span>
							</li>
							<li class="blog-list__item">
								<span class="blog-list__item-title">How does the FANY Luna smartwatch measure ................</span>
								<span class="blog-list__item-date">2025-11-18</span>
								<span class="blog-list__item-arrow">›</span>
							</li>
							<li class="blog-list__item">
								<span class="blog-list__item-title">How does the FANY Luna smartwatch measure ................</span>
								<span class="blog-list__item-date">2025-11-18</span>
								<span class="blog-list__item-arrow">›</span>
							</li>
							<li class="blog-list__item">
								<span class="blog-list__item-title">How does the FANY Luna smartwatch measure ................</span>
								<span class="blog-list__item-date">2025-11-18</span>
								<span class="blog-list__item-arrow">›</span>
							</li>
							<li class="blog-list__item">
								<span class="blog-list__item-title">How does the FANY Luna smartwatch measure ................</span>
								<span class="blog-list__item-date">2025-11-18</span>
								<span class="blog-list__item-arrow">›</span>
							</li>
							<li class="blog-list__item">
								<span class="blog-list__item-title">How does the FANY Luna smartwatch measure ................</span>
								<span class="blog-list__item-date">2025-11-18</span>
								<span class="blog-list__item-arrow">›</span>
							</li>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
