<?php
/**
 * FAQs Manager Class
 * 
 * Handles custom post type registration, admin menu, and AJAX operations for FAQs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAQs_Manager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'init', array( $this, 'register_faq_post_type' ), 10 );
        add_action( 'init', array( $this, 'register_faq_taxonomy' ), 11 );
        add_action( 'init', array( $this, 'register_faq_meta' ), 13 );
        add_action( 'init', array( $this, 'add_faq_rewrite_rules' ), 12 );
        add_filter( 'post_type_link', array( $this, 'modify_faq_permalink' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_faq_delete', array( $this, 'ajax_delete_faq' ) );
        add_action( 'wp_ajax_faq_toggle_status', array( $this, 'ajax_toggle_status' ) );
        add_action( 'wp_ajax_faq_toggle_pin', array( $this, 'ajax_toggle_pin' ) );
        // add_action( 'template_redirect', array( $this, 'block_faq_frontend_access' ) );
        add_action( 'template_redirect', array( $this, 'setup_faq_query' ), 1 );
        add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules_after_switch' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_faq_meta_boxes' ) );
        add_action( 'save_post_faq', array( $this, 'save_faq_meta_data' ), 10, 2 );
        add_filter( 'manage_faq_posts_columns', array( $this, 'add_faq_table_columns' ) );
        add_action( 'manage_faq_posts_custom_column', array( $this, 'render_faq_table_column_content' ), 10, 2 );
        add_action( 'pre_get_posts', array( $this, 'custom_faq_order' ) );
        add_filter( 'template_include', array( $this, 'faq_template_include' ), 99 );
        add_filter( 'pll_get_post_types', array( $this, 'add_faq_to_polylang' ), 10, 2 );
        add_filter( 'pll_get_taxonomies', array( $this, 'add_faq_taxonomy_to_polylang' ), 10, 2 );

    }
    
    /**
     * Register FAQ Custom Post Type
     */
    public function register_faq_post_type() {
        $labels = array(
            'name'               => __( 'FAQ', 'feryfit' ),
            'singular_name'      => __( '常见问题', 'feryfit' ),
            'menu_name'          => __( '常见问题', 'feryfit' ),
            'add_new'            => __( '新增', 'feryfit' ),
            'add_new_item'       => __( '新增常见问题', 'feryfit' ),
            'edit_item'          => __( '编辑常见问题', 'feryfit' ),
            'new_item'           => __( '新常见问题', 'feryfit' ),
            'view_item'          => __( '查看常见问题', 'feryfit' ),
            'search_items'       => __( '搜索常见问题', 'feryfit' ),
            'not_found'          => __( '未找到常见问题', 'feryfit' ),
            'not_found_in_trash' => __( '回收站中未找到常见问题', 'feryfit' ),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 6,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'faq' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'editor', 'custom-fields', 'comments' ),
            'show_in_rest'       => true,
            'rest_base'          => 'faqs',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'taxonomies'         => array( 'faq_category' ),
        );
        
        register_post_type( 'faq', $args );
    }
    
    /**
     * Register FAQ Category Taxonomy
     */
    public function register_faq_taxonomy() {
        $labels = array(
            'name'              => __( '常见问题分类', 'feryfit' ),
            'singular_name'     => __( '常见问题分类', 'feryfit' ),
            'search_items'      => __( '搜索分类', 'feryfit' ),
            'all_items'         => __( '所有分类', 'feryfit' ),
            'parent_item'       => __( '父分类', 'feryfit' ),
            'parent_item_colon' => __( '父分类:', 'feryfit' ),
            'edit_item'         => __( '编辑分类', 'feryfit' ),
            'update_item'       => __( '更新分类', 'feryfit' ),
            'add_new_item'      => __( '新增分类', 'feryfit' ),
            'new_item_name'     => __( '新分类名称', 'feryfit' ),
            'menu_name'         => __( '分类', 'feryfit' ),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'faq-category' ),
            'show_in_rest'      => true,
        );
        
        register_taxonomy( 'faq_category', 'faq', $args );
    }
    
    /**
     * Register FAQ Meta Fields for REST API
     */
    public function register_faq_meta() {
        register_meta( 'post', '_faq_is_pinned', array(
            'object_subtype'    => 'faq',
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
    }
    
    /**
     * Enqueue Admin Assets
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load for FAQ post type pages
        if ( 'edit.php' !== $hook ) {
            return;
        }
        
        global $post_type;
        if ( 'faq' !== $post_type ) {
            return;
        }
        
        wp_enqueue_script( 'faqs-admin-js', get_template_directory_uri() . '/includes/js/faqs-admin.js', array( 'jquery' ), '1.0.0', true );
        
        wp_localize_script( 'faqs-admin-js', 'faqsAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'faqs_admin_nonce' ),
            'strings' => array(
                'confirmDelete' => __( '确定要删除这个常见问题吗？', 'feryfit' ),
                'deleteSuccess' => __( '常见问题删除成功！', 'feryfit' ),
                'deleteError'   => __( '删除常见问题失败。', 'feryfit' ),
            )
        ) );
    }
    
    /**
     * Custom FAQ order for admin list
     */
    public function custom_faq_order( $query ) {
        global $pagenow;
        
        // Only apply to admin FAQ list
        if ( is_admin() && 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'faq' === $_GET['post_type'] ) {
            // Only modify if no specific orderby is set
            if ( ! isset( $_GET['orderby'] ) ) {
                $query->set( 'meta_key', '_faq_is_pinned' );
                $query->set( 'orderby', array(
                    'meta_value_num' => 'DESC',
                    'post_modified'  => 'DESC',
                ) );
            }
        }
    }
    
    /**
     * Add FAQ post type to Polylang translatable types
     */
    public function add_faq_to_polylang( $post_types, $is_settings ) {
        $post_types['faq'] = 'faq';
        return $post_types;
    }

    /**
     * Add FAQ category taxonomy to Polylang translatable types
     */
    public function add_faq_taxonomy_to_polylang( $taxonomies, $is_settings ) {
        $taxonomies['faq_category'] = 'faq_category';
        return $taxonomies;
    }

    /**
     * Add custom rewrite rules for FAQ
     */
    public function add_faq_rewrite_rules() {
        add_rewrite_rule(
            '^archives/faq/(\d+)/?$',
            'index.php?post_type=faq&p=$matches[1]',
            'top'
        );
    }
    
    /**
     * Modify FAQ permalink to use ID format
     */
    public function modify_faq_permalink( $post_link, $post ) {
        if ( 'faq' === $post->post_type ) {
            return home_url( '/archives/faq/' . $post->ID . '/' );
        }
        return $post_link;
    }
    
    /**
     * Setup FAQ query for frontend
     */
    public function setup_faq_query() {
        if ( is_singular( 'faq' ) && ! is_admin() ) {
            global $wp_query;
            if ( isset( $wp_query->query_vars['p'] ) && isset( $wp_query->query_vars['post_type'] ) && 'faq' === $wp_query->query_vars['post_type'] ) {
                $faq_id = intval( $wp_query->query_vars['p'] );
                if ( $faq_id > 0 ) {
                    $wp_query->set( 'p', $faq_id );
                    $wp_query->set( 'post_type', 'faq' );
                    $wp_query->is_singular = true;
                    $wp_query->is_single = true;
                    $wp_query->is_page = false;
                    $wp_query->is_archive = false;
                }
            }
        }
    }
    
    /**
     * Include custom template for FAQ single posts
     */
    public function faq_template_include( $template ) {
        if ( is_singular( 'faq' ) && ! is_admin() ) {
            $custom_template = get_template_directory() . '/single-faq.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }
    
    /**
     * Block frontend access to FAQ single posts (deprecated)
     */
    public function block_faq_frontend_access() {
        if ( is_singular( 'faq' ) && ! is_admin() ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            nocache_headers();
            include get_query_template( '404' );
            exit;
        }
    }
    
    /**
     * Flush rewrite rules after theme switch
     */
    public function flush_rewrite_rules_after_switch() {
        $this->register_faq_post_type();
        $this->add_faq_rewrite_rules();
        flush_rewrite_rules();
    }
    
    /**
     * Add FAQ Meta Boxes
     */
    public function add_faq_meta_boxes() {
        add_meta_box(
            'faq_settings',
            __( '常见问题设置', 'feryfit' ),
            array( $this, 'render_faq_meta_box' ),
            'faq',
            'side',
            'default'
        );
    }
    
    /**
     * Render FAQ Meta Box
     */
    public function render_faq_meta_box( $post ) {
        wp_nonce_field( 'faq_meta_box', 'faq_meta_box_nonce' );
        
        $is_pinned = get_post_meta( $post->ID, '_faq_is_pinned', true );
        ?>
        <p>
            <label>
                <input type="checkbox" name="faq_is_pinned" value="1" <?php checked( $is_pinned, '1' ); ?>>
                <strong><?php _e( '将此常见问题置顶', 'feryfit' ); ?></strong>
            </label>
        </p>
        <p class="description">
            <?php _e( '置顶的常见问题将显示在列表顶部。', 'feryfit' ); ?>
        </p>
        <?php
    }
    
    /**
     * Save FAQ Meta Data
     */
    public function save_faq_meta_data( $post_id, $post ) {
        if ( ! isset( $_POST['faq_meta_box_nonce'] ) ) {
            return;
        }
        
        if ( ! wp_verify_nonce( $_POST['faq_meta_box_nonce'], 'faq_meta_box' ) ) {
            return;
        }
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        $is_pinned = isset( $_POST['faq_is_pinned'] ) ? '1' : '0';
        update_post_meta( $post_id, '_faq_is_pinned', $is_pinned );
    }
    
    /**
     * AJAX: Toggle FAQ pin status
     */
    public function ajax_toggle_pin() {
        check_ajax_referer( 'faqs_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '权限不足', 'feryfit' ) ) );
        }
        
        $faq_id = isset( $_POST['faq_id'] ) ? intval( $_POST['faq_id'] ) : 0;
        
        if ( $faq_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( '无效的常见问题ID', 'feryfit' ) ) );
        }
        
        $current_pinned = get_post_meta( $faq_id, '_faq_is_pinned', true );
        $new_pinned = $current_pinned == '1' ? '0' : '1';
        
        update_post_meta( $faq_id, '_faq_is_pinned', $new_pinned );
        
        wp_send_json_success( array(
            'faq_id' => $faq_id,
            'pinned' => $new_pinned,
            'message' => $new_pinned == '1' ? __( '置顶成功', 'feryfit' ) : __( '取消置顶成功', 'feryfit' ),
        ) );
    }
    
    /**
     * Add custom columns to FAQ list table
     */
    public function add_faq_table_columns( $columns ) {
        $columns['faq_pinned']   = __( '置顶', 'feryfit' );
        $columns['faq_votes']    = __( '点赞次数', 'feryfit' );
        $columns['faq_modified'] = __( '更新时间', 'feryfit' );
        return $columns;
    }
    
    /**
     * Render custom column content
     */
    public function render_faq_table_column_content( $column, $post_id ) {
        if ( 'faq_pinned' === $column ) {
            $is_pinned = get_post_meta( $post_id, '_faq_is_pinned', true );
            $icon_class = $is_pinned ? 'dashicons-star-filled' : 'dashicons-star-empty';
            $color = $is_pinned ? '#d97706' : '#949799';
            $title = $is_pinned ? __( '点击取消置顶', 'feryfit' ) : __( '点击置顶', 'feryfit' );
            ?>
            <button type="button" 
                    class="button-link faq-table-toggle-pin" 
                    data-faq-id="<?php echo esc_attr( $post_id ); ?>"
                    data-pinned="<?php echo esc_attr( $is_pinned ); ?>"
                    title="<?php echo esc_attr( $title ); ?>">
                <span class="dashicons <?php echo esc_attr( $icon_class ); ?>" style="color: <?php echo esc_attr( $color ); ?>;"></span>
            </button>
            <?php
        }
        if ( 'faq_votes' === $column ) {
            $yes_votes = get_post_meta( $post_id, '_faq_yes_votes', true );
            $yes_votes = empty( $yes_votes ) ? 0 : intval( $yes_votes );
            
            echo '<span style="color: #10b981; font-weight: 600; font-size: 14px;">' . $yes_votes . '</span>';
        }
        if ( 'faq_modified' === $column ) {
            $post = get_post( $post_id );
            $modified = $post ? $post->post_modified : '';
            if ( $modified ) {
                echo esc_html( mysql2date( __( 'Y-m-d H:i', 'feryfit' ), $modified ) );
            }
        }
    }
    
    /**
     * AJAX: Delete FAQ
     */
    public function ajax_delete_faq() {
        check_ajax_referer( 'faqs_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'feryfit' ) ) );
        }
        
        $faq_id = isset( $_POST['faq_id'] ) ? intval( $_POST['faq_id'] ) : 0;
        
        if ( $faq_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid FAQ ID', 'feryfit' ) ) );
        }
        
        $result = wp_delete_post( $faq_id, true );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'FAQ deleted successfully', 'feryfit' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete FAQ', 'feryfit' ) ) );
        }
    }
    
    /**
     * AJAX: Toggle FAQ Status
     */
    public function ajax_toggle_status() {
        check_ajax_referer( 'faqs_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'feryfit' ) ) );
        }
        
        $faq_id = isset( $_POST['faq_id'] ) ? intval( $_POST['faq_id'] ) : 0;
        
        if ( $faq_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid FAQ ID', 'feryfit' ) ) );
        }
        
        $faq = get_post( $faq_id );
        if ( ! $faq ) {
            wp_send_json_error( array( 'message' => __( 'FAQ not found', 'feryfit' ) ) );
        }
        
        $new_status = $faq->post_status === 'publish' ? 'draft' : 'publish';
        $result = wp_update_post( array(
            'ID'          => $faq_id,
            'post_status' => $new_status,
        ) );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( array(
                'new_status' => $new_status,
                'message'    => sprintf(
                    __( 'FAQ %s', 'feryfit' ),
                    $new_status === 'publish' ? __( 'activated', 'feryfit' ) : __( 'deactivated', 'feryfit' )
                ),
            ) );
        }
    }
}

FAQs_Manager::get_instance();
