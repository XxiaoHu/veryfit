<?php

/**
 * Render the video carousel block
 *
 * @param array $attributes Block attributes.
 *
 * @return string
 */

if (! defined('ABSPATH')) {
	exit;
}

$section_title      = isset($attributes['sectionTitle']) ? $attributes['sectionTitle'] : 'Hot Video Section';
$learn_more_text    = isset($attributes['learnMoreText']) ? $attributes['learnMoreText'] : 'Learn More';
$items_per_row      = 4;
$rows_per_slide     = 2;

// 动态生成视频归档页面链接，支持 Polylang 多语言
$video_archive_url = home_url('/video/');
if (function_exists('pll_current_language')) {
	$current_lang = pll_current_language();
	$default_lang = pll_default_language();
	if ($current_lang && $current_lang !== $default_lang) {
		$video_archive_url = home_url('/' . $current_lang . '/video/');
	}
}

$video_posts = array();
$max_items = $items_per_row * $rows_per_slide;

if (function_exists('feryfit_get_video_contents')) {
	$raw_posts = feryfit_get_video_contents(array(
		'posts_per_page' => 50,
	));

	foreach ($raw_posts as $post) {
		$thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
		$raw_content = get_the_content(null, false, $post);
		$raw_content = apply_filters('the_content', $raw_content);

		$iframe_src = '';
		$iframe_html = '';
		if (preg_match('/<iframe[^>]*>/i', $raw_content, $matches)) {
			$iframe_html = $matches[0];
			if (preg_match('/src=["\']([^"\']+)["\']/', $iframe_html, $src_matches)) {
				$iframe_src = $src_matches[1];
			}
		}

		$youtube_id = '';
		$youtube_thumbnail = '';
		if ($iframe_src && preg_match('#(?:youtube\.com/embed/|youtu\.be/|youtube\.com/watch\?v=)([a-zA-Z0-9_-]{11})#', $iframe_src, $yt_matches)) {
			$youtube_id = $yt_matches[1];
			$youtube_thumbnail = 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg';
		}

		$video_posts[] = array(
			'id'                => $post->ID,
			'title'             => get_the_title($post),
			'content'           => $raw_content,
			'iframe_src'        => $iframe_src,
			'iframe_html'       => $iframe_html,
			'youtube_id'        => $youtube_id,
			'youtube_thumbnail' => $youtube_thumbnail,
			'excerpt'           => get_the_excerpt($post),
			'thumbnail'         => $thumbnail_url ? $thumbnail_url : '',
			'duration'          => '',
			'video_url'         => feryfit_get_video_content_url($post->ID),
			'permalink'         => feryfit_get_video_content_permalink($post->ID),
			'post_date'         => get_the_date('Y-m-d H:i:s', $post),
			'post_modified'     => get_the_modified_date('Y-m-d H:i:s', $post),
		);
	}
}

$display_videos = $video_posts;
$has_navigation = count($video_posts) > $max_items;
$play_btn_url = get_template_directory_uri() . '/assets/images/Group.png';

?>

<div class="wp-block-feryfit-video-carousel">
	<div class="video-carousel">
		<div class="video-carousel__header">
			<h2 class="video-carousel__title"><?php echo esc_html($section_title); ?></h2>
			<a href="<?php echo esc_url($video_archive_url); ?>" class="video-carousel__learn-more">
				<?php echo esc_html($learn_more_text); ?> >
			</a>
		</div>

		<div class="video-carousel__wrapper">
			<?php if ($has_navigation) : ?>
			<button class="video-carousel__prev" aria-label="<?php esc_attr_e('Previous slide', 'feryfit'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
					<path d="M30 55C43.75 55 55 43.75 55 30C55 16.25 43.75 5 30 5C16.25 5 5 16.25 5 30C5 43.75 16.25 55 30 55Z" fill="#D4D4D4" />
					<path d="M33.75 41.25L22.5 30L33.75 18.75" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
			<?php endif; ?>

			<div class="video-carousel__items" data-items-per-row="4" data-rows-per-slide="2" data-max-items="<?php echo absint($max_items); ?>">
				<?php foreach ($display_videos as $video) : ?>
					<div class="video-carousel__item">
						<a class="video-carousel__item-link" href="<?php echo esc_url($video['permalink']); ?>">
							<div class="video-carousel__thumbnail-wrapper">
								<?php if (! empty($video['youtube_thumbnail'])) : ?>
									<img
										class="video-carousel__video"
										src="<?php echo esc_url($video['youtube_thumbnail']); ?>"
										alt="<?php echo esc_attr($video['title']); ?>"
										loading="lazy" />
								<?php endif; ?>
								<img
									class="video-carousel__play-btn"
									src="<?php echo esc_url($play_btn_url); ?>"
									alt="<?php esc_attr_e('Play', 'feryfit'); ?>" />
							</div>
							<div class="video-carousel__item-title-box">
								<div class="video-carousel__item-title"><?php echo esc_html($video['title']); ?></div>
							</div>

						</a>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ($has_navigation) : ?>
		<button class="video-carousel__next" aria-label="<?php esc_attr_e('Next slide', 'feryfit'); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
					<path d="M30 55C43.75 55 55 43.75 55 30C55 16.25 43.75 5 30 5C16.25 5 5 16.25 5 30C5 43.75 16.25 55 30 55Z" fill="#D4D4D4" />
					<path d="M26.25 18.75L37.5 30L26.25 41.25" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
			<?php endif; ?>
		</div>

		<?php if ($has_navigation) : ?>
		<div class="video-carousel__pagination"></div>
		<?php endif; ?>
	</div>
</div>

<script>
	(function() {
		var videoData = <?php echo wp_json_encode($display_videos); ?>;
		console.log('Video Carousel Data:', videoData);

		var wrapper = document.querySelector('.video-carousel__wrapper');
		var itemsContainer = document.querySelector('.video-carousel__items');
		var prevBtn = document.querySelector('.video-carousel__prev');
		var nextBtn = document.querySelector('.video-carousel__next');
		var paginationContainer = document.querySelector('.video-carousel__pagination');

		if (!itemsContainer) return;

		var maxItems = parseInt(itemsContainer.dataset.maxItems) || 8;
		var items = itemsContainer.querySelectorAll('.video-carousel__item');
		var currentIndex = 0;
		var visibleItems = maxItems;
		var touchStartX = 0;
		var touchEndX = 0;
		var autoPlayInterval = null;
		var autoPlayDelay = 5000;

		function isMobile() {
			return window.innerWidth <= 768;
		}

		function getItemsPerRow() {
			return isMobile() ? 2 : 4;
		}

		function getMaxRows() {
			return isMobile() ? 3 : 2;
		}

		function getVisibleItems() {
			return getItemsPerRow() * getMaxRows();
		}

		function updateVisibility() {
			visibleItems = getVisibleItems();
			items.forEach(function(item, index) {
				if (index >= currentIndex && index < currentIndex + visibleItems) {
					item.style.display = '';
					item.style.opacity = '1';
					item.style.transform = 'translateY(0)';
				} else {
					item.style.display = 'none';
					item.style.opacity = '0';
					item.style.transform = 'translateY(20px)';
				}
			});
		}

		function updateVisibilityWithAnimation() {
			visibleItems = getVisibleItems();
			
			items.forEach(function(item, index) {
				if (index >= currentIndex && index < currentIndex + visibleItems) {
					item.style.display = '';
					item.style.opacity = '0';
					item.style.transform = 'translateY(20px)';
					item.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
					
					requestAnimationFrame(function() {
						requestAnimationFrame(function() {
							item.style.opacity = '1';
							item.style.transform = 'translateY(0)';
						});
					});
				} else {
					item.style.opacity = '0';
					item.style.transform = 'translateY(-20px)';
					item.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
					item.style.display = 'none';
				}
			});
		}

		function updateGridColumns() {
			var cols = getItemsPerRow();
			itemsContainer.style.gridTemplateColumns = 'repeat(' + cols + ', 1fr)';
		}

		function updateNavigationButtons() {
			if (prevBtn) {
				prevBtn.style.display = currentIndex > 0 ? '' : 'none';
			}
			if (nextBtn) {
				nextBtn.style.display = (currentIndex + visibleItems) < items.length ? '' : 'none';
			}
		}

		function getTotalPages() {
			return Math.ceil(items.length / visibleItems);
		}

		function getCurrentPage() {
			return Math.floor(currentIndex / visibleItems);
		}

		function createPagination() {
			if (!paginationContainer) return;
			paginationContainer.innerHTML = '';
			var totalPages = getTotalPages();
			for (var i = 0; i < totalPages; i++) {
				var dot = document.createElement('span');
				dot.className = 'video-carousel__pagination-dot';
				dot.setAttribute('data-page', i);
				dot.addEventListener('click', function() {
					var page = parseInt(this.getAttribute('data-page'));
					currentIndex = page * visibleItems;
					updateVisibility();
					updateNavigationButtons();
					updatePagination();
					resetAutoPlay();
				});
				paginationContainer.appendChild(dot);
			}
		}

		function updatePagination() {
			if (!paginationContainer) return;
			var dots = paginationContainer.querySelectorAll('.video-carousel__pagination-dot');
			var currentPage = getCurrentPage();
			dots.forEach(function(dot, index) {
				if (index === currentPage) {
					dot.classList.add('active');
				} else {
					dot.classList.remove('active');
				}
			});
		}

		function goToNext() {
			if (currentIndex + visibleItems < items.length) {
				currentIndex += visibleItems;
			} else {
				currentIndex = 0;
			}
			updateVisibilityWithAnimation();
			updateNavigationButtons();
			updatePagination();
		}

		function startAutoPlay() {
			stopAutoPlay();
			autoPlayInterval = setInterval(goToNext, autoPlayDelay);
		}

		function stopAutoPlay() {
			if (autoPlayInterval) {
				clearInterval(autoPlayInterval);
				autoPlayInterval = null;
			}
		}

		function resetAutoPlay() {
			stopAutoPlay();
			startAutoPlay(); // 暂停自动轮播
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', function() {
				if (currentIndex > 0) {
					currentIndex -= visibleItems;
					if (currentIndex < 0) currentIndex = 0;
					updateVisibilityWithAnimation();
					updateNavigationButtons();
					updatePagination();
					resetAutoPlay();
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function() {
				if (currentIndex + visibleItems < items.length) {
					currentIndex += visibleItems;
					updateVisibilityWithAnimation();
					updateNavigationButtons();
					updatePagination();
					resetAutoPlay();
				}
			});
		}

		itemsContainer.addEventListener('touchstart', function(e) {
			touchStartX = e.changedTouches[0].screenX;
		}, {passive: true});

		itemsContainer.addEventListener('touchend', function(e) {
			touchEndX = e.changedTouches[0].screenX;
			handleSwipe();
		}, {passive: true});

		function handleSwipe() {
			var swipeThreshold = 50;
			var diff = touchStartX - touchEndX;

			if (Math.abs(diff) < swipeThreshold) return;

			if (diff > 0) {
				if (currentIndex + visibleItems < items.length) {
					currentIndex += visibleItems;
					updateVisibilityWithAnimation();
					updateNavigationButtons();
					updatePagination();
					resetAutoPlay();
				}
			} else {
				if (currentIndex > 0) {
					currentIndex -= visibleItems;
					if (currentIndex < 0) currentIndex = 0;
					updateVisibilityWithAnimation();
					updateNavigationButtons();
					updatePagination();
					resetAutoPlay();
				}
			}
		}

		wrapper.addEventListener('mouseenter', stopAutoPlay);
		wrapper.addEventListener('mouseleave', startAutoPlay); // 暂停自动轮播

		window.addEventListener('resize', function() {
			updateVisibility();
			updateGridColumns();
			updateNavigationButtons();
			createPagination();
			updatePagination();
		});

		updateVisibility();
		updateGridColumns();
		updateNavigationButtons();
		createPagination();
		updatePagination();
		startAutoPlay(); // 暂停自动轮播
	})();
</script>