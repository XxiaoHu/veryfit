<?php
/**
 * Kadence functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package kadence
 */

define( 'KADENCE_VERSION', '1.4.5' );
define( 'KADENCE_MINIMUM_WP_VERSION', '6.0' );
define( 'KADENCE_MINIMUM_PHP_VERSION', '7.4' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], KADENCE_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), KADENCE_MINIMUM_PHP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}
// Include WordPress shims.
require get_template_directory() . '/inc/wordpress-shims.php';

// Load the `kadence()` entry point function.
require get_template_directory() . '/inc/class-theme.php';

// Load the `kadence()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Initialize the theme.
call_user_func( 'Kadence\kadence' );

// ==============================================
// 自定义搜索区块样板（修复版 · 无错误 · 可直接使用）
// ==============================================
add_action( 'init', 'custom_search_block_pattern' );
function custom_search_block_pattern() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern(
		'custom/search-section',
		array(
			'title'       => '自定义搜索功能区块',
			'description' => '全站博客模糊搜索区块',
			'categories'  => array( 'widgets' ),
			'content'     => '
<!-- wp:group {"className":"custom-search-section","align":"wide"} -->
<div class="wp-block-group custom-search-section">

<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">搜索文章</h2>
<!-- /wp:heading -->

<!-- wp:custom-html -->
<form method="get" action="' . esc_url( home_url( '/' ) ) . '">
<input type="hidden" name="post_type" value="post">
<div style="display:flex;gap:10px;max-width:600px;margin:0 auto;">
<input type="text" name="s" placeholder="输入关键词搜索..." style="flex:1;padding:12px;border:1px solid #ddd;border-radius:6px;">
<button type="submit" style="padding:12px 24px;background:#000;color:#fff;border:none;border-radius:6px;cursor:pointer;">搜索</button>
</div>
</form>
<!-- /wp:custom-html -->

</div>
<!-- /wp:group -->
			',
		)
	);
}

// ==============================================
// 搜索功能：只搜索文章标题 + 内容
// ==============================================
add_filter( 'posts_search', 'theme_blog_search_only', 10, 2 );
function theme_blog_search_only( $search, $wp_query ) {
	global $wpdb;

	if ( is_admin() || ! $wp_query->is_search() ) {
		return $search;
	}

	if ( $wp_query->query_vars['post_type'] === 'post' ) {
		$keyword = $wp_query->get( 's' );
		$like_keyword = '%' . $wpdb->esc_like( $keyword ) . '%';

		$search = "
			AND (
				{$wpdb->posts}.post_title LIKE '{$like_keyword}'
				OR {$wpdb->posts}.post_content LIKE '{$like_keyword}'
			)
		";
	}

	return $search;
}

// ==============================================
// Polylang 多语言搜索支持
// ==============================================
add_filter( 'pre_get_posts', 'search_current_language_only' );
function search_current_language_only( $query ) {
	if (
		function_exists( 'pll_current_language' )
		&& $query->is_search()
		&& ! is_admin()
	) {
		$query->set( 'lang', pll_current_language() );
	}
}