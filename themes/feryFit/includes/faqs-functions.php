<?php
/**
 * FAQs Helper Functions
 * 
 * Functions to retrieve and display FAQs on the frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle FAQ helpfulness vote via AJAX
 */
function feryfit_handle_faq_vote() {
    if ( ! check_ajax_referer( 'feryfit_faq_action', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
    }

    if ( ! isset( $_POST['faq_id'], $_POST['vote'] ) ) {
        wp_send_json_error( array( 'message' => '缺少参数' ) );
    }

    $faq_id = intval( wp_unslash( $_POST['faq_id'] ) );
    $vote = sanitize_text_field( wp_unslash( $_POST['vote'] ) );

    if ( 'faq' !== get_post_type( $faq_id ) || 'publish' !== get_post_status( $faq_id ) ) {
        wp_send_json_error( array( 'message' => '无效的FAQ' ), 400 );
    }

    if ( ! in_array( $vote, array( 'yes', 'no' ) ) ) {
        wp_send_json_error( array( 'message' => '无效的投票选项' ) );
    }

    $ip = feryfit_get_client_ip();
    if ( ! feryfit_rate_limit_check( 'faq_vote:' . $ip, 30, MINUTE_IN_SECONDS ) ) {
        wp_send_json_error( array( 'message' => '请求过于频繁' ), 429 );
    }

    // IP-based daily rate limit: one vote per FAQ per IP per day
    $today = gmdate( 'Y-m-d' );
    $transient_key = 'faq_vote_' . $faq_id . '_' . md5( $ip ) . '_' . $today;
    if ( get_transient( $transient_key ) ) {
        // Already voted today, return current count without incrementing
        $yes_votes = get_post_meta( $faq_id, '_faq_yes_votes', true );
        $no_votes = get_post_meta( $faq_id, '_faq_no_votes', true );
        wp_send_json_error( array(
            'message' => '今日已点赞',
            'yes_votes' => empty( $yes_votes ) ? 0 : intval( $yes_votes ),
            'no_votes' => empty( $no_votes ) ? 0 : intval( $no_votes ),
        ) );
    }

    // Get current votes
    $yes_votes = get_post_meta( $faq_id, '_faq_yes_votes', true );
    $no_votes = get_post_meta( $faq_id, '_faq_no_votes', true );

    // Initialize if not set
    if ( empty( $yes_votes ) ) {
        $yes_votes = 0;
    }
    if ( empty( $no_votes ) ) {
        $no_votes = 0;
    }

    // Update votes
    if ( $vote === 'yes' ) {
        $yes_votes++;
        update_post_meta( $faq_id, '_faq_yes_votes', $yes_votes );
    } else {
        $no_votes++;
        update_post_meta( $faq_id, '_faq_no_votes', $no_votes );
    }

    // Set transient to prevent re-vote (expires at end of day GMT)
    $seconds_until_midnight = strtotime( 'tomorrow GMT' ) - time();
    set_transient( $transient_key, 1, $seconds_until_midnight );

    wp_send_json_success( array(
        'yes_votes' => $yes_votes,
        'no_votes' => $no_votes,
        'total_votes' => $yes_votes + $no_votes,
        'percentage' => ( $yes_votes + $no_votes ) > 0 ? round( ( $yes_votes / ( $yes_votes + $no_votes ) ) * 100 ) : 0,
    ) );
}
add_action( 'wp_ajax_feryfit_faq_vote', 'feryfit_handle_faq_vote' );
add_action( 'wp_ajax_nopriv_feryfit_faq_vote', 'feryfit_handle_faq_vote' );

/**
 * Get FAQ helpfulness votes
 * 
 * @param int $faq_id FAQ post ID.
 * @return array Array with yes_votes, no_votes, total_votes, and percentage.
 */
function feryfit_get_faq_votes( $faq_id ) {
    $yes_votes = get_post_meta( $faq_id, '_faq_yes_votes', true );
    $no_votes = get_post_meta( $faq_id, '_faq_no_votes', true );
    
    $yes_votes = empty( $yes_votes ) ? 0 : intval( $yes_votes );
    $no_votes = empty( $no_votes ) ? 0 : intval( $no_votes );
    $total_votes = $yes_votes + $no_votes;
    $percentage = $total_votes > 0 ? round( ( $yes_votes / $total_votes ) * 100 ) : 0;
    
    return array(
        'yes_votes' => $yes_votes,
        'no_votes' => $no_votes,
        'total_votes' => $total_votes,
        'percentage' => $percentage,
    );
}

/**
 * Handle AJAX request to get FAQ votes
 */
function feryfit_ajax_get_faq_votes() {
    if ( ! isset( $_POST['faq_id'] ) ) {
        wp_send_json_error( array( 'message' => '缺少参数' ) );
    }
    
    $faq_id = intval( wp_unslash( $_POST['faq_id'] ) );
    if ( 'faq' !== get_post_type( $faq_id ) || 'publish' !== get_post_status( $faq_id ) ) {
        wp_send_json_error( array( 'message' => '无效的FAQ' ), 400 );
    }

    $votes = feryfit_get_faq_votes( $faq_id );
    
    wp_send_json_success( $votes );
}
add_action( 'wp_ajax_feryfit_get_faq_votes', 'feryfit_ajax_get_faq_votes' );
add_action( 'wp_ajax_nopriv_feryfit_get_faq_votes', 'feryfit_ajax_get_faq_votes' );

/**
 * Handle AJAX request to submit FAQ comment
 */
function feryfit_ajax_submit_faq_comment() {
    if ( ! check_ajax_referer( 'feryfit_faq_action', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
    }

    if ( ! isset( $_POST['faq_id'], $_POST['comment'] ) ) {
        wp_send_json_error( array( 'message' => '缺少参数' ) );
    }
    
    $faq_id = intval( wp_unslash( $_POST['faq_id'] ) );
    if ( 'faq' !== get_post_type( $faq_id ) || 'publish' !== get_post_status( $faq_id ) ) {
        wp_send_json_error( array( 'message' => '无效的FAQ' ), 400 );
    }

    $ip = feryfit_get_client_ip();
    if ( ! feryfit_rate_limit_check( 'faq_comment:' . $ip, 3, 5 * MINUTE_IN_SECONDS ) ) {
        wp_send_json_error( array( 'message' => '请求过于频繁' ), 429 );
    }

    $comment_content = substr( sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ), 0, 2000 );
    $author = isset( $_POST['author'] ) ? substr( sanitize_text_field( wp_unslash( $_POST['author'] ) ), 0, 80 ) : '';
    $email = isset( $_POST['email'] ) ? substr( sanitize_email( wp_unslash( $_POST['email'] ) ), 0, 100 ) : '';

    if ( '' === trim( $comment_content ) ) {
        wp_send_json_error( array( 'message' => '评论不能为空' ), 400 );
    }
    
    // Validate email if provided
    if ( ! empty( $email ) && ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => '无效的邮箱地址' ) );
    }
    
    // Use default values if empty
    if ( empty( $author ) ) {
        $author = 'Anonymous';
    }
    if ( empty( $email ) ) {
        $email = 'anonymous@example.com';
    }
    
    // Create comment
    $comment_data = array(
        'comment_post_ID' => $faq_id,
        'comment_author' => $author,
        'comment_author_email' => $email,
        'comment_content' => $comment_content,
        'comment_type' => '',
        'comment_status' => 'hold', // 评论需要审核
    );
    
    $comment_id = wp_insert_comment( $comment_data );
    
    if ( $comment_id ) {
        wp_send_json_success( array( 'message' => 'Comment submitted successfully' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to submit comment' ) );
    }
}
add_action( 'wp_ajax_feryfit_submit_faq_comment', 'feryfit_ajax_submit_faq_comment' );
add_action( 'wp_ajax_nopriv_feryfit_submit_faq_comment', 'feryfit_ajax_submit_faq_comment' );

/**
 * Get all published FAQs ordered by pinned status and menu_order
 * 
 * @param array $args Optional. Additional arguments for WP_Query.
 * @return array Array of FAQ posts.
 */
function feryfit_get_faqs( $args = array() ) {
    $defaults = array(
        'post_type'      => 'faq',
        'posts_per_page' => -1,
        'orderby'        => array(
            'meta_value_num' => 'DESC',
            'menu_order'     => 'ASC',
        ),
        'meta_key'       => '_faq_is_pinned',
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
 * Get FAQs by category
 * 
 * @param string|int $category Category slug or ID.
 * @param array $args Optional. Additional arguments for WP_Query.
 * @return array Array of FAQ posts.
 */
function feryfit_get_faqs_by_category( $category, $args = array() ) {
    $defaults = array(
        'post_type'      => 'faq',
        'posts_per_page' => -1,
        'orderby'        => array(
            'meta_value_num' => 'DESC',
            'menu_order'     => 'ASC',
        ),
        'meta_key'       => '_faq_is_pinned',
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
                'taxonomy' => 'faq_category',
                'field'    => is_numeric( $category ) ? 'term_id' : 'slug',
                'terms'    => $category,
            ),
        );
    }
    
    return get_posts( $args );
}

/**
 * Get all FAQ categories
 * 
 * @return array Array of category terms.
 */
function feryfit_get_faq_categories() {
    return get_terms( array(
        'taxonomy'   => 'faq_category',
        'hide_empty' => false,
    ) );
}

/**
 * Get a single FAQ by ID
 * 
 * @param int $faq_id FAQ post ID.
 * @return WP_Post|null FAQ post object or null if not found.
 */
function feryfit_get_faq( $faq_id ) {
    return get_post( $faq_id );
}

/**
 * Get FAQ question
 * 
 * @param int $faq_id FAQ post ID.
 * @return string FAQ question (post title).
 */
function feryfit_get_faq_question( $faq_id ) {
    return get_the_title( $faq_id );
}

/**
 * Get FAQ answer
 * 
 * @param int  $faq_id FAQ post ID.
 * @param bool $apply_filters Whether to apply content filters. Default true.
 * @return string FAQ answer (post content).
 */
function feryfit_get_faq_answer( $faq_id, $apply_filters = true ) {
    $content = get_the_content( '', false, $faq_id );
    
    if ( $apply_filters ) {
        $content = apply_filters( 'the_content', $content );
    }
    
    return $content;
}

/**
 * Display FAQs as an accordion
 * 
 * @param array $args Optional. Arguments to customize the output.
 */
function feryfit_display_faqs_accordion( $args = array() ) {
    $defaults = array(
        'limit'               => -1,
        'show_search'         => true,
        'show_categories'     => true,
        'search_placeholder'  => __( '搜索常见问题...', 'feryfit' ),
        'category_placeholder' => __( '所有分类', 'feryfit' ),
        'container_class'     => 'faqs-accordion',
        'item_class'          => 'faq-accordion-item',
        'question_class'      => 'faq-accordion-question',
        'answer_class'        => 'faq-accordion-answer',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    $faqs = feryfit_get_faqs( array(
        'posts_per_page' => $args['limit'],
    ) );
    
    $categories = $args['show_categories'] ? feryfit_get_faq_categories() : array();
    
    if ( empty( $faqs ) ) {
        echo '<p>' . __( '暂无常见问题。', 'feryfit' ) . '</p>';
        return;
    }
    ?>
    <div class="<?php echo esc_attr( $args['container_class'] ); ?>">
        <?php if ( $args['show_search'] || ( $args['show_categories'] && ! empty( $categories ) ) ) : ?>
            <div class="faqs-filters">
                <?php if ( $args['show_search'] ) : ?>
                    <div class="faqs-search">
                        <input type="text" class="faqs-search-input" placeholder="<?php echo esc_attr( $args['search_placeholder'] ); ?>">
                    </div>
                <?php endif; ?>
                
                <?php if ( $args['show_categories'] && ! empty( $categories ) ) : ?>
                    <div class="faqs-category-filter">
                        <select class="faqs-category-select">
                            <option value=""><?php echo esc_html( $args['category_placeholder'] ); ?></option>
                            <?php foreach ( $categories as $category ) : ?>
                                <option value="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="faqs-accordion-list">
            <?php foreach ( $faqs as $faq ) : 
                $is_pinned = get_post_meta( $faq->ID, '_faq_is_pinned', true );
                $faq_categories = get_the_terms( $faq->ID, 'faq_category' );
                $category_slugs = is_array( $faq_categories ) && ! empty( $faq_categories ) ? wp_list_pluck( $faq_categories, 'slug' ) : array();
            ?>
                <div class="<?php echo esc_attr( $args['item_class'] ); ?> <?php echo $is_pinned ? 'faq-item-pinned' : ''; ?>" 
                     data-faq-id="<?php echo esc_attr( $faq->ID ); ?>"
                     data-categories="<?php echo esc_attr( implode( ',', $category_slugs ) ); ?>">
                    <div class="<?php echo esc_attr( $args['question_class'] ); ?>">
                        <h3>
                            <button type="button" class="faq-toggle">
                                <?php if ( $is_pinned ) : ?>
                                    <span class="faq-pinned-icon" title="<?php _e( 'Pinned', 'feryfit' ); ?>">📌</span>
                                <?php endif; ?>
                                <?php echo esc_html( get_the_title( $faq->ID ) ); ?>
                                <span class="faq-toggle-icon">+</span>
                            </button>
                        </h3>
                    </div>
                    <div class="<?php echo esc_attr( $args['answer_class'] ); ?>">
                        <div class="faq-answer-content">
                            <?php echo feryfit_get_faq_answer( $faq->ID ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
        .faqs-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .faqs-search {
            flex: 1;
            min-width: 200px;
        }
        .faqs-search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .faqs-category-filter {
            min-width: 200px;
        }
        .faqs-category-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: #fff;
            cursor: pointer;
        }
        .faq-accordion-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .faq-accordion-item.faq-item-pinned {
            border-color: #fbbf24;
            border-width: 2px;
        }
        .faq-accordion-question {
            margin: 0;
        }
        .faq-toggle {
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
        .faq-accordion-item.faq-item-pinned .faq-toggle {
            background: #fffbeb;
        }
        .faq-toggle:hover {
            background: #f0f0f0;
        }
        .faq-accordion-item.faq-item-pinned .faq-toggle:hover {
            background: #fef3c7;
        }
        .faq-pinned-icon {
            font-size: 14px;
        }
        .faq-toggle-icon {
            font-size: 20px;
            font-weight: 300;
            transition: transform 0.3s ease;
        }
        .faq-accordion-item.active .faq-toggle-icon {
            transform: rotate(45deg);
        }
        .faq-accordion-answer {
            display: none;
            padding: 20px;
            background: #fff;
            border-top: 1px solid #ddd;
        }
        .faq-accordion-item.active .faq-accordion-answer {
            display: block;
        }
        .faq-accordion-item.hidden {
            display: none;
        }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ accordion toggle
        document.querySelectorAll('.faq-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                var item = this.closest('.faq-accordion-item');
                item.classList.toggle('active');
            });
        });
        
        // FAQ search
        var searchInput = document.querySelector('.faqs-search-input');
        var categorySelect = document.querySelector('.faqs-category-select');
        
        function filterFAQs() {
            var searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            var selectedCategory = categorySelect ? categorySelect.value : '';
            
            document.querySelectorAll('.faq-accordion-item').forEach(function(item) {
                var question = item.querySelector('.faq-toggle').textContent.toLowerCase();
                var answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                var itemCategories = item.getAttribute('data-categories') || '';
                
                var matchesSearch = searchTerm === '' || question.indexOf(searchTerm) > -1 || answer.indexOf(searchTerm) > -1;
                var matchesCategory = selectedCategory === '' || itemCategories.indexOf(selectedCategory) > -1;
                
                if (matchesSearch && matchesCategory) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', filterFAQs);
        }
        
        if (categorySelect) {
            categorySelect.addEventListener('change', filterFAQs);
        }
    });
    </script>
    <?php
}

/**
 * Hide author column in comments management page
 */
function feryfit_hide_comment_author_column( $columns ) {
    unset( $columns['author'] );
    return $columns;
}
add_filter( 'manage_edit-comments_columns', 'feryfit_hide_comment_author_column' );

/**
 * Remove author column content in comments table
 */
function feryfit_hide_comment_author_content( $column, $comment_ID ) {
    if ( $column === 'author' ) {
        echo '';
    }
}
add_action( 'manage_comments_custom_column', 'feryfit_hide_comment_author_content', 10, 2 );
