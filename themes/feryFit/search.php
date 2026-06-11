<?php
/**
 * Search Results Template
 *
 * @package feryFit
 */

get_header();

// 关键词标红函数
function highlight_search_term($text, $search_term) {
    if (empty($search_term)) {
        return $text;
    }
    $pattern = '/(' . preg_quote($search_term, '/') . ')/i';
    $replacement = '<span class="search-highlight">$1</span>';
    return preg_replace($pattern, $replacement, $text);
}

$search_query = get_search_query();
?>

<div id="primary" class="content-area search-page">
	<main id="main" class="site-main">

		<header class="page-header">
			<div class="search-page__banner">
				<!-- 搜索框 -->
				<form class="hero-banner__search-form" role="search" method="get">
					<?php if (function_exists('pll_current_language')) : ?>
						<input type="hidden" name="lang" value="<?php echo esc_attr(pll_current_language()); ?>">
					<?php endif; ?>
					<span class="hero-banner__search-icon">
						<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<path d="m21 21-4.35-4.35"></path>
						</svg>
					</span>
					<input
						type="text"
						class="hero-banner__search-input"
						placeholder="<?php echo esc_attr( pll__( 'Search by question or keyword', 'feryfit' ) ); ?>"
						name="s"
						id="hero-banner-search"
						value="<?php echo esc_attr( $search_query ); ?>"
					/>
					<button type="submit" class="hero-banner__search-button">
						<?php echo esc_html( pll__( 'Search', 'feryfit' ) ); ?>
					</button>
				</form>
			</div>
		</header><!-- .page-header -->

		<div class="search-results-container">
			<?php if ( have_posts() ) : ?>

				<div class="search-results-count">
					<?php global $wp_query; echo esc_html( sprintf( pll__( '%d results have been found', 'feryfit' ), $wp_query->found_posts ) ); ?>
				</div>

				<ol class="search-results-list">
					<?php
					/* Start the Loop */
					$index = 1;
					while ( have_posts() ) :
						the_post();

						// 获取标题和摘要，并标红关键词
						$title = highlight_search_term(get_the_title(), $search_query);
						$excerpt = highlight_search_term(wp_strip_all_tags(get_the_excerpt()), $search_query);
						$post_type = get_post_type();
						?>

						<li class="search-result-item">
							<a href="<?php echo esc_url( get_permalink() ); ?>" class="search-result-link">
								<h3 class="search-result-title">
									<?php echo $index . '. '; ?>
									<?php if ($post_type === 'video_content') : ?>
										<span class="post-type-badge">[Video]</span>
									<?php endif; ?>
									<?php echo $title; ?>
								</h3>
								<p class="search-result-excerpt"><?php echo $excerpt; ?></p>
							</a>
						</li>

						<?php
						$index++;
					endwhile;
					?>
				</ol>

				<?php
				global $wp_query;
				$total_pages = intval( $wp_query->max_num_pages );
				$current_page = max( 1, get_query_var( 'paged' ) );
				$start = ( $current_page - 1 ) * 10 + 1;
				$end = min( $start + 9, intval( $wp_query->found_posts ) );

				if ( $total_pages > 1 ) :
				?>
				<div class="search-pagination">
					<div class="search-pagination__nav-container">
						<?php if ( $current_page == 1 ) : ?>
							<span class="search-pagination__nav search-pagination__nav--prev search-pagination__nav--disabled">
								<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
									<g clip-path="url(#sp_prev)">
										<path d="M18.4125 22.15C18.315 22.2479 18.199 22.3256 18.0714 22.3786C17.9438 22.4315 17.8069 22.4588 17.6688 22.4588C17.5306 22.4588 17.3937 22.4315 17.2661 22.3786C17.1385 22.3256 17.0226 22.2479 16.925 22.15L10 15.725C9.89439 15.6267 9.81015 15.5078 9.75254 15.3755C9.69494 15.2432 9.66522 15.1005 9.66522 14.9562C9.66522 14.812 9.69494 14.6693 9.75254 14.537C9.81015 14.4047 9.89439 14.2858 10 14.1875L16.9 7.725C17.1039 7.53437 17.3752 7.43255 17.6541 7.44193C17.9331 7.45131 18.1969 7.57111 18.3875 7.775C18.5781 7.97888 18.68 8.25014 18.6706 8.52911C18.6612 8.80807 18.5414 9.07188 18.3375 9.2625L12.2875 15L18.375 20.7C18.5666 20.8905 18.6774 21.1474 18.6844 21.4175C18.6914 21.6875 18.594 21.9499 18.4125 22.15ZM15 0C12.0333 0 9.13319 0.879735 6.66645 2.52796C4.19972 4.17618 2.27713 6.51886 1.14181 9.25975C0.00649922 12.0006 -0.290551 15.0166 0.288227 17.9264C0.867006 20.8361 2.29562 23.5088 4.3934 25.6066C6.49119 27.7044 9.16394 29.133 12.0737 29.7118C14.9834 30.2906 17.9994 29.9935 20.7403 28.8582C23.4811 27.7229 25.8238 25.8003 27.472 23.3336C29.1203 20.8668 30 17.9667 30 15C30 11.0218 28.4197 7.20644 25.6066 4.3934C22.7936 1.58035 18.9783 0 15 0Z" fill="#B8B8B8"/>
									</g>
									<defs><clipPath id="sp_prev"><rect width="30" height="30" fill="white"/></clipPath></defs>
								</svg>
							</span>
						<?php else : ?>
							<a href="<?php echo esc_url( get_pagenum_link( $current_page - 1 ) ); ?>" class="search-pagination__nav search-pagination__nav--prev">
								<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
									<g clip-path="url(#sp_prev_active)">
										<path d="M18.4125 22.15C18.315 22.2479 18.199 22.3256 18.0714 22.3786C17.9438 22.4315 17.8069 22.4588 17.6688 22.4588C17.5306 22.4588 17.3937 22.4315 17.2661 22.3786C17.1385 22.3256 17.0226 22.2479 16.925 22.15L10 15.725C9.89439 15.6267 9.81015 15.5078 9.75254 15.3755C9.69494 15.2432 9.66522 15.1005 9.66522 14.9562C9.66522 14.812 9.69494 14.6693 9.75254 14.537C9.81015 14.4047 9.89439 14.2858 10 14.1875L16.9 7.725C17.1039 7.53437 17.3752 7.43255 17.6541 7.44193C17.9331 7.45131 18.1969 7.57111 18.3875 7.775C18.5781 7.97888 18.68 8.25014 18.6706 8.52911C18.6612 8.80807 18.5414 9.07188 18.3375 9.2625L12.2875 15L18.375 20.7C18.5666 20.8905 18.6774 21.1474 18.6844 21.4175C18.6914 21.6875 18.594 21.9499 18.4125 22.15ZM15 0C12.0333 0 9.13319 0.879735 6.66645 2.52796C4.19972 4.17618 2.27713 6.51886 1.14181 9.25975C0.00649922 12.0006 -0.290551 15.0166 0.288227 17.9264C0.867006 20.8361 2.29562 23.5088 4.3934 25.6066C6.49119 27.7044 9.16394 29.133 12.0737 29.7118C14.9834 30.2906 17.9994 29.9935 20.7403 28.8582C23.4811 27.7229 25.8238 25.8003 27.472 23.3336C29.1203 20.8668 30 17.9667 30 15C30 11.0218 28.4197 7.20644 25.6066 4.3934C22.7936 1.58035 18.9783 0 15 0Z" fill="black"/>
									</g>
									<defs><clipPath id="sp_prev_active"><rect width="30" height="30" fill="white"/></clipPath></defs>
								</svg>
							</a>
						<?php endif; ?>

						<div class="search-pagination__pages">
							<?php
							$max_visible = 5;
							$start_page = max( 1, $current_page - floor( $max_visible / 2 ) );
							$end_page = min( $total_pages, $start_page + $max_visible - 1 );
							if ( $end_page - $start_page + 1 < $max_visible ) {
								$start_page = max( 1, $end_page - $max_visible + 1 );
							}
							if ( $start_page > 1 ) :
								?>
								<a href="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>" class="search-pagination__page">1</a>
								<?php if ( $start_page > 2 ) : ?>
									<span class="search-pagination__dots">...</span>
								<?php endif; ?>
							<?php endif; ?>

							<?php for ( $i = $start_page; $i <= $end_page; $i++ ) : ?>
								<a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>" class="search-pagination__page <?php echo $current_page === $i ? 'search-pagination__page--active' : ''; ?>">
									<?php echo $i; ?>
								</a>
							<?php endfor; ?>

							<?php if ( $end_page < $total_pages ) : ?>
								<?php if ( $end_page < $total_pages - 1 ) : ?>
									<span class="search-pagination__dots">...</span>
								<?php endif; ?>
								<a href="<?php echo esc_url( get_pagenum_link( $total_pages ) ); ?>" class="search-pagination__page"><?php echo $total_pages; ?></a>
							<?php endif; ?>
						</div>

						<div class="search-pagination__mobile-counter">
							<span class="search-pagination__current"><?php echo $current_page; ?></span>
							<span class="search-pagination__separator">/</span>
							<span class="search-pagination__total"><?php echo $total_pages; ?></span>
						</div>

						<?php if ( $current_page == $total_pages ) : ?>
							<span class="search-pagination__nav search-pagination__nav--next search-pagination__nav--disabled">
								<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
									<g clip-path="url(#sp_next)">
										<path d="M11.5875 7.85C11.685 7.75211 11.801 7.67444 11.9286 7.62145C12.0562 7.56845 12.1931 7.54117 12.3312 7.54117C12.4694 7.54117 12.6063 7.56845 12.7339 7.62145C12.8615 7.67444 12.9774 7.75211 13.075 7.85L20 14.275C20.1056 14.3733 20.1899 14.4922 20.2475 14.6245C20.3051 14.7568 20.3348 14.8995 20.3348 15.0438C20.3348 15.188 20.3051 15.3307 20.2475 15.463C20.1899 15.5953 20.1056 15.7142 20 15.8125L13.1 22.275C12.8961 22.4656 12.6248 22.5674 12.3459 22.5581C12.0669 22.5487 11.8031 22.4289 11.6125 22.225C11.4219 22.0211 11.32 21.7499 11.3294 21.4709C11.3388 21.1919 11.4586 20.9281 11.6625 20.7375L17.7125 15L11.625 9.3C11.4334 9.10954 11.3226 8.85257 11.3156 8.58252C11.3086 8.31247 11.406 8.05011 11.5875 7.85ZM15 30C17.9667 30 20.8668 29.1203 23.3335 27.472C25.8003 25.8238 27.7229 23.4811 28.8582 20.7403C29.9935 17.9994 30.2906 14.9834 29.7118 12.0736C29.133 9.16393 27.7044 6.49119 25.6066 4.3934C23.5088 2.29561 20.8361 0.867001 17.9263 0.288223C15.0166 -0.290556 12.0006 0.00649452 9.25974 1.14181C6.51885 2.27712 4.17617 4.19971 2.52795 6.66645C0.879728 9.13318 -5.72205e-06 12.0333 -5.72205e-06 15C-5.72205e-06 18.9782 1.58035 22.7936 4.39339 25.6066C7.20644 28.4196 11.0217 30 15 30Z" fill="#B8B8B8"/>
									</g>
									<defs><clipPath id="sp_next"><rect width="30" height="30" fill="white" transform="matrix(-1 0 0 -1 30 30)"/></clipPath></defs>
								</svg>
							</span>
						<?php else : ?>
							<a href="<?php echo esc_url( get_pagenum_link( $current_page + 1 ) ); ?>" class="search-pagination__nav search-pagination__nav--next">
								<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
									<g clip-path="url(#sp_next_active)">
										<path d="M11.5875 7.85C11.685 7.75211 11.801 7.67444 11.9286 7.62145C12.0562 7.56845 12.1931 7.54117 12.3312 7.54117C12.4694 7.54117 12.6063 7.56845 12.7339 7.62145C12.8615 7.67444 12.9774 7.75211 13.075 7.85L20 14.275C20.1056 14.3733 20.1899 14.4922 20.2475 14.6245C20.3051 14.7568 20.3348 14.8995 20.3348 15.0438C20.3348 15.188 20.3051 15.3307 20.2475 15.463C20.1899 15.5953 20.1056 15.7142 20 15.8125L13.1 22.275C12.8961 22.4656 12.6248 22.5674 12.3459 22.5581C12.0669 22.5487 11.8031 22.4289 11.6125 22.225C11.4219 22.0211 11.32 21.7499 11.3294 21.4709C11.3388 21.1919 11.4586 20.9281 11.6625 20.7375L17.7125 15L11.625 9.3C11.4334 9.10954 11.3226 8.85257 11.3156 8.58252C11.3086 8.31247 11.406 8.05011 11.5875 7.85ZM15 30C17.9667 30 20.8668 29.1203 23.3335 27.472C25.8003 25.8238 27.7229 23.4811 28.8582 20.7403C29.9935 17.9994 30.2906 14.9834 29.7118 12.0736C29.133 9.16393 27.7044 6.49119 25.6066 4.3934C23.5088 2.29561 20.8361 0.867001 17.9263 0.288223C15.0166 -0.290556 12.0006 0.00649452 9.25974 1.14181C6.51885 2.27712 4.17617 4.19971 2.52795 6.66645C0.879728 9.13318 -5.72205e-06 12.0333 -5.72205e-06 15C-5.72205e-06 18.9782 1.58035 22.7936 4.39339 25.6066C7.20644 28.4196 11.0217 30 15 30Z" fill="black"/>
									</g>
									<defs><clipPath id="sp_next_active"><rect width="30" height="30" fill="white" transform="matrix(-1 0 0 -1 30 30)"/></clipPath></defs>
								</svg>
							</a>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

			<?php else : ?>

				<div class="search-no-results">
					<div class="search-no-results__icon">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/noSearch.png" alt="No results" />
					</div>
					<h3 class="search-no-results__title"><?php echo esc_html(pll__("Can't find what you're looking for?")); ?></h3>
					<p class="search-no-results__desc"><?php echo esc_html(pll__("Click the chat bubble in the bottom right corner — our support team is here to help")); ?> <span class="search-no-results__emoji"></span></p>
				</div>

			<?php endif; ?>
		</div>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
feryfit_floating_chat(array(
	'whatsapp' => get_option('feryfit_whatsapp', ''),
	'email'    => 'mailto:' . get_option('feryfit_email', ''),
	'facebook' => get_option('feryfit_facebook', ''),
));

get_sidebar();
get_footer();
?>
