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
        add_action( 'template_redirect', array( $this, 'setup_video_content_query' ), 1 );
        add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules_after_switch' ) );
        add_action( 'admin_init', array( $this, 'maybe_flush_rewrite_rules' ) );
        add_action( 'pre_get_posts', array( $this, 'handle_video_content_archive_rewrite' ) );
        add_filter( 'template_include', array( $this, 'video_content_template_include' ), 99 );

        // Polylang integration
        add_filter( 'pll_get_post_types', array( $this, 'add_video_content_to_polylang' ), 10, 2 );
        add_filter( 'pll_get_taxonomies', array( $this, 'add_video_content_taxonomies_to_polylang' ), 10, 2 );

        // Data sync hooks
        add_action( 'admin_menu', array( $this, 'add_sync_menu_page' ) );
        add_action( 'wp_ajax_sync_video_to_video_content', array( $this, 'ajax_sync_video_to_video_content' ) );
        add_action( 'save_post_video', array( $this, 'auto_sync_video_on_save' ), 10, 3 );
        add_action( 'before_delete_post', array( $this, 'auto_sync_video_on_delete' ), 10, 2 );
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
            return home_url( '/index.php?post_type=video_content&p=' . $post->ID );
        }
        return $post_link;
    }

    /**
     * Setup video content query on frontend
     */
    public function setup_video_content_query() {
        global $wp_query;

        // 处理归档页面
        if ( get_query_var( 'video_content_archive' ) && ! is_admin() ) {
            $wp_query->set( 'post_type', 'video_content' );
            $wp_query->is_archive = true;
            $wp_query->is_post_type_archive = true;
            $wp_query->is_home = false;
            $wp_query->is_singular = false;
        }

        // 处理单页 - 检查查询参数
        if ( ! is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'video_content' && isset( $_GET['p'] ) ) {
            $video_content_id = intval( $_GET['p'] );
            if ( $video_content_id > 0 ) {
                $wp_query->set( 'p', $video_content_id );
                $wp_query->set( 'post_type', 'video_content' );
                $wp_query->is_singular = true;
                $wp_query->is_single = true;
                $wp_query->is_page = false;
                $wp_query->is_archive = false;
                $wp_query->is_home = false;
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
        if ( $version !== '5' ) {
            $this->register_video_content_post_type();
            $this->add_video_content_rewrite_rules();
            flush_rewrite_rules();
            update_option( 'feryfit_video_content_rewrite_version', '5' );
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

    /**
     * Add sync menu page
     */
    public function add_sync_menu_page() {
        add_submenu_page(
            'edit.php?post_type=video_content',
            __( '数据同步', 'feryfit' ),
            __( '数据同步', 'feryfit' ),
            'manage_options',
            'video-content-sync',
            array( $this, 'render_sync_page' )
        );
    }

    /**
     * Render sync page
     */
    public function render_sync_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Video 数据同步到 Video Content', 'feryfit' ); ?></h1>

            <div class="card">
                <h2><?php _e( '同步说明', 'feryfit' ); ?></h2>
                <p><?php _e( '此功能将把所有 Video 文章类型的数据同步到 Video Content 文章类型。', 'feryfit' ); ?></p>
                <ul>
                    <li><?php _e( '✓ 同步标题、内容、缩略图', 'feryfit' ); ?></li>
                    <li><?php _e( '✓ 同步所有自定义字段（meta data）', 'feryfit' ); ?></li>
                    <li><?php _e( '✓ 同步发布状态和日期', 'feryfit' ); ?></li>
                    <li><?php _e( '✓ 保持排序顺序（menu_order）', 'feryfit' ); ?></li>
                    <li><?php _e( '⚠ <strong style="color:red;">同步完成后会自动删除原 Video 数据！</strong>', 'feryfit' ); ?></li>
                </ul>
            </div>

            <div class="card">
                <h2><?php _e( '同步状态', 'feryfit' ); ?></h2>
                <p>
                    <strong><?php _e( 'Video 总数：', 'feryfit' ); ?></strong>
                    <span id="video-count">
                        <?php echo wp_count_posts( 'video' )->publish; ?>
                    </span>
                </p>
                <p>
                    <strong><?php _e( 'Video Content 总数：', 'feryfit' ); ?></strong>
                    <span id="video-content-count">
                        <?php echo wp_count_posts( 'video_content' )->publish; ?>
                    </span>
                </p>
            </div>

            <div class="card">
                <h2><?php _e( '执行同步', 'feryfit' ); ?></h2>
                <p style="color:red; font-weight:bold;">
                    <?php _e( '⚠️ 警告：同步完成后会永久删除所有原 Video 数据，请确认后再操作！', 'feryfit' ); ?>
                </p>
                <p>
                    <button type="button" class="button button-primary button-large" id="sync-video-btn">
                        <?php _e( '开始同步并删除原数据', 'feryfit' ); ?>
                    </button>
                </p>
                <div id="sync-progress" style="display:none; margin-top:20px;">
                    <p><?php _e( '同步进度：', 'feryfit' ); ?><span id="sync-status"></span></p>
                    <progress id="sync-progress-bar" max="100" value="0" style="width:100%;height:30px;"></progress>
                </div>
                <div id="sync-result" style="margin-top:20px;"></div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#sync-video-btn').on('click', function() {
                if (!confirm('<?php _e( '确定要同步数据吗？同步完成后会删除所有原 Video 数据！', 'feryfit' ); ?>')) {
                    return;
                }

                var $btn = $(this);
                var $progress = $('#sync-progress');
                var $result = $('#sync-result');
                var $status = $('#sync-status');
                var $progressBar = $('#sync-progress-bar');

                $btn.prop('disabled', true).text('<?php _e( '同步中...', 'feryfit' ); ?>');
                $progress.show();
                $result.html('');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'sync_video_to_video_content',
                        nonce: '<?php echo wp_create_nonce( 'video_content_sync_nonce' ); ?>'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).text('<?php _e( '开始同步并删除原数据', 'feryfit' ); ?>');
                        $progress.hide();

                        if (response.success) {
                            $result.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                            // 更新计数
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            $result.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).text('<?php _e( '开始同步并删除原数据', 'feryfit' ); ?>');
                        $progress.hide();
                        $result.html('<div class="notice notice-error"><p><?php _e( '同步失败，请重试', 'feryfit' ); ?></p></div>');
                    }
                });
            });
        });
        </script>

        <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
            max-width: 800px;
        }
        .card h2 {
            margin-top: 0;
        }
        .card ul {
            list-style: none;
            padding-left: 0;
        }
        .card ul li {
            padding: 5px 0;
        }
        </style>
        <?php
    }

    /**
     * AJAX sync video to video content
     */
    public function ajax_sync_video_to_video_content() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'video_content_sync_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( '权限验证失败', 'feryfit' ) ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '没有同步权限', 'feryfit' ) ) );
        }

        $result = $this->sync_video_to_video_content();

        if ( $result['success'] ) {
            wp_send_json_success( array( 'message' => $result['message'] ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }

    /**
     * Sync video to video content
     */
    public function sync_video_to_video_content() {
        $videos = get_posts( array(
            'post_type'      => 'video',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ) );

        if ( empty( $videos ) ) {
            return array(
                'success' => false,
                'message' => __( '没有找到需要同步的 Video 数据', 'feryfit' ),
            );
        }

        $synced_count = 0;
        $error_count = 0;
        $deleted_count = 0;
        $video_ids_to_delete = array();

        foreach ( $videos as $video ) {
            $result = $this->sync_single_video( $video->ID );
            if ( $result ) {
                $synced_count++;
                $video_ids_to_delete[] = $video->ID;
            } else {
                $error_count++;
            }
        }

        // 同步完成后删除原 Video 数据
        if ( ! empty( $video_ids_to_delete ) ) {
            foreach ( $video_ids_to_delete as $video_id ) {
                $deleted = wp_delete_post( $video_id, true ); // true = 强制删除，不放入回收站
                if ( $deleted ) {
                    $deleted_count++;
                }
            }
        }

        $message = sprintf(
            __( '同步完成！成功同步 %d 条记录，失败 %d 条。已删除 %d 条原 Video 数据。', 'feryfit' ),
            $synced_count,
            $error_count,
            $deleted_count
        );

        return array(
            'success' => true,
            'message' => $message,
            'synced'  => $synced_count,
            'errors'  => $error_count,
            'deleted' => $deleted_count,
        );
    }

    /**
     * Sync single video to video content
     */
    public function sync_single_video( $video_id ) {
        $video = get_post( $video_id );
        if ( ! $video || $video->post_type !== 'video' ) {
            return false;
        }

        // Check if already synced
        $existing = get_posts( array(
            'post_type'  => 'video_content',
            'meta_key'   => '_synced_from_video_id',
            'meta_value' => $video_id,
            'fields'     => 'ids',
        ) );

        if ( ! empty( $existing ) ) {
            // Update existing video_content
            $video_content_id = $existing[0];
            $updated = wp_update_post( array(
                'ID'           => $video_content_id,
                'post_title'   => $video->post_title,
                'post_content' => $video->post_content,
                'post_status'  => $video->post_status,
                'post_date'    => $video->post_date,
                'menu_order'   => $video->menu_order,
            ) );

            if ( is_wp_error( $updated ) ) {
                return false;
            }
        } else {
            // Create new video_content
            $video_content_id = wp_insert_post( array(
                'post_type'    => 'video_content',
                'post_title'   => $video->post_title,
                'post_content' => $video->post_content,
                'post_status'  => $video->post_status,
                'post_date'    => $video->post_date,
                'menu_order'   => $video->menu_order,
            ) );

            if ( is_wp_error( $video_content_id ) ) {
                return false;
            }

            // Store sync relationship
            update_post_meta( $video_content_id, '_synced_from_video_id', $video_id );
        }

        // Sync thumbnail
        $thumbnail_id = get_post_thumbnail_id( $video_id );
        if ( $thumbnail_id ) {
            set_post_thumbnail( $video_content_id, $thumbnail_id );
        }

        // Sync all meta data
        $meta_data = get_post_meta( $video_id );
        foreach ( $meta_data as $key => $values ) {
            // Skip thumbnail meta
            if ( $key === '_thumbnail_id' ) {
                continue;
            }

            delete_post_meta( $video_content_id, $key );
            foreach ( $values as $value ) {
                add_post_meta( $video_content_id, $key, maybe_unserialize( $value ) );
            }
        }

        return true;
    }

    /**
     * Auto sync video on save
     */
    public function auto_sync_video_on_save( $post_id, $post, $update ) {
        // Skip auto-saves and revisions
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Sync to video_content
        $this->sync_single_video( $post_id );
    }

    /**
     * Auto sync video on delete
     */
    public function auto_sync_video_on_delete( $post_id, $post ) {
        if ( $post->post_type !== 'video' ) {
            return;
        }

        // Find and delete corresponding video_content
        $existing = get_posts( array(
            'post_type'  => 'video_content',
            'meta_key'   => '_synced_from_video_id',
            'meta_value' => $post_id,
            'fields'     => 'ids',
        ) );

        if ( ! empty( $existing ) ) {
            foreach ( $existing as $video_content_id ) {
                wp_delete_post( $video_content_id, true );
            }
        }
    }
}

// Initialize Video Content Manager
Video_Content_Manager::get_instance();
