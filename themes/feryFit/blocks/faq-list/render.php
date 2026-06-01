<?php
/**
 * FAQ List Block Renderer
 *
 * @package feryfit
 */

$title = isset( $attributes['title'] ) ? $attributes['title'] : 'TOP FAQS';
$learn_more_text = isset( $attributes['learnMoreText'] ) ? $attributes['learnMoreText'] : 'Learn More >';
$posts_per_page = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 8;
$desktop_margin = isset( $attributes['desktopTopMargin'] ) ? $attributes['desktopTopMargin'] : 40;
$mobile_margin = isset( $attributes['mobileTopMargin'] ) ? $attributes['mobileTopMargin'] : 20;

// 动态生成 FAQ 归档页面链接，支持 Polylang 多语言
$faq_archive_url = get_post_type_archive_link( 'faq' );
// 如果使用 Polylang，获取当前语言的归档链接
if ( function_exists( 'pll_current_language' ) ) {
    $current_lang = pll_current_language();
    $default_lang = pll_default_language();
    // 非默认语言需要添加语言前缀
    if ( $current_lang && $current_lang !== $default_lang ) {
        $faq_archive_url = home_url( '/' . $current_lang . '/archives/faq/' );
    }
}

$wrapper_attributes = get_block_wrapper_attributes( array(
	'style' => sprintf( 'margin-top: %spx; --faq-list-mobile-margin-top: %spx;', esc_attr( $desktop_margin ), esc_attr( $mobile_margin ) ),
) );

// Helper function to limit text length
if ( ! function_exists( 'feryfit_faq_limit_title' ) ) {
	function feryfit_faq_limit_title( $title, $length = 60 ) {
		$title = strip_tags( $title );
		if ( strlen( $title ) > $length ) {
			$title = mb_substr( $title, 0, $length ) . '...';
		}
		return $title;
	}
}

// Get FAQ posts
// 排序规则：置顶优先（使用 _faq_is_pinned meta 字段），然后按修改时间降序（最新的在前面）
$args = array(
	'post_type' => 'faq',
	'posts_per_page' => -1, // 获取所有，然后在 PHP 中排序和限制
	'post_status' => 'publish',
	'order' => 'DESC',
	'orderby' => 'modified',
);

$posts = get_posts( $args );

// 手动排序：置顶文章优先，然后按修改时间降序
if ( ! empty( $posts ) ) {
	$sticky_faqs = array();
	$normal_faqs = array();
	
	foreach ( $posts as $post ) {
		$is_pinned = get_post_meta( $post->ID, '_faq_is_pinned', true );
		if ( $is_pinned === '1' ) {
			$sticky_faqs[] = $post;
		} else {
			$normal_faqs[] = $post;
		}
	}
	
	// 置顶文章按修改时间降序
	usort( $sticky_faqs, function( $a, $b ) {
		return strtotime( $b->post_modified ) - strtotime( $a->post_modified );
	});
	
	// 普通文章按修改时间降序
	usort( $normal_faqs, function( $a, $b ) {
		return strtotime( $b->post_modified ) - strtotime( $a->post_modified );
	});
	
	// 合并：置顶在前
	$posts = array_merge( $sticky_faqs, $normal_faqs );
	
	// 限制数量
	$posts = array_slice( $posts, 0, $posts_per_page );
}

$arrow_icon = get_template_directory_uri() . '/assets/images/right.png';

?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="faq-list">
		<div class="faq-list__header">
			<h2 class="faq-list__title"><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( $faq_archive_url ); ?>" class="faq-list__learn-more">
				<?php echo esc_html( $learn_more_text ); ?>
			</a>
		</div>
		
		<?php if ( ! empty( $posts ) ) : ?>
			<ul class="faq-list__items">
				<?php $index = 1; ?>
				<?php foreach ( $posts as $post ) : ?>
					<li class="faq-list__item">
						<a href="<?php echo esc_url( home_url( '/archives/faq/' . $post->ID . '/' ) ); ?>">
							<span class="faq-list__item-number"><?php echo esc_html( $index ); ?>.</span>
							<span class="faq-list__item-title">
								<?php echo esc_html( feryfit_faq_limit_title( get_the_title( $post ), 60 ) ); ?>
							</span>
							<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
						</a>
					</li>
					<?php $index++; ?>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<ul class="faq-list__items">
				<li class="faq-list__item">
					<span class="faq-list__item-number">1.</span>
					<span class="faq-list__item-title">How can I charge and turn on the watch?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">2.</span>
					<span class="faq-list__item-title">First connection?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">3.</span>
					<span class="faq-list__item-title">How to check the model of the watch?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">4.</span>
					<span class="faq-list__item-title">How to sync data?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">5.</span>
					<span class="faq-list__item-title">How to change C to F/ Mile to KM/ Military Time?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">6.</span>
					<span class="faq-list__item-title">How to receive Facebook, SMS, or incoming call notifications?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">7.</span>
					<span class="faq-list__item-title">How to set a walking reminder?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
				<li class="faq-list__item">
					<span class="faq-list__item-number">8.</span>
					<span class="faq-list__item-title">How to use the Music Control feature?</span>
					<img src="<?php echo esc_url( $arrow_icon ); ?>" alt="" class="faq-list__item-arrow" />
				</li>
			</ul>
		<?php endif; ?>
	</div>
</div>
