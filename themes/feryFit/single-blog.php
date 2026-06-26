<?php

/**
 * Template for Blog Single Post
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

			</header><!-- .entry-header -->

			<?php if (has_post_thumbnail()) : ?>
				<div class="entry-thumbnail">
					<?php the_post_thumbnail('large'); ?>
				</div>
			<?php endif; ?>

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
		<div class="blog-feedback-wrapper" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
			<hr style="border: 1px solid  #B8B8B8;">
			<div class="blog-like-button-box">
				<button class="blog-like-button loading" data-vote="yes">
					<svg class="blog-like-icon" xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33" fill="none">
						<path d="M2.87929 15.2393C2.80946 14.4361 3.44255 13.7456 4.2487 13.7456H6.87312C7.6323 13.7456 8.24769 14.361 8.24769 15.1202V28.1786C8.24769 28.9378 7.6323 29.5532 6.87312 29.5532H5.38421C4.67121 29.5532 4.07657 29.008 4.01481 28.2977L2.87929 15.2393Z" fill="white" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M12.3711 14.6905C12.3711 14.1161 12.7281 13.602 13.2476 13.3571C14.3806 12.8231 16.3112 11.7484 17.1821 10.296C18.3045 8.42408 18.5162 5.04199 18.5506 4.2673C18.5554 4.15885 18.5523 4.05028 18.5672 3.94275C18.7533 2.6011 21.3431 4.16843 22.3367 5.82561C22.876 6.72498 22.9451 7.90663 22.8884 8.82917C22.8277 9.81625 22.5383 10.7698 22.2544 11.7171L21.6494 13.7351H29.1112C30.0228 13.7351 30.6818 14.6061 30.434 15.4833L26.7431 28.5524C26.5759 29.1445 26.0356 29.5534 25.4203 29.5534H13.7457C12.9865 29.5534 12.3711 28.938 12.3711 28.1788V14.6905Z" fill="white" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
					<span class="blog-like-count">...</span>
				</button>
			</div>
			<div class="blog-comment-section">
				<form class="blog-comment-form" id="blog-comment-form">
					<textarea class="blog-comment-textarea" name="comment" placeholder="<?php echo esc_attr(pll__('Share your thoughts...')); ?>" rows="4" required></textarea>
					<div class="blog-comment-button-wrapper">
						<button type="submit" class="blog-comment-submit">
							<span class="blog-submit-text"><?php echo esc_html(pll__('Submit')); ?></span>
						</button>
						<span class="blog-submit-icon"></span>
					</div>
				</form>
			</div>
		</div>
	<?php

	endwhile; // End of the loop.
	?>

</main><!-- #primary -->

<?php
// 调用客服浮窗组件
feryfit_floating_chat(array(
	'whatsapp' => get_option('feryfit_whatsapp', ''),
	'email'    => 'mailto:' . get_option('feryfit_email', ''),
	'facebook' => get_option('feryfit_facebook', ''),
));
?>

<script>
	var feryfitBlogNonce = '<?php echo esc_js( wp_create_nonce( 'feryfit_blog_action' ) ); ?>';

	// Daily like tracking via localStorage
	function blogGetToday() {
		var d = new Date();
		return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
	}

	function blogGetLikes() {
		try {
			var raw = localStorage.getItem('blog_likes');
			var data = raw ? JSON.parse(raw) : {};
			// Clean up stale dates
			var today = blogGetToday();
			var cleaned = {};
			if (data[today]) {
				cleaned[today] = data[today];
			}
			if (Object.keys(data).length !== Object.keys(cleaned).length) {
				localStorage.setItem('blog_likes', JSON.stringify(cleaned));
			}
			return cleaned;
		} catch(e) {
			return {};
		}
	}

	function blogHasLikedToday(postId) {
		var likes = blogGetLikes();
		var today = blogGetToday();
		return !!(likes[today] && likes[today][postId]);
	}

	function blogMarkLikedToday(postId) {
		var likes = blogGetLikes();
		var today = blogGetToday();
		if (!likes[today]) {
			likes[today] = {};
		}
		likes[today][postId] = true;
		try {
			localStorage.setItem('blog_likes', JSON.stringify(likes));
		} catch(e) {}
	}

	jQuery(document).ready(function($) {
		var $feedbackWrapper = $('.blog-feedback-wrapper');

		if ($feedbackWrapper.length === 0) {
			console.log('blog-feedback-wrapper not found');
			return;
		}

		var postId = $feedbackWrapper.data('post-id');
		var $likeButton = $feedbackWrapper.find('.blog-like-button');

		// On load: fetch count + check if already liked today
		$.ajax({
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			type: 'POST',
			data: {
				action: 'feryfit_get_post_likes',
				post_id: postId
			},
			success: function(response) {
				if (response.success) {
					$likeButton.find('.blog-like-count').text(response.data.yes_votes);
				}
				initLikeButton();
			},
			error: function() {
				$likeButton.find('.blog-like-count').text('0');
				initLikeButton();
			}
		});

		function initLikeButton() {
			$likeButton.removeClass('loading');

			if (blogHasLikedToday(postId)) {
				$likeButton.addClass('blog-like-button--liked');
				$likeButton.prop('disabled', true);
				$likeButton.attr('title', '<?php echo esc_js(pll__('Liked')); ?>');
			}
		}

		$likeButton.on('click', function(e) {
			e.preventDefault();

			if ($likeButton.hasClass('blog-like-button--liked')) {
				return;
			}

			var vote = $likeButton.data('vote');
			var currentCount = parseInt($likeButton.find('.blog-like-count').text()) || 0;

			$likeButton.prop('disabled', true);
			$likeButton.find('.blog-like-count').text('...');

			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'POST',
				data: {
					action: 'feryfit_post_like',
					post_id: postId,
					vote: vote,
					nonce: feryfitBlogNonce
				},
				success: function(response) {
					if (response.success) {
						blogMarkLikedToday(postId);
						$likeButton.addClass('blog-like-button--liked');
						$likeButton.find('.blog-like-count').text(currentCount + 1);
					} else {
						// Already liked (server-side reject) or other error
						blogMarkLikedToday(postId);
						$likeButton.addClass('blog-like-button--liked');
						$likeButton.prop('disabled', true);
						// Use the count returned by server
						if (response.data && typeof response.data.yes_votes !== 'undefined') {
							$likeButton.find('.blog-like-count').text(response.data.yes_votes);
						} else {
							$likeButton.find('.blog-like-count').text(currentCount);
						}
					}
				},
				error: function(xhr, status, error) {
					console.error('Like error:', status, error);
					// Restore count on network error (don't mark as liked)
					$likeButton.prop('disabled', false);
					$.ajax({
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						type: 'POST',
						data: {
							action: 'feryfit_get_post_likes',
							post_id: postId
						},
						success: function(resp) {
							if (resp.success) {
								$likeButton.find('.blog-like-count').text(resp.data.yes_votes);
							}
						}
					});
				}
			});
		});

		$feedbackWrapper.on('submit', '#blog-comment-form', function(e) {
			e.preventDefault();

			var $form = $(this);
			var $submitBtn = $form.find('.blog-comment-submit');
			var $submitIcon = $form.find('.blog-submit-icon');

			$submitBtn.prop('disabled', true).addClass('loading');
			$submitBtn.find('.blog-submit-text').text('<?php echo esc_js(pll__('Submitting...')); ?>');

			var formData = {
				action: 'feryfit_submit_blog_comment',
				post_id: postId,
				comment: $form.find('[name="comment"]').val(),
				nonce: feryfitBlogNonce
			};

			console.log('Submitting comment:', formData);

			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'POST',
				data: formData,
				success: function(response) {
					console.log('Comment AJAX response:', response);

					if (response.success) {
						$submitBtn.hide();
						$submitIcon.html('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none"><path d="M24 44C29.5228 44 34.5228 41.7614 38.1421 38.1421C41.7614 34.5228 44 29.5228 44 24C44 18.4772 41.7614 13.4772 38.1421 9.85786C34.5228 6.23858 29.5228 4 24 4C18.4772 4 13.4772 6.23858 9.85786 9.85786C6.23858 13.4772 4 18.4772 4 24C4 29.5228 6.23858 34.5228 9.85786 38.1421C13.4772 41.7614 18.4772 44 24 44Z" stroke="#13A856" stroke-width="3" stroke-linejoin="round"/><path d="M16 24L22 30L34 18" stroke="#13A856" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>');
						$submitIcon.show();
						$form.find('textarea').val('');

						setTimeout(function() {
							$submitIcon.hide();
							$submitBtn.show();
							$submitBtn.prop('disabled', false).removeClass('loading');
							$submitBtn.find('.blog-submit-text').text('<?php echo esc_js(pll__('Submit')); ?>');
						}, 2000);
					} else {
						$submitBtn.hide();
						$submitIcon.html('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="20" stroke="#E72B19" stroke-width="3"/><path d="M16 16L32 32M32 16L16 32" stroke="#E72B19" stroke-width="3" stroke-linecap="round"/></svg>');
						$submitIcon.show();

						setTimeout(function() {
							$submitIcon.hide();
							$submitBtn.show();
							$submitBtn.prop('disabled', false).removeClass('loading');
							$submitBtn.find('.blog-submit-text').text('<?php echo esc_js(pll__('Submit')); ?>');
						}, 2000);
					}
				},
				error: function(xhr, status, error) {
					console.error('Comment AJAX error:', status, error);
					$submitBtn.hide();
					$submitIcon.html('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="20" stroke="#E72B19" stroke-width="3"/><path d="M16 16L32 32M32 16L16 32" stroke="#E72B19" stroke-width="3" stroke-linecap="round"/></svg>');
					$submitIcon.show();

					setTimeout(function() {
						$submitIcon.hide();
						$submitBtn.show();
						$submitBtn.prop('disabled', false).removeClass('loading');
						$submitBtn.find('.blog-submit-text').text('<?php echo esc_js(pll__('Submit')); ?>');
					}, 2000);
				}
			});
		});
	});
</script>

<?php
get_sidebar();
get_footer();
