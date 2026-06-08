<?php

/**
 * Template for FAQ Archive Page
 *
 * @package feryfit
 */

get_header(); ?>
    <?php feryfit_breadcrumb_render(); ?>
<main id="primary" class="site-main" style="background: #fff;">

    <div class="faq-archive">


        <div class="faq-archive__header">
            <h2 class="faq-archive__title">
                <?php echo esc_html( pll__( 'FAQ', 'feryfit' ) ); ?>
            </h2>
        </div>

        <?php
        if (! function_exists('feryfit_faq_archive_limit_title')) {
            function feryfit_faq_archive_limit_title($title, $length = 80)
            {
                $title = strip_tags($title);
                if (strlen($title) > $length) {
                    $title = mb_substr($title, 0, $length) . '...';
                }
                return $title;
            }
        }

        $posts_per_page = 10;
        $current_page = max(1, intval(get_query_var('paged', 1)));

        // 获取所有 FAQ 文章
        $args = array(
            'post_type' => 'faq',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);
        $all_posts = $query->posts;

        // 手动排序：置顶优先，然后按修改时间降序
        $sticky_faqs = array();
        $normal_faqs = array();

        if (! empty($all_posts)) {
            foreach ($all_posts as $post) {
                $is_pinned = get_post_meta($post->ID, '_faq_is_pinned', true);
                if ($is_pinned === '1') {
                    $sticky_faqs[] = $post;
                } else {
                    $normal_faqs[] = $post;
                }
            }

            usort($sticky_faqs, function ($a, $b) {
                return strtotime($b->post_modified) - strtotime($a->post_modified);
            });

            usort($normal_faqs, function ($a, $b) {
                return strtotime($b->post_modified) - strtotime($a->post_modified);
            });

            $posts = array_merge($sticky_faqs, $normal_faqs);
        } else {
            $posts = array();
        }

        $arrow_icon = get_template_directory_uri() . '/assets/images/right.png';

        // 如果没有数据，使用假数据
        $dummy_faqs = array(
            'How can I charge and turn on the watch?',
            'First connection?',
            'How to check the model of the watch?',
            'How to sync data?',
            'How to change C to F/ Mile to KM/ Military Time?',
            'How to receive Facebook, SMS, or incoming call notifications on my device?',
            'How to set a walking reminder?',
            'How to use the Music Control feature?',
            'How to set the Weather Reminder?',
            'How to enable the Find Phone feature?',
        );

        if (empty($posts)) {
            foreach ($dummy_faqs as $index => $faq) {
                $posts[] = (object) array(
                    'ID' => $index + 1,
                    'post_title' => $faq,
                );
            }
        }

        $total_count = count($posts);
        $total_pages = intval(max(1, ceil($total_count / $posts_per_page)));

        // 如果当前页超过总页数，重定向到最后一页
        if ($current_page > $total_pages) {
            wp_redirect(get_post_type_archive_link('faq') . ($total_pages > 1 ? '?paged=' . $total_pages : ''));
            exit;
        }

        $start = ($current_page - 1) * $posts_per_page + 1;
        $end = min($start + $posts_per_page - 1, $total_count);
        ?>

        <ul class="faq-archive__items">
            <?php $display_index = $start; ?>
            <?php foreach (array_slice($posts, ($current_page - 1) * $posts_per_page, $posts_per_page) as $post) : ?>
                <li class="faq-archive__item">
                    <a href="<?php echo esc_url(home_url('/archives/faq/' . $post->ID . '/')); ?>">
                        <span class="faq-archive__item-title">
                            <?php echo esc_html($display_index); ?>.
                            <?php echo esc_html(feryfit_faq_archive_limit_title($post->post_title, 80)); ?>
                        </span>
                        <img src="<?php echo esc_url($arrow_icon); ?>" alt="" class="faq-archive__item-arrow" />
                    </a>
                </li>
                <?php $display_index++; ?>
            <?php endforeach; ?>
        </ul>

        <div class="faq-archive__nav-wrapper">
            <?php if ($total_pages > 1) : ?>
                <div class="faq-archive__nav-container">
                    <?php if ($current_page == 1) : ?>
                        <span class="faq-archive__nav faq-archive__nav--prev faq-archive__nav--disabled">
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
                        <a href="<?php echo esc_url(add_query_arg('paged', $current_page - 1, get_post_type_archive_link('faq'))); ?>"
                            class="faq-archive__nav faq-archive__nav--prev">
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

                    <div class="faq-archive__pages">
                        <?php
                        $max_visible = 5;
                        $start_page = max(1, $current_page - floor($max_visible / 2));
                        $end_page = min($total_pages, $start_page + $max_visible - 1);

                        if ($end_page - $start_page + 1 < $max_visible) {
                            $start_page = max(1, $end_page - $max_visible + 1);
                        }

                        if ($start_page > 1) {
                        ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('faq')); ?>"
                                class="faq-archive__page">1</a>
                            <?php
                            if ($start_page > 2) {
                                echo '<span class="faq-archive__dots">...</span>';
                            }
                        }

                        for ($i = $start_page; $i <= $end_page; $i++) {
                            ?>
                            <a href="<?php echo esc_url($i === 1 ? get_post_type_archive_link('faq') : add_query_arg('paged', $i, get_post_type_archive_link('faq'))); ?>"
                                class="faq-archive__page <?php echo $current_page === $i ? 'faq-archive__page--active' : ''; ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                        <?php
                        }

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<span class="faq-archive__dots">...</span>';
                            }
                        ?>
                            <a href="<?php echo esc_url(add_query_arg('paged', $total_pages, get_post_type_archive_link('faq'))); ?>"
                                class="faq-archive__page"><?php echo esc_html($total_pages); ?></a>
                        <?php
                        }
                        ?>
                    </div>

                    <div class="faq-archive__mobile-counter">
                        <span class="faq-archive__current"><?php echo esc_html($current_page); ?></span>
                        <span class="faq-archive__separator">/</span>
                        <span class="faq-archive__total"><?php echo esc_html($total_pages); ?></span>
                    </div>

                    <?php if ($current_page == $total_pages) : ?>
                        <span class="faq-archive__nav faq-archive__nav--next faq-archive__nav--disabled">
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
                        <a href="<?php echo esc_url(add_query_arg('paged', $current_page + 1, get_post_type_archive_link('faq'))); ?>"
                            class="faq-archive__nav faq-archive__nav--next">
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
