<?php
/**
 * Video Content Manager Class
 *
 * Handles custom post type registration, admin menu, and AJAX operations for Video Content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Video_Content_Manager {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_video_content_post_type' ), 10 );
        add_action( 'init', array( $this, 'add_video_content_rewrite_rules' ), 12 );
        add_action( 'init', array( $this, 'register_query_vars' ), 15 );
        add_filter( 'query_vars', array( $this, 'add_video_content_query_vars' ) );
        add_filter( 'post_type_link', array( $this, 'modify_video_content_permalink' ), 10, 2 );
        add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules_after_switch' ) );
        add_action( 'admin_init', array( $this, 'maybe_flush_rewrite_rules' ) );
        add_action( 'pre_get_posts', array( $this, 'setup_video_content_query' ) );
        add_action( 'pre_get_posts', array( $this, 'handle_video_content_archive_rewrite' ) );
        add_filter( 'template_include', array( $this, 'video_content_template_include' ), 99 );

        // Polylang integration
        add_filter( 'pll_get_post_types', array( $this, 'add_video_content_to_polylang' ), 10, 2 );
        add_filter( 'pll_get_taxonomies', array( $this, 'add_video_content_taxonomies_to_polylang' ), 10, 2 );
    }

    /**
     * Register Video Content Custom Post Type
     */
    public function register_video_content_post_type() {
        $labels = array(
            'name'               => __( 'Video', 'feryfit' ),
            'singular_name'      => __( '视频内容', 'feryfit' ),
            'menu_name'          => __( '视频内容', 'feryfit' ),
            'add_new'            => __( '新增', 'feryfit' ),
            'add_new_item'       => __( '新增视频内容', 'feryfit' ),
            'edit_item'          => __( '编辑视频内容', 'feryfit' ),
            'new_item'           => __( '新视频内容', 'feryfit' ),
            'view_item'          => __( '查看视频内容', 'feryfit' ),
            'search_items'       => __( '搜索视频内容', 'feryfit' ),
            'not_found'          => __( '未找到视频内容', 'feryfit' ),
            'not_found_in_trash' => __( '回收站中未找到视频内容', 'feryfit' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 9,
            'query_var'          => true,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'show_in_rest'       => true,
            'rest_base'          => 'video_contents',
            'menu_icon'          => 'dashicons-video-alt2',
        );

        register_post_type( 'video_content', $args );
    }

    /**
     * Add custom rewrite rules for Video Content
     */
    public function add_video_content_rewrite_rules() {
        // 添加 /video 归档页面重写规则
        add_rewrite_rule(
            '^video/?$',
            'index.php?video_content_archive=1',
            'top'
        );
        add_rewrite_rule(
            '^video/page/?([0-9]{1,})/?$',
            'index.php?video_content_archive=1&paged=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^video/([0-9]+)/?$',
            'index.php?post_type=video_content&p=$matches[1]',
            'top'
        );

        // Polylang 多语言支持 - 添加带语言前缀的重写规则
        if ( function_exists( 'pll_languages_list' ) ) {
            $languages = pll_languages_list();
            foreach ( $languages as $lang ) {
                // 为每种语言添加归档页面规则
                add_rewrite_rule(
                    '^' . $lang . '/video/?$',
                    'index.php?video_content_archive=1&lang=' . $lang,
                    'top'
                );
                add_rewrite_rule(
                    '^' . $lang . '/video/page/?([0-9]{1,})/?$',
                    'index.php?video_content_archive=1&lang=' . $lang . '&paged=$matches[1]',
                    'top'
                );
                add_rewrite_rule(
                    '^' . $lang . '/video/([0-9]+)/?$',
                    'index.php?post_type=video_content&p=$matches[1]&lang=' . $lang,
                    'top'
                );
            }
        }
    }

    /**
     * Register query vars on init
     */
    public function register_query_vars() {
        global $wp;
        $wp->add_query_var( 'video_content_archive' );
        $wp->add_query_var( 'video_content' );
    }

    /**
     * Add query vars for Video Content
     */
    public function add_video_content_query_vars( $vars ) {
        $vars[] = 'video_content';
        $vars[] = 'video_content_archive';
        return $vars;
    }

    /**
     * Handle video content archive rewrite
     */
    public function handle_video_content_archive_rewrite( $query ) {
        if ( ! is_admin() && $query->is_main_query() ) {
            if ( $query->get( 'post_type' ) === 'video_content' && ! $query->get( 'p' ) ) {
                $query->set( 'error', '' );
                $query->is_archive = true;
                $query->is_post_type_archive = true;
            }
        }
    }

    /**
     * Modify video content permalink (使用查询参数格式)
     */
    public function modify_video_content_permalink( $post_link, $post ) {
        if ( $post->post_type === 'video_content' ) {
            $path = '/video/' . $post->ID . '/';

            if ( function_exists( 'pll_get_post_language' ) && function_exists( 'pll_default_language' ) ) {
                $lang = pll_get_post_language( $post->ID );
                if ( $lang && $lang !== pll_default_language() ) {
                    $path = '/' . $lang . $path;
                }
            }

            return home_url( $path );
        }
        return $post_link;
    }

    /**
     * Setup video content query on frontend
     */
    public function setup_video_content_query( $query ) {
        if ( is_admin() || ! $query->is_main_query() ) {
            return;
        }

        // 处理归档页面
        if ( get_query_var( 'video_content_archive' ) && ! is_admin() ) {
            $query->set( 'post_type', 'video_content' );
            $query->is_archive = true;
            $query->is_post_type_archive = true;
            $query->is_home = false;
            $query->is_singular = false;

            // 处理 Polylang 语言参数
            $lang = get_query_var( 'lang' );
            if ( $lang && function_exists( 'pll_current_language' ) ) {
                // 设置当前语言上下文
                if ( function_exists( 'PLL' ) ) {
                    PLL()->curlang = PLL()->model->get_language( $lang );
                }
            }
        }

        // 处理单页 - 检查查询参数
        if ( ! is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'video_content' && isset( $_GET['p'] ) ) {
            $video_content_id = intval( $_GET['p'] );
            if ( $video_content_id > 0 ) {
                $query->set( 'p', $video_content_id );
                $query->set( 'post_type', 'video_content' );
                $query->is_singular = true;
                $query->is_single = true;
                $query->is_page = false;
                $query->is_archive = false;
                $query->is_home = false;
            }
        }
    }

    /**
     * Include custom template for video content single posts
     * Reuse video templates
     */
    public function video_content_template_include( $template ) {
        global $wp_query;

        // 检测 /video 归档页面
        if ( get_query_var( 'video_content_archive' ) ) {
            $custom_template = get_template_directory() . '/archive-video.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }

        // 检测单页
        if ( is_singular( 'video_content' ) && ! is_admin() ) {
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
        $this->register_video_content_post_type();
        $this->add_video_content_rewrite_rules();
        flush_rewrite_rules();
    }

    /**
     * Auto-flush rewrite rules if version changed
     */
    public function maybe_flush_rewrite_rules() {
        $version = get_option( 'feryfit_video_content_rewrite_version' );
        if ( $version !== '7' ) {
            $this->register_video_content_post_type();
            $this->add_video_content_rewrite_rules();
            flush_rewrite_rules();
            update_option( 'feryfit_video_content_rewrite_version', '7' );
        }
    }

    /**
     * Add Video Content post type to Polylang translatable types
     */
    public function add_video_content_to_polylang( $post_types, $is_settings ) {
        $post_types['video_content'] = 'video_content';
        return $post_types;
    }

    /**
     * Add Video Content taxonomies to Polylang translatable taxonomies
     */
    public function add_video_content_taxonomies_to_polylang( $taxonomies, $is_settings ) {
        return $taxonomies;
    }
}

// Initialize Video Content Manager
Video_Content_Manager::get_instance();
