<?php

/**
 * Template for Video Archive Page
 *
 * @package feryfit
 */

get_header(); ?>
    <?php feryfit_breadcrumb_render(); ?>
<main id="primary" class="site-main" style="background: #fff;">

    <div class="video-archive">


        <div class="video-archive__header">
            <h2 class="video-archive__title"><?php echo esc_html( pll__( 'Video', 'feryfit' ) ); ?></h2>
        </div>

        <?php
        $posts_per_page = 12;
        $current_page = max(1, intval(get_query_var('paged', 1)));

        $video_posts = array();

        // 使用 video_content 数据
        if (function_exists('feryfit_get_video_contents')) {
            $raw_posts = feryfit_get_video_contents(array(
                'posts_per_page' => -1,
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

        // Mock data if no videos found
        if (empty($video_posts)) {
            for ($i = 1; $i <= 12; $i++) {
                $video_posts[] = array(
                    'id'                => $i,
                    'title'             => '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep monitoring',
                    'youtube_thumbnail' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop',
                    'thumbnail'         => '',
                    'duration'          => '2:' . sprintf('%02d', (8 + $i) % 60),
                    'permalink'         => '#',
                );
            }
        }

        $total_count = count($video_posts);
        $total_pages = intval(max(1, ceil($total_count / $posts_per_page)));

        // 动态生成视频归档页面链接，支持 Polylang 多语言
        $archive_link = home_url('/video/');
        if (function_exists('pll_current_language')) {
            $current_lang = pll_current_language();
            $default_lang = pll_default_language();
            if ($current_lang && $current_lang !== $default_lang) {
                $archive_link = home_url('/' . $current_lang . '/video/');
            }
        }

        // Redirect if current page exceeds total pages
        if ($current_page > $total_pages) {
            wp_redirect($archive_link . ($total_pages > 1 ? 'page/' . $total_pages . '/' : ''));
            exit;
        }

        $start = ($current_page - 1) * $posts_per_page + 1;
        $end = min($start + $posts_per_page - 1, $total_count);

        // Get videos for current page
        $display_videos = array_slice($video_posts, ($current_page - 1) * $posts_per_page, $posts_per_page);
        ?>

        <div class="video-archive__grid">
            <?php foreach ($display_videos as $video) : ?>
                <div class="video-card">
                    <a href="<?php echo esc_url($video['permalink']); ?>" class="video-card__link">
                        <div class="video-card__thumbnail">
                            <?php if (!empty($video['youtube_thumbnail'])) : ?>
                                <img
                                    src="<?php echo esc_url($video['youtube_thumbnail']); ?>"
                                    alt="<?php echo esc_attr($video['title']); ?>"
                                    class="video-card__image"
                                    loading="lazy" />
                            <?php elseif (!empty($video['thumbnail'])) : ?>
                                <img
                                    src="<?php echo esc_url($video['thumbnail']); ?>"
                                    alt="<?php echo esc_attr($video['title']); ?>"
                                    class="video-card__image"
                                    loading="lazy" />
                            <?php endif; ?>
                            <button class="video-card__play-btn" aria-label="<?php esc_attr_e('Play video', 'feryfit'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                    <circle cx="25" cy="25" r="24.5" fill="black" fill-opacity="0.2" stroke="white" />
                                    <path d="M33 25L21 18.0718V31.9282L33 25Z" fill="white" />
                                </svg>
                            </button>
                            <?php if (!empty($video['duration'])) : ?>
                                <span class="video-card__duration"><?php echo esc_html($video['duration']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="video-card__title">
                            <?php echo esc_html($video['title']); ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="video-archive__pagination">
            <?php if ($total_pages > 1) : ?>
                <div class="video-archive__pagination-container">
                    <?php if ($current_page == 1) : ?>
                        <span class="video-archive__pagination-nav video-archive__pagination-nav--prev video-archive__pagination-nav--disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                <g clip-path="url(#clip0_prev)">
                                    <path d="M18.4125 22.15C18.315 22.2479 18.199 22.3256 18.0714 22.3786C17.9438 22.4315 17.8069 22.4588 17.6688 22.4588C17.5306 22.4588 17.3937 22.4315 17.2661 22.3786C17.1385 22.3256 17.0226 22.2479 16.925 22.15L10 15.725C9.89439 15.6267 9.81015 15.5078 9.75254 15.3755C9.69494 15.2432 9.66522 15.1005 9.66522 14.9562C9.66522 14.812 9.69494 14.6693 9.75254 14.537C9.81015 14.4047 9.89439 14.2858 10 14.1875L16.9 7.725C17.1039 7.53437 17.3752 7.43255 17.6541 7.44193C17.9331 7.45131 18.1969 7.57111 18.3875 7.775C18.5781 7.97888 18.68 8.25014 18.6706 8.52911C18.6612 8.80807 18.5414 9.07188 18.3375 9.2625L12.2875 15L18.375 20.7C18.5666 20.8905 18.6774 21.1474 18.6844 21.4175C18.6914 21.6875 18.594 21.9499 18.4125 22.15ZM15 0C12.0333 0 9.13319 0.879735 6.66645 2.52796C4.19972 4.17618 2.27713 6.51886 1.14181 9.25975C0.00649922 12.0006 -0.290551 15.0166 0.288227 17.9264C0.867006 20.8361 2.29562 23.5088 4.3934 25.6066C6.49119 27.7044 9.16394 29.133 12.0737 29.7118C14.9834 30.2906 17.9994 29.9935 20.7403 28.8582C23.4811 27.7229 25.8238 25.8003 27.472 23.3336C29.1203 20.8668 30 17.9667 30 15C30 11.0218 28.4197 7.20644 25.6066 4.3934C22.7936 1.58035 18.9783 0 15 0Z" fill="#B8B8B8" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_prev">
                                        <rect width="30" height="30" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    <?php else : ?>
                        <a href="<?php echo esc_url($archive_link . 'page/' . ($current_page - 1) . '/'); ?>"
                            class="video-archive__pagination-nav video-archive__pagination-nav--prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                <g clip-path="url(#clip0_prev_active)">
                                    <path d="M18.4125 22.15C18.315 22.2479 18.199 22.3256 18.0714 22.3786C17.9438 22.4315 17.8069 22.4588 17.6688 22.4588C17.5306 22.4588 17.3937 22.4315 17.2661 22.3786C17.1385 22.3256 17.0226 22.2479 16.925 22.15L10 15.725C9.89439 15.6267 9.81015 15.5078 9.75254 15.3755C9.69494 15.2432 9.66522 15.1005 9.66522 14.9562C9.66522 14.812 9.69494 14.6693 9.75254 14.537C9.81015 14.4047 9.89439 14.2858 10 14.1875L16.9 7.725C17.1039 7.53437 17.3752 7.43255 17.6541 7.44193C17.9331 7.45131 18.1969 7.57111 18.3875 7.775C18.5781 7.97888 18.68 8.25014 18.6706 8.52911C18.6612 8.80807 18.5414 9.07188 18.3375 9.2625L12.2875 15L18.375 20.7C18.5666 20.8905 18.6774 21.1474 18.6844 21.4175C18.6914 21.6875 18.594 21.9499 18.4125 22.15ZM15 0C12.0333 0 9.13319 0.879735 6.66645 2.52796C4.19972 4.17618 2.27713 6.51886 1.14181 9.25975C0.00649922 12.0006 -0.290551 15.0166 0.288227 17.9264C0.867006 20.8361 2.29562 23.5088 4.3934 25.6066C6.49119 27.7044 9.16394 29.133 12.0737 29.7118C14.9834 30.2906 17.9994 29.9935 20.7403 28.8582C23.4811 27.7229 25.8238 25.8003 27.472 23.3336C29.1203 20.8668 30 17.9667 30 15C30 11.0218 28.4197 7.20644 25.6066 4.3934C22.7936 1.58035 18.9783 0 15 0Z" fill="black" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_prev_active">
                                        <rect width="30" height="30" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    <?php endif; ?>

                    <div class="video-archive__pagination-pages">
                        <?php
                        $max_visible = 5;
                        $start_page = max(1, $current_page - floor($max_visible / 2));
                        $end_page = min($total_pages, $start_page + $max_visible - 1);

                        if ($end_page - $start_page + 1 < $max_visible) {
                            $start_page = max(1, $end_page - $max_visible + 1);
                        }

                        if ($start_page > 1) {
                        ?>
                            <a href="<?php echo esc_url($archive_link); ?>"
                                class="video-archive__pagination-page">1</a>
                            <?php
                            if ($start_page > 2) {
                                echo '<span class="video-archive__pagination-dots">...</span>';
                            }
                        }

                        for ($i = $start_page; $i <= $end_page; $i++) {
                            ?>
                            <a href="<?php echo esc_url($i === 1 ? $archive_link : add_query_arg('paged', $i, $archive_link)); ?>"
                                class="video-archive__pagination-page <?php echo $current_page === $i ? 'video-archive__pagination-page--active' : ''; ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                        <?php
                        }

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<span class="video-archive__pagination-dots">...</span>';
                            }
                        ?>
                            <a href="<?php echo esc_url(add_query_arg('paged', $total_pages, $archive_link)); ?>"
                                class="video-archive__pagination-page"><?php echo esc_html($total_pages); ?></a>
                        <?php
                        }
                        ?>
                    </div>

                    <div class="video-archive__pagination-mobile-counter">
                        <span class="video-archive__pagination-current"><?php echo esc_html($current_page); ?></span>
                        <span class="video-archive__pagination-separator">/</span>
                        <span class="video-archive__pagination-total"><?php echo esc_html($total_pages); ?></span>
                    </div>

                    <?php if ($current_page == $total_pages) : ?>
                        <span class="video-archive__pagination-nav video-archive__pagination-nav--next video-archive__pagination-nav--disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                <g clip-path="url(#clip0_next)">
                                    <path d="M11.5875 7.85C11.685 7.75211 11.801 7.67444 11.9286 7.62145C12.0562 7.56845 12.1931 7.54117 12.3312 7.54117C12.4694 7.54117 12.6063 7.56845 12.7339 7.62145C12.8615 7.67444 12.9774 7.75211 13.075 7.85L20 14.275C20.1056 14.3733 20.1899 14.4922 20.2475 14.6245C20.3051 14.7568 20.3348 14.8995 20.3348 15.0438C20.3348 15.188 20.3051 15.3307 20.2475 15.463C20.1899 15.5953 20.1056 15.7142 20 15.8125L13.1 22.275C12.8961 22.4656 12.6248 22.5674 12.3459 22.5581C12.0669 22.5487 11.8031 22.4289 11.6125 22.225C11.4219 22.0211 11.32 21.7499 11.3294 21.4709C11.3388 21.1919 11.4586 20.9281 11.6625 20.7375L17.7125 15L11.625 9.3C11.4334 9.10954 11.3226 8.85257 11.3156 8.58252C11.3086 8.31247 11.406 8.05011 11.5875 7.85ZM15 30C17.9667 30 20.8668 29.1203 23.3335 27.472C25.8003 25.8238 27.7229 23.4811 28.8582 20.7403C29.9935 17.9994 30.2906 14.9834 29.7118 12.0736C29.133 9.16393 27.7044 6.49119 25.6066 4.3934C23.5088 2.29561 20.8361 0.867001 17.9263 0.288223C15.0166 -0.290556 12.0006 0.00649452 9.25974 1.14181C6.51885 2.27712 4.17617 4.19971 2.52795 6.66645C0.879728 9.13318 -5.72205e-06 12.0333 -5.72205e-06 15C-5.72205e-06 18.9782 1.58035 22.7936 4.39339 25.6066C7.20644 28.4196 11.0217 30 15 30Z" fill="#B8B8B8" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_next">
                                        <rect width="30" height="30" fill="white" transform="matrix(-1 0 0 -1 30 30)" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    <?php else : ?>
                        <a href="<?php echo esc_url(add_query_arg('paged', $current_page + 1, $archive_link)); ?>"
                            class="video-archive__pagination-nav video-archive__pagination-nav--next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                <g clip-path="url(#clip0_next_active)">
                                    <path d="M11.5875 7.85C11.685 7.75211 11.801 7.67444 11.9286 7.62145C12.0562 7.56845 12.1931 7.54117 12.3312 7.54117C12.4694 7.54117 12.6063 7.56845 12.7339 7.62145C12.8615 7.67444 12.9774 7.75211 13.075 7.85L20 14.275C20.1056 14.3733 20.1899 14.4922 20.2475 14.6245C20.3051 14.7568 20.3348 14.8995 20.3348 15.0438C20.3348 15.188 20.3051 15.3307 20.2475 15.463C20.1899 15.5953 20.1056 15.7142 20 15.8125L13.1 22.275C12.8961 22.4656 12.6248 22.5674 12.3459 22.5581C12.0669 22.5487 11.8031 22.4289 11.6125 22.225C11.4219 22.0211 11.32 21.7499 11.3294 21.4709C11.3388 21.1919 11.4586 20.9281 11.6625 20.7375L17.7125 15L11.625 9.3C11.4334 9.10954 11.3226 8.85257 11.3156 8.58252C11.3086 8.31247 11.406 8.05011 11.5875 7.85ZM15 30C17.9667 30 20.8668 29.1203 23.3335 27.472C25.8003 25.8238 27.7229 23.4811 28.8582 20.7403C29.9935 17.9994 30.2906 14.9834 29.7118 12.0736C29.133 9.16393 27.7044 6.49119 25.6066 4.3934C23.5088 2.29561 20.8361 0.867001 17.9263 0.288223C15.0166 -0.290556 12.0006 0.00649452 9.25974 1.14181C6.51885 2.27712 4.17617 4.19971 2.52795 6.66645C0.879728 9.13318 -5.72205e-06 12.0333 -5.72205e-06 15C-5.72205e-06 18.9782 1.58035 22.7936 4.39339 25.6066C7.20644 28.4196 11.0217 30 15 30Z" fill="black" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_next_active">
                                        <rect width="30" height="30" fill="white" transform="matrix(-1 0 0 -1 30 30)" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
