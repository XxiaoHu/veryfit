<?php
/**
 * Video Manager Class
 *
 * Handles custom post type registration, admin menu, and AJAX operations for Video
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Video_Manager {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_video_post_type' ), 10 );
        add_action( 'init', array( $this, 'add_video_rewrite_rules' ), 12 );
        add_filter( 'query_vars', array( $this, 'add_video_query_vars' ) );
        add_filter( 'post_type_link', array( $this, 'modify_video_permalink' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_video_delete', array( $this, 'ajax_delete_video' ) );
        add_action( 'template_redirect', array( $this, 'setup_video_query' ), 1 );
        add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules_after_switch' ) );
        add_action( 'admin_init', array( $this, 'maybe_flush_rewrite_rules' ) );
        add_action( 'pre_get_posts', array( $this, 'handle_video_archive_rewrite' ) );
        add_filter( 'template_include', array( $this, 'video_template_include' ), 99 );

        // Polylang integration (correctly using 2 arguments)
        add_filter( 'pll_get_post_types', array( $this, 'add_video_to_polylang' ), 10, 2 );
        add_filter( 'pll_get_taxonomies', array( $this, 'add_video_taxonomies_to_polylang' ), 10, 2 );
    }

    /**
     * Register Video Custom Post Type
     */
    public function register_video_post_type() {
        $labels = array(
            'name'               => __( 'Video', 'feryfit' ),
            'singular_name'      => __( '视频', 'feryfit' ),
            'menu_name'          => __( '视频', 'feryfit' ),
            'add_new'            => __( '新增', 'feryfit' ),
            'add_new_item'       => __( '新增视频', 'feryfit' ),
            'edit_item'          => __( '编辑视频', 'feryfit' ),
            'new_item'           => __( '新视频', 'feryfit' ),
            'view_item'          => __( '查看视频', 'feryfit' ),
            'search_items'       => __( '搜索视频', 'feryfit' ),
            'not_found'          => __( '未找到视频', 'feryfit' ),
            'not_found_in_trash' => __( '回收站中未找到视频', 'feryfit' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 8,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'video' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'show_in_rest'       => true,
            'rest_base'          => 'videos',
            'menu_icon'          => 'dashicons-video-alt3',
        );

        register_post_type( 'video', $args );
    }

    /**
     * Add custom rewrite rules for Video
     */
    public function add_video_rewrite_rules() {
        add_rewrite_rule(
            '^archives/video/(\d+)/?$',
            'index.php?post_type=video&p=$matches[1]',
            'top'
        );
    }

    /**
     * Add query vars for Video
     */
    public function add_video_query_vars( $vars ) {
        $vars[] = 'video';
        return $vars;
    }

    /**
     * Handle video archive rewrite
     */
    public function handle_video_archive_rewrite( $query ) {
        if ( ! is_admin() && $query->is_main_query() ) {
            if ( $query->get( 'post_type' ) === 'video' && ! $query->get( 'p' ) ) {
                $query->set( 'error', '' );
                $query->is_archive = true;
                $query->is_post_type_archive = true;
            }
        }
    }

    /**
     * Modify video permalink
     */
    public function modify_video_permalink( $post_link, $post ) {
        if ( $post->post_type === 'video' ) {
            return home_url( '/archives/video/' . $post->ID . '/' );
        }
        return $post_link;
    }

    /**
     * Setup video query on frontend
     */
    public function setup_video_query() {
        if ( is_singular( 'video' ) && ! is_admin() ) {
            global $wp_query;
            if ( isset( $wp_query->query_vars['p'] ) && isset( $wp_query->query_vars['post_type'] ) && 'video' === $wp_query->query_vars['post_type'] ) {
                $video_id = intval( $wp_query->query_vars['p'] );
                if ( $video_id > 0 ) {
                    $wp_query->set( 'p', $video_id );
                    $wp_query->set( 'post_type', 'video' );
                    $wp_query->is_singular = true;
                    $wp_query->is_single = true;
                    $wp_query->is_page = false;
                    $wp_query->is_archive = false;
                }
            }
        }
    }

    /**
     * Include custom template for video single posts
     */
    public function video_template_include( $template ) {
        if ( is_singular( 'video' ) && ! is_admin() ) {
            $custom_template = get_template_directory() . '/single-video.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }

    /**
     * Flush rewrite rules after theme switch
     */
    public function flush_rewrite_rules_after_switch() {
        $this->register_video_post_type();
        $this->add_video_rewrite_rules();
        flush_rewrite_rules();
    }

    /**
     * Auto-flush rewrite rules if version changed
     */
    public function maybe_flush_rewrite_rules() {
        $version = get_option( 'feryfit_video_rewrite_version' );
        if ( $version !== '3' ) {
            $this->register_video_post_type();
            $this->add_video_rewrite_rules();
            flush_rewrite_rules();
            update_option( 'feryfit_video_rewrite_version', '3' );
        }
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        global $post;

        if ( ! $post || $post->post_type !== 'video' ) {
            return;
        }

        wp_enqueue_script(
            'video-admin',
            get_template_directory_uri() . '/includes/js/video-admin.js',
            array( 'jquery' ),
            '1.0.0',
            true
        );

        wp_localize_script(
            'video-admin',
            'videoAdmin',
            array(
                'nonce' => wp_create_nonce( 'video_admin_nonce' ),
            )
        );
    }

    /**
     * AJAX delete video
     */
    public function ajax_delete_video() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'video_admin_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( '权限验证失败', 'feryfit' ) ) );
        }

        $post_id = intval( $_POST['post_id'] );

        if ( ! current_user_can( 'delete_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => __( '没有删除权限', 'feryfit' ) ) );
        }

        wp_delete_post( $post_id, true );
        wp_send_json_success( array( 'message' => __( '删除成功', 'feryfit' ) ) );
    }

    /**
     * Add Video post type to Polylang translatable types
     *
     * @param array $post_types List of post types Polylang can translate.
     * @param bool  $is_settings Whether in settings page context.
     * @return array
     */
    public function add_video_to_polylang( $post_types, $is_settings ) {
        $post_types['video'] = 'video';
        return $post_types;
    }

    /**
     * Add Video taxonomies to Polylang translatable taxonomies (placeholder for future use)
     *
     * @param array $taxonomies List of taxonomies Polylang can translate.
     * @param bool  $is_settings Whether in settings page context.
     * @return array
     */
    public function add_video_taxonomies_to_polylang( $taxonomies, $is_settings ) {
        // If you add custom taxonomies for Video later, add them here, e.g.:
        // $taxonomies['video_category'] = 'video_category';
        return $taxonomies;
    }
}

// Initialize Video Manager
Video_Manager::get_instance();