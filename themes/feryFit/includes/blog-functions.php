<?php
/**
 * Blog Helper Functions
 * 
 * Functions to retrieve and display Blog posts on the frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get all published Blog posts ordered by pinned status and menu_order
 * 
 * @param array $args Optional. Additional arguments for WP_Query.
 * @return array Array of Blog posts.
 */
function feryfit_get_blogs( $args = array() ) {
    $defaults = array(
        'post_type'      => 'blog',
        'posts_per_page' => -1,
        'orderby'        => array(
            'meta_value_num' => 'DESC',
            'menu_order'     => 'ASC',
        ),
        'meta_key'       => '_blog_is_pinned',
        'post_status'    => 'publish',
        'lang'           => '',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    if ( empty( $args['lang'] ) && function_exists( 'pll_current_language' ) ) {
        $args['lang'] = pll_current_language();
    }
    
    return get_posts( $args );
}

/**
 * Get Blog posts by category
 * 
 * @param string|int $category Category slug or ID.
 * @param array $args Optional. Additional arguments for WP_Query.
 * @return array Array of Blog posts.
 */
function feryfit_get_blogs_by_category( $category, $args = array() ) {
    $defaults = array(
        'post_type'      => 'blog',
        'posts_per_page' => -1,
        'orderby'        => array(
            'meta_value_num' => 'DESC',
            'menu_order'     => 'ASC',
        ),
        'meta_key'       => '_blog_is_pinned',
        'post_status'    => 'publish',
        'lang'           => '',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    if ( empty( $args['lang'] ) && function_exists( 'pll_current_language' ) ) {
        $args['lang'] = pll_current_language();
    }
    
    if ( ! empty( $category ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'blog_category',
                'field'    => is_numeric( $category ) ? 'term_id' : 'slug',
                'terms'    => $category,
            ),
        );
    }
    
    return get_posts( $args );
}

/**
 * Get all Blog categories
 * 
 * @return array Array of category terms.
 */
function feryfit_get_blog_categories() {
    return get_terms( array(
        'taxonomy'   => 'blog_category',
        'hide_empty' => false,
    ) );
}

/**
 * Get a single Blog by ID
 * 
 * @param int $blog_id Blog post ID.
 * @return WP_Post|null Blog post object or null if not found.
 */
function feryfit_get_blog( $blog_id ) {
    return get_post( $blog_id );
}

/**
 * Get Blog title
 * 
 * @param int $blog_id Blog post ID.
 * @return string Blog title.
 */
function feryfit_get_blog_title( $blog_id ) {
    return get_the_title( $blog_id );
}

/**
 * Get Blog content
 * 
 * @param int  $blog_id Blog post ID.
 * @param bool $apply_filters Whether to apply content filters. Default true.
 * @return string Blog content.
 */
function feryfit_get_blog_content( $blog_id, $apply_filters = true ) {
    $content = get_the_content( '', false, $blog_id );
    
    if ( $apply_filters ) {
        $content = apply_filters( 'the_content', $content );
    }
    
    return $content;
}

/**
 * Get Blog featured image URL
 * 
 * @param int $blog_id Blog post ID.
 * @param string $size Image size. Default 'full'.
 * @return string|null Image URL or null if not found.
 */
function feryfit_get_blog_featured_image( $blog_id, $size = 'full' ) {
    $image_id = get_post_thumbnail_id( $blog_id );
    if ( $image_id ) {
        $image = wp_get_attachment_image_src( $image_id, $size );
        return $image ? $image[0] : null;
    }
    return null;
}

/**
 * Display Blog posts as an accordion
 * 
 * @param array $args Optional. Arguments to customize the output.
 */
function feryfit_display_blogs_accordion( $args = array() ) {
    $defaults = array(
        'limit'               => -1,
        'show_search'         => true,
        'show_categories'     => true,
        'search_placeholder'  => __( '搜索博客...', 'feryfit' ),
        'category_placeholder' => __( '所有分类', 'feryfit' ),
        'container_class'     => 'blogs-accordion',
        'item_class'          => 'blog-accordion-item',
        'title_class'         => 'blog-accordion-title',
        'content_class'       => 'blog-accordion-content',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    $blogs = feryfit_get_blogs( array(
        'posts_per_page' => $args['limit'],
    ) );
    
    $categories = $args['show_categories'] ? feryfit_get_blog_categories() : array();
    
    if ( empty( $blogs ) ) {
        echo '<p>' . __( '暂无博客文章。', 'feryfit' ) . '</p>';
        return;
    }
    ?>
    <div class="<?php echo esc_attr( $args['container_class'] ); ?>">
        <?php if ( $args['show_search'] || ( $args['show_categories'] && ! empty( $categories ) ) ) : ?>
            <div class="blogs-filters">
                <?php if ( $args['show_search'] ) : ?>
                    <div class="blogs-search">
                        <input type="text" class="blogs-search-input" placeholder="<?php echo esc_attr( $args['search_placeholder'] ); ?>">
                    </div>
                <?php endif; ?>
                
                <?php if ( $args['show_categories'] && ! empty( $categories ) ) : ?>
                    <div class="blogs-category-filter">
                        <select class="blogs-category-select">
                            <option value=""><?php echo esc_html( $args['category_placeholder'] ); ?></option>
                            <?php foreach ( $categories as $category ) : ?>
                                <option value="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="blogs-accordion-list">
            <?php foreach ( $blogs as $blog ) : 
                $is_pinned = get_post_meta( $blog->ID, '_blog_is_pinned', true );
                $blog_categories = get_the_terms( $blog->ID, 'blog_category' );
                $category_slugs = is_array( $blog_categories ) && ! empty( $blog_categories ) ? wp_list_pluck( $blog_categories, 'slug' ) : array();
                $featured_image = feryfit_get_blog_featured_image( $blog->ID, 'medium' );
            ?>
                <div class="<?php echo esc_attr( $args['item_class'] ); ?> <?php echo $is_pinned ? 'blog-item-pinned' : ''; ?>" 
                     data-blog-id="<?php echo esc_attr( $blog->ID ); ?>"
                     data-categories="<?php echo esc_attr( implode( ',', $category_slugs ) ); ?>">
                    <div class="<?php echo esc_attr( $args['title_class'] ); ?>">
                        <h3>
                            <button type="button" class="blog-toggle">
                                <?php if ( $is_pinned ) : ?>
                                    <span class="blog-pinned-icon" title="<?php _e( 'Pinned', 'feryfit' ); ?>">📌</span>
                                <?php endif; ?>
                                <?php echo esc_html( get_the_title( $blog->ID ) ); ?>
                                <span class="blog-toggle-icon">+</span>
                            </button>
                        </h3>
                    </div>
                    <div class="<?php echo esc_attr( $args['content_class'] ); ?>">
                        <div class="blog-content-wrapper">
                            <?php if ( $featured_image ) : ?>
                                <img src="<?php echo esc_url( $featured_image ); ?>" alt="<?php echo esc_attr( get_the_title( $blog->ID ) ); ?>" class="blog-featured-image">
                            <?php endif; ?>
                            <div class="blog-content-text">
                                <?php echo feryfit_get_blog_content( $blog->ID ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
        .blogs-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .blogs-search {
            flex: 1;
            min-width: 200px;
        }
        .blogs-search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .blogs-category-filter {
            min-width: 200px;
        }
        .blogs-category-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: #fff;
            cursor: pointer;
        }
        .blog-accordion-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .blog-accordion-item.blog-item-pinned {
            border-color: #fbbf24;
            border-width: 2px;
        }
        .blog-accordion-title {
            margin: 0;
        }
        .blog-toggle {
            width: 100%;
            text-align: left;
            padding: 15px 20px;
            background: #f9f9f9;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .blog-accordion-item.blog-item-pinned .blog-toggle {
            background: #fffbeb;
        }
        .blog-toggle:hover {
            background: #f0f0f0;
        }
        .blog-accordion-item.blog-item-pinned .blog-toggle:hover {
            background: #fef3c7;
        }
        .blog-pinned-icon {
            font-size: 14px;
        }
        .blog-toggle-icon {
            font-size: 20px;
            font-weight: 300;
            transition: transform 0.3s ease;
        }
        .blog-accordion-item.active .blog-toggle-icon {
            transform: rotate(45deg);
        }
        .blog-accordion-content {
            display: none;
            padding: 20px;
            background: #fff;
            border-top: 1px solid #ddd;
        }
        .blog-accordion-item.active .blog-accordion-content {
            display: block;
        }
        .blog-content-wrapper {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .blog-featured-image {
            max-width: 200px;
            height: auto;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .blog-content-text {
            flex: 1;
            min-width: 200px;
        }
        .blog-accordion-item.hidden {
            display: none;
        }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Blog accordion toggle
        document.querySelectorAll('.blog-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                var item = this.closest('.blog-accordion-item');
                item.classList.toggle('active');
            });
        });
        
        // Blog search
        var searchInput = document.querySelector('.blogs-search-input');
        var categorySelect = document.querySelector('.blogs-category-select');
        
        function filterBlogs() {
            var searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            var selectedCategory = categorySelect ? categorySelect.value : '';
            
            document.querySelectorAll('.blog-accordion-item').forEach(function(item) {
                var title = item.querySelector('.blog-toggle').textContent.toLowerCase();
                var content = item.querySelector('.blog-content-text').textContent.toLowerCase();
                var itemCategories = item.getAttribute('data-categories') || '';
                
                var matchesSearch = searchTerm === '' || title.indexOf(searchTerm) > -1 || content.indexOf(searchTerm) > -1;
                var matchesCategory = selectedCategory === '' || itemCategories.indexOf(selectedCategory) > -1;
                
                if (matchesSearch && matchesCategory) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', filterBlogs);
        }
        
        if (categorySelect) {
            categorySelect.addEventListener('change', filterBlogs);
        }
    });
    </script>
    <?php
}

/**
 * Handle blog post like via AJAX
 */
function feryfit_handle_blog_like() {
    if ( ! check_ajax_referer( 'feryfit_blog_action', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
    }

    if ( ! isset( $_POST['post_id'], $_POST['vote'] ) ) {
        wp_send_json_error( array( 'message' => '缺少参数' ) );
    }

    $post_id = intval( wp_unslash( $_POST['post_id'] ) );
    $vote = sanitize_text_field( wp_unslash( $_POST['vote'] ) );

    if ( 'blog' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
        wp_send_json_error( array( 'message' => '无效的文章' ), 400 );
    }

    if ( ! in_array( $vote, array( 'yes', 'no' ) ) ) {
        wp_send_json_error( array( 'message' => '无效的投票选项' ) );
    }

    $ip = feryfit_get_client_ip();
    if ( ! feryfit_rate_limit_check( 'blog_like:' . $ip, 30, MINUTE_IN_SECONDS ) ) {
        wp_send_json_error( array( 'message' => '请求过于频繁' ), 429 );
    }

    // IP-based daily rate limit: one like per post per IP per day
    $today = gmdate( 'Y-m-d' );
    $transient_key = 'blog_like_' . $post_id . '_' . md5( $ip ) . '_' . $today;
    if ( get_transient( $transient_key ) ) {
        // Already liked today, return current count without incrementing
        $yes_votes = get_post_meta( $post_id, '_blog_yes_votes', true );
        $no_votes = get_post_meta( $post_id, '_blog_no_votes', true );
        wp_send_json_error( array(
            'message' => '今日已点赞',
            'yes_votes' => empty( $yes_votes ) ? 0 : intval( $yes_votes ),
            'no_votes' => empty( $no_votes ) ? 0 : intval( $no_votes ),
        ) );
    }

    $yes_votes = get_post_meta( $post_id, '_blog_yes_votes', true );
    $no_votes = get_post_meta( $post_id, '_blog_no_votes', true );

    if ( empty( $yes_votes ) ) {
        $yes_votes = 0;
    }
    if ( empty( $no_votes ) ) {
        $no_votes = 0;
    }

    if ( $vote === 'yes' ) {
        $yes_votes++;
        update_post_meta( $post_id, '_blog_yes_votes', $yes_votes );
    } else {
        $no_votes++;
        update_post_meta( $post_id, '_blog_no_votes', $no_votes );
    }

    // Set transient to prevent re-like (expires at end of day GMT)
    $seconds_until_midnight = strtotime( 'tomorrow GMT' ) - time();
    set_transient( $transient_key, 1, $seconds_until_midnight );

    wp_send_json_success( array(
        'yes_votes' => $yes_votes,
        'no_votes' => $no_votes,
        'total_votes' => $yes_votes + $no_votes,
    ) );
}
add_action( 'wp_ajax_feryfit_post_like', 'feryfit_handle_blog_like' );
add_action( 'wp_ajax_nopriv_feryfit_post_like', 'feryfit_handle_blog_like' );

/**
 * Get blog post likes via AJAX
 */
function feryfit_ajax_get_blog_likes() {
    if ( ! isset( $_POST['post_id'] ) ) {
        wp_send_json_error( array( 'message' => '缺少参数' ) );
    }

    $post_id = intval( wp_unslash( $_POST['post_id'] ) );
    if ( 'blog' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
        wp_send_json_error( array( 'message' => '无效的文章' ), 400 );
    }

    $yes_votes = get_post_meta( $post_id, '_blog_yes_votes', true );
    $no_votes = get_post_meta( $post_id, '_blog_no_votes', true );

    $yes_votes = empty( $yes_votes ) ? 0 : intval( $yes_votes );
    $no_votes = empty( $no_votes ) ? 0 : intval( $no_votes );

    wp_send_json_success( array(
        'yes_votes' => $yes_votes,
        'no_votes' => $no_votes,
        'total_votes' => $yes_votes + $no_votes,
    ) );
}
add_action( 'wp_ajax_feryfit_get_post_likes', 'feryfit_ajax_get_blog_likes' );
add_action( 'wp_ajax_nopriv_feryfit_get_post_likes', 'feryfit_ajax_get_blog_likes' );

/**
 * Handle AJAX request to submit blog comment
 */
function feryfit_ajax_submit_blog_comment() {
    if ( ! check_ajax_referer( 'feryfit_blog_action', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
    }

    if ( ! isset( $_POST['post_id'], $_POST['comment'] ) ) {
        wp_send_json_error( array( 'message' => '缺少参数' ) );
    }

    $post_id = intval( wp_unslash( $_POST['post_id'] ) );
    if ( 'blog' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
        wp_send_json_error( array( 'message' => '无效的文章' ), 400 );
    }

    $ip = feryfit_get_client_ip();
    if ( ! feryfit_rate_limit_check( 'blog_comment:' . $ip, 3, 5 * MINUTE_IN_SECONDS ) ) {
        wp_send_json_error( array( 'message' => '请求过于频繁' ), 429 );
    }

    $comment_content = substr( sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ), 0, 2000 );
    if ( '' === trim( $comment_content ) ) {
        wp_send_json_error( array( 'message' => '评论不能为空' ), 400 );
    }

    $comment_data = array(
        'comment_post_ID' => $post_id,
        'comment_author' => 'Anonymous',
        'comment_author_email' => 'anonymous@example.com',
        'comment_content' => $comment_content,
        'comment_type' => '',
        'comment_status' => 'hold',
    );

    $comment_id = wp_insert_comment( $comment_data );

    if ( $comment_id ) {
        wp_send_json_success( array( 'message' => 'Comment submitted successfully' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to submit comment' ) );
    }
}
add_action( 'wp_ajax_feryfit_submit_blog_comment', 'feryfit_ajax_submit_blog_comment' );
add_action( 'wp_ajax_nopriv_feryfit_submit_blog_comment', 'feryfit_ajax_submit_blog_comment' );
