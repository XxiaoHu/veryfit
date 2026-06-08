<?php
/**
 * Render the hero-banner block
 *
 * @param array $attributes Block attributes.
 *
 * @return string
 */

if (! defined('ABSPATH')) {
	exit;
}

$background_image  = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : '';
$mobile_bg_image   = isset($attributes['mobileBackgroundImage']) ? $attributes['mobileBackgroundImage'] : '';
$title             = isset($attributes['title']) ? $attributes['title'] : '';
$subtitle          = isset($attributes['subtitle']) ? $attributes['subtitle'] : '';
$keywords          = isset($attributes['keywords']) ? $attributes['keywords'] : array();
$banner_width      = isset($attributes['bannerWidth']) ? intval($attributes['bannerWidth']) : 1940;
$banner_height     = isset($attributes['bannerHeight']) ? intval($attributes['bannerHeight']) : 480;
$search_placeholder = isset($attributes['searchPlaceholder']) ? $attributes['searchPlaceholder'] : pll__( 'Search by question or keyword', 'feryfit' );
$search_button_text = isset($attributes['searchButtonText']) ? $attributes['searchButtonText'] : pll__( 'Search', 'feryfit' );

$wrapper_attributes = get_block_wrapper_attributes(array(
	'style' => 'max-width:' . $banner_width . 'px; height:' . $banner_height . 'px; min-height:' . $banner_height . 'px;',
));

// No hardcoded action URL - form submits to current page.
// WordPress detects ?s= param and displays search results, preserving current language.
?>

<div <?php echo $wrapper_attributes; ?>>
	<div class="hero-banner__background" style="background-image: <?php echo $background_image ? 'url(' . esc_url($background_image) . ')' : 'none'; ?>;">
		<?php if ($mobile_bg_image) : ?>
			<div class="hero-banner__mobile-background" style="background-image: url(<?php echo esc_url($mobile_bg_image); ?>);"></div>
		<?php endif; ?>
		<div class="hero-banner__content">
			<h1 class="hero-banner__title"><?php echo wp_kses_post($title); ?></h1>
			<p class="hero-banner__subtitle"><?php echo wp_kses_post($subtitle); ?></p>
			<form class="hero-banner__search-form" role="search" method="get" action="<?php 
	$lang = function_exists('pll_current_language') ? pll_current_language() : '';
	$default_lang = function_exists('pll_default_language') ? pll_default_language() : '';
	echo esc_url( ($lang && $lang !== $default_lang) ? home_url('/' . $lang . '/') : home_url('/') );
?>">
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
					placeholder="<?php echo esc_attr($search_placeholder); ?>"
					name="s"
					id="hero-banner-search"
				/>
				<button type="submit" class="hero-banner__search-button">
					<?php echo esc_html($search_button_text); ?>
				</button>
			</form>
			<?php if (! empty($keywords)) : ?>
				<div class="hero-banner__keywords-list">
					<?php foreach ($keywords as $keyword) : ?>
						<span class="hero-banner__keyword-tag"><?php echo esc_html($keyword); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
