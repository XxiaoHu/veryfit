<?php
/**
 * Render the breadcrumb block
 *
 * @param array $attributes Block attributes.
 *
 * @return string
 */

if (! defined('ABSPATH')) {
	exit;
}

$wrapper_attributes = get_block_wrapper_attributes();

// 获取当前页面的面包屑导航
if (! function_exists('feryfit_breadcrumb_get_items')) {
	function feryfit_breadcrumb_get_items() {
		$items = array();

		// 添加首页
		$items[] = array(
			'title' => 'Home',
			'url' => home_url('/'),
			'is_current' => false,
		);

		// 如果是首页，直接返回
		if (is_front_page()) {
			$items[0]['is_current'] = true;
			return $items;
		}

		// 获取当前页面信息
		global $post;

		if (is_singular() && $post) {
			// 单篇文章/页面
			$post_title = get_the_title();
			
			// 获取父页面层级
			$ancestors = get_post_ancestors($post->ID);
			$ancestors = array_reverse($ancestors);
			
			foreach ($ancestors as $ancestor_id) {
				$ancestor = get_post($ancestor_id);
				if ($ancestor) {
					$items[] = array(
						'title' => $ancestor->post_title,
						'url' => get_permalink($ancestor_id),
						'is_current' => false,
					);
				}
			}

			// 添加当前页面
			$items[] = array(
				'title' => $post_title,
				'url' => get_permalink(),
				'is_current' => true,
			);
		} elseif (is_archive()) {
			// 归档页面
			if (is_category()) {
				$items[] = array(
					'title' => single_cat_title('', false),
					'url' => get_category_link(get_query_var('cat')),
					'is_current' => true,
				);
			} elseif (is_tag()) {
				$items[] = array(
					'title' => single_tag_title('', false),
					'url' => get_tag_link(get_query_var('tag_id')),
					'is_current' => true,
				);
			} elseif (is_post_type_archive()) {
				$items[] = array(
					'title' => post_type_archive_title('', false),
					'url' => get_post_type_archive_link(get_query_var('post_type')),
					'is_current' => true,
				);
			} elseif (is_date()) {
				$items[] = array(
					'title' => get_the_date('Y年m月'),
					'url' => '',
					'is_current' => true,
				);
			} else {
				$items[] = array(
					'title' => get_the_archive_title(),
					'url' => '',
					'is_current' => true,
				);
			}
		} elseif (is_search()) {
			// 搜索结果页面
			$items[] = array(
				'title' => __('搜索结果', 'feryfit') . ': ' . get_search_query(),
				'url' => '',
				'is_current' => true,
			);
		} elseif (is_404()) {
			// 404页面
			$items[] = array(
				'title' => __('页面未找到', 'feryfit'),
				'url' => '',
				'is_current' => true,
			);
		}

		return $items;
	}
}

$breadcrumb_items = feryfit_breadcrumb_get_items();

?>

<div <?php echo $wrapper_attributes; ?>>
	<div class="breadcrumb">
		<?php foreach ($breadcrumb_items as $index => $item) : ?>
			<span class="breadcrumb__item <?php echo $item['is_current'] ? 'breadcrumb__item--current' : ''; ?>">
				<?php if (! $item['is_current'] && ! empty($item['url'])) : ?>
					<a href="<?php echo esc_url($item['url']); ?>" class="breadcrumb__link">
						<?php echo esc_html($item['title']); ?>
					</a>
				<?php else : ?>
					<?php echo esc_html($item['title']); ?>
				<?php endif; ?>
			</span>
			<?php if ($index < count($breadcrumb_items) - 1) : ?>
				<span class="breadcrumb__separator">/</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
