<?php
/**
 * Blog Manager Class
 * 
 * Handles custom post type registration, admin menu, and AJAX operations for Blog
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Blog_Manager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'init', array( $this, 'register_blog_post_type' ), 10 );
        add_action( 'init', array( $this, 'register_blog_taxonomy' ), 11 );
        add_action( 'init', array( $this, 'add_blog_rewrite_rules' ), 12 );
        add_filter( 'query_vars', array( $this, 'add_blog_query_vars' ) );
        add_filter( 'post_type_link', array( $this, 'modify_blog_permalink' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_blog_delete', array( $this, 'ajax_delete_blog' ) );
        add_action( 'wp_ajax_blog_toggle_status', array( $this, 'ajax_toggle_status' ) );
        add_action( 'wp_ajax_blog_toggle_pin', array( $this, 'ajax_toggle_pin' ) );
        // add_action( 'template_redirect', array( $this, 'block_blog_frontend_access' ) );
        add_action( 'template_redirect', array( $this, 'setup_blog_query' ), 1 );
        add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules_after_switch' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_blog_meta_boxes' ) );
        add_action( 'save_post_blog', array( $this, 'save_blog_meta_data' ), 10, 2 );
        add_filter( 'manage_blog_posts_columns', array( $this, 'add_blog_table_columns' ) );
        add_action( 'manage_blog_posts_custom_column', array( $this, 'render_blog_table_column_content' ), 10, 2 );
        add_action( 'pre_get_posts', array( $this, 'custom_blog_order' ) );
        add_action( 'pre_get_posts', array( $this, 'handle_blog_archive_rewrite' ) );
        add_filter( 'template_include', array( $this, 'blog_template_include' ), 99 );
        add_filter( 'pll_get_post_types', array( $this, 'add_blog_to_polylang' ), 10, 2 );
        add_filter( 'pll_get_taxonomies', array( $this, 'add_blog_taxonomy_to_polylang' ), 10, 2 );

    }
    
    /**
     * Register Blog Custom Post Type
     */
    public function register_blog_post_type() {
        $labels = array(
            'name'               => __( 'blog', 'feryfit' ),
            'singular_name'      => __( '博客', 'feryfit' ),
            'menu_name'          => __( '博客', 'feryfit' ),
            'add_new'            => __( '新增', 'feryfit' ),
            'add_new_item'       => __( '新增博客', 'feryfit' ),
            'edit_item'          => __( '编辑博客', 'feryfit' ),
            'new_item'           => __( '新博客', 'feryfit' ),
            'view_item'          => __( '查看博客', 'feryfit' ),
            'search_items'       => __( '搜索博客', 'feryfit' ),
            'not_found'          => __( '未找到博客', 'feryfit' ),
            'not_found_in_trash' => __( '回收站中未找到博客', 'feryfit' ),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-welcome-write-blog',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'blog' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'comments' ),
            'show_in_rest'       => true,
            'rest_base'          => 'blogs',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'taxonomies'         => array( 'blog_category' ),
        );
        
        register_post_type( 'blog', $args );
    }
    
    /**
     * Register Blog Category Taxonomy
     */
    public function register_blog_taxonomy() {
        $labels = array(
            'name'              => __( '博客分类', 'feryfit' ),
            'singular_name'     => __( '博客分类', 'feryfit' ),
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
            'rewrite'           => array( 'slug' => 'blog-category' ),
            'show_in_rest'      => true,
        );
        
        register_taxonomy( 'blog_category', 'blog', $args );
    }
    
    /**
     * Enqueue Admin Assets
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load for Blog post type pages
        if ( 'edit.php' !== $hook ) {
            return;
        }
        
        global $post_type;
        if ( 'blog' !== $post_type ) {
            return;
        }
        
        wp_enqueue_script( 'blog-admin-js', get_template_directory_uri() . '/includes/js/blog-admin.js', array( 'jquery' ), '1.0.0', true );
        
        wp_localize_script( 'blog-admin-js', 'blogAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'blog_admin_nonce' ),
            'strings' => array(
                'confirmDelete' => __( '确定要删除这个博客吗？', 'feryfit' ),
                'deleteSuccess' => __( '博客删除成功！', 'feryfit' ),
                'deleteError'   => __( '删除博客失败。', 'feryfit' ),
            )
        ) );
    }
    
    /**
     * Custom Blog order for admin list
     */
    public function custom_blog_order( $query ) {
        global $pagenow;
        
        // Only apply to admin Blog list
        if ( is_admin() && 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'blog' === $_GET['post_type'] ) {
            // Only modify if no specific orderby is set
            if ( ! isset( $_GET['orderby'] ) ) {
                $query->set( 'meta_key', '_blog_is_pinned' );
                $query->set( 'orderby', array(
                    'meta_value_num' => 'DESC',
                    'post_modified'  => 'DESC',
                ) );
            }
        }
    }
    
    /**
     * Add Blog post type to Polylang translatable types
     */
    public function add_blog_to_polylang( $post_types, $is_settings ) {
        $post_types['blog'] = 'blog';
        return $post_types;
    }

    /**
     * Add Blog category taxonomy to Polylang translatable types
     */
    public function add_blog_taxonomy_to_polylang( $taxonomies, $is_settings ) {
        $taxonomies['blog_category'] = 'blog_category';
        return $taxonomies;
    }

    /**
     * Block frontend access to Blog single posts
     */
    public function block_blog_frontend_access() {
        if ( is_singular( 'blog' ) && ! is_admin() ) {
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
        $this->register_blog_post_type();
        flush_rewrite_rules();
    }
    
    /**
     * Add Blog Meta Boxes
     */
    public function add_blog_meta_boxes() {
        add_meta_box(
            'blog_settings',
            __( '博客设置', 'feryfit' ),
            array( $this, 'render_blog_meta_box' ),
            'blog',
            'side',
            'default'
        );
    }
    
    /**
     * Render Blog Meta Box
     */
    public function render_blog_meta_box( $post ) {
        wp_nonce_field( 'blog_meta_box', 'blog_meta_box_nonce' );
        
        $is_pinned = get_post_meta( $post->ID, '_blog_is_pinned', true );
        ?>
        <p>
            <label>
                <input type="checkbox" name="blog_is_pinned" value="1" <?php checked( $is_pinned, '1' ); ?>>
                <strong><?php _e( '将此博客置顶', 'feryfit' ); ?></strong>
            </label>
        </p>
        <p class="description">
            <?php _e( '置顶的博客将显示在列表顶部。', 'feryfit' ); ?>
        </p>
        <?php
    }
    
    /**
     * Save Blog Meta Data
     */
    public function save_blog_meta_data( $post_id, $post ) {
        if ( ! isset( $_POST['blog_meta_box_nonce'] ) ) {
            return;
        }
        
        if ( ! wp_verify_nonce( $_POST['blog_meta_box_nonce'], 'blog_meta_box' ) ) {
            return;
        }
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        $is_pinned = isset( $_POST['blog_is_pinned'] ) ? 1 : 0;
        update_post_meta( $post_id, '_blog_is_pinned', $is_pinned );
    }
    
    /**
     * AJAX: Toggle Blog pin status
     */
    public function ajax_toggle_pin() {
        check_ajax_referer( 'blog_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '权限不足', 'feryfit' ) ) );
        }
        
        $blog_id = isset( $_POST['blog_id'] ) ? intval( $_POST['blog_id'] ) : 0;
        
        if ( $blog_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( '无效的博客ID', 'feryfit' ) ) );
        }
        
        $current_pinned = get_post_meta( $blog_id, '_blog_is_pinned', true );
        $new_pinned = ( $current_pinned == 1 || $current_pinned == '1' ) ? 0 : 1;
        
        update_post_meta( $blog_id, '_blog_is_pinned', $new_pinned );
        
        wp_send_json_success( array(
            'blog_id' => $blog_id,
            'pinned' => $new_pinned,
            'message' => $new_pinned == '1' ? __( '置顶成功', 'feryfit' ) : __( '取消置顶成功', 'feryfit' ),
        ) );
    }
    
    /**
     * Add custom columns to Blog list table
     */
    public function add_blog_table_columns( $columns ) {
        $columns['blog_pinned']   = __( '置顶', 'feryfit' );
        $columns['blog_votes']    = __( '点赞次数', 'feryfit' );
        $columns['blog_modified'] = __( '更新时间', 'feryfit' );
        return $columns;
    }
    
    /**
     * Render custom column content
     */
    public function render_blog_table_column_content( $column, $post_id ) {
        if ( 'blog_pinned' === $column ) {
            $is_pinned = get_post_meta( $post_id, '_blog_is_pinned', true );
            $icon_class = $is_pinned ? 'dashicons-star-filled' : 'dashicons-star-empty';
            $color = $is_pinned ? '#d97706' : '#949799';
            $title = $is_pinned ? __( '点击取消置顶', 'feryfit' ) : __( '点击置顶', 'feryfit' );
            ?>
            <button type="button" 
                    class="button-link blog-table-toggle-pin" 
                    data-blog-id="<?php echo esc_attr( $post_id ); ?>"
                    data-pinned="<?php echo esc_attr( $is_pinned ); ?>"
                    title="<?php echo esc_attr( $title ); ?>">
                <span class="dashicons <?php echo esc_attr( $icon_class ); ?>" style="color: <?php echo esc_attr( $color ); ?>;"></span>
            </button>
            <?php
        }
        if ( 'blog_votes' === $column ) {
            $yes_votes = get_post_meta( $post_id, '_blog_yes_votes', true );
            $yes_votes = empty( $yes_votes ) ? 0 : intval( $yes_votes );
            
            echo '<span style="color: #10b981; font-weight: 600; font-size: 14px;">' . $yes_votes . '</span>';
        }
        if ( 'blog_modified' === $column ) {
            $post = get_post( $post_id );
            $modified = $post ? $post->post_modified : '';
            if ( $modified ) {
                echo esc_html( mysql2date( __( 'Y-m-d H:i', 'feryfit' ), $modified ) );
            }
        }
    }
    
    /**
     * AJAX: Delete Blog
     */
    public function ajax_delete_blog() {
        check_ajax_referer( 'blog_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'feryfit' ) ) );
        }
        
        $blog_id = isset( $_POST['blog_id'] ) ? intval( $_POST['blog_id'] ) : 0;
        
        if ( $blog_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid Blog ID', 'feryfit' ) ) );
        }
        
        $result = wp_delete_post( $blog_id, true );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Blog deleted successfully', 'feryfit' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete Blog', 'feryfit' ) ) );
        }
    }
    
    /**
     * AJAX: Toggle Blog Status
     */
    public function ajax_toggle_status() {
        check_ajax_referer( 'blog_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'feryfit' ) ) );
        }
        
        $blog_id = isset( $_POST['blog_id'] ) ? intval( $_POST['blog_id'] ) : 0;
        
        if ( $blog_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid Blog ID', 'feryfit' ) ) );
        }
        
        $blog = get_post( $blog_id );
        if ( ! $blog ) {
            wp_send_json_error( array( 'message' => __( 'Blog not found', 'feryfit' ) ) );
        }
        
        $new_status = $blog->post_status === 'publish' ? 'draft' : 'publish';
        $result = wp_update_post( array(
            'ID'          => $blog_id,
            'post_status' => $new_status,
        ) );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( array(
                'new_status' => $new_status,
                'message'    => sprintf(
                    __( 'Blog %s', 'feryfit' ),
                    $new_status === 'publish' ? __( 'activated', 'feryfit' ) : __( 'deactivated', 'feryfit' )
                ),
            ) );
        }
    }
    
    /**
     * Modify blog post permalink to use ID format
     */
    public function modify_blog_permalink( $post_link, $post ) {
        if ( 'blog' === $post->post_type ) {
            return home_url( '/archives/blog/' . $post->ID . '/' );
        }
        return $post_link;
    }
    
    /**
     * Add custom rewrite rules for /archives/blog/{id} format
     */
    public function add_blog_rewrite_rules() {
        add_rewrite_rule(
            '^archives/blog/(\d+)/?$',
            'index.php?post_type=blog&p=$matches[1]',
            'top'
        );
    }
    
    /**
     * Add custom query vars
     */
    public function add_blog_query_vars( $vars ) {
        $vars[] = 'blog_id';
        return $vars;
    }
    
    /**
     * Handle the custom rewrite rule for blog single posts
     */
    public function handle_blog_archive_rewrite( $query ) {
        if ( ! is_admin() && $query->is_main_query() && isset( $query->query_vars['p'] ) && isset( $query->query_vars['post_type'] ) && 'blog' === $query->query_vars['post_type'] ) {
            $query->set( 'post_type', 'blog' );
            $query->is_singular = true;
            $query->is_single = true;
        }
    }
    
    /**
     * Template include filter for blog single posts
     */
    public function blog_template_include( $template ) {
        global $wp_query;
        
        if ( isset( $wp_query->query_vars['post_type'] ) && 'blog' === $wp_query->query_vars['post_type'] && isset( $wp_query->query_vars['p'] ) ) {
            $blog_template = get_template_directory() . '/single-blog.php';
            if ( file_exists( $blog_template ) ) {
                return $blog_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Set up the blog query correctly before template redirect
     */
    public function setup_blog_query() {
        global $wp, $wp_query;
        
        if ( ! is_admin() && isset( $wp->query_vars['post_type'] ) && 'blog' === $wp->query_vars['post_type'] && isset( $wp->query_vars['p'] ) ) {
            $post_id = intval( $wp->query_vars['p'] );
            $post = get_post( $post_id );
            
            if ( $post && 'blog' === $post->post_type ) {
                $wp_query = new WP_Query( array(
                    'p' => $post_id,
                    'post_type' => 'blog',
                    'posts_per_page' => 1,
                ) );
                $wp_query->is_singular = true;
                $wp_query->is_single = true;
                $wp_query->is_page = false;
                $wp_query->is_archive = false;
            }
        }
    }
}

Blog_Manager::get_instance();
