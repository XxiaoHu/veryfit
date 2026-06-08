<?php

// 初始化 session（用于跟踪用户访问路径）
function feryfit_init_session() {
    if ( ! session_id() ) {
        session_start();
    }
}
add_action( 'init', 'feryfit_init_session' );


function feryfit_setup() {
    load_theme_textdomain( 'feryfit', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'editor-styles' );
    
    // 添加自定义 Logo 支持
    add_theme_support( 'custom-logo', array(
        'height'      => 70,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
    ) );
    
    // 注册导航菜单位置
    register_nav_menus( array(
        'primary'        => __( 'Primary Menu', 'feryfit' ),
        'footer-product' => __( 'Footer Product Menu', 'feryfit' ),
        'footer-about'   => __( 'Footer About & Support Menu', 'feryfit' ),
        'footer-policy'  => __( 'Footer Policy Menu', 'feryfit' ),
    ) );
}
add_action( 'after_setup_theme', 'feryfit_setup' );

function feryfit_scripts() {
    wp_enqueue_style( 'feryfit-style', get_stylesheet_uri() );
    wp_enqueue_style( 'feryfit-floating-chat', get_template_directory_uri() . '/inc/template-functions/floating-chat.css', array(), '1.0.0' );
    wp_enqueue_style( 'feryfit-faq-vote', get_template_directory_uri() . '/assets/css/faq-vote.css', array(), '1.0.0' );
    wp_enqueue_style( 'feryfit-blog-single', get_template_directory_uri() . '/assets/css/blog-single.css', array(), '1.0.0' );

    // 加载 breadcrumb 全局样式
    wp_enqueue_style( 'feryfit-breadcrumb', get_template_directory_uri() . '/assets/css/breadcrumb.css', array(), '1.0.0' );

    // 为 FAQ 归档页面加载 faq-archive 样式
    if ( is_post_type_archive( 'faq' ) ) {
        wp_enqueue_style( 'feryfit-faq-archive', get_template_directory_uri() . '/assets/css/faq-archive.css', array(), '1.0.0' );
    }

    // 为 Video 归档页面加载 video-archive 样式
    if ( is_post_type_archive( 'video' ) || is_post_type_archive( 'video_content' ) || get_query_var( 'video_content_archive' ) ) {
        wp_enqueue_style( 'feryfit-video-archive', get_template_directory_uri() . '/assets/css/video-archive.css', array(), '1.0.0' );
    }

    // 为 404 页面加载 404 样式
    if ( is_404() ) {
        wp_enqueue_style( 'feryfit-404', get_template_directory_uri() . '/assets/css/404.css', array(), '1.0.0' );
    }

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'feryfit-header', get_template_directory_uri() . '/assets/js/header-drawer.js', array(), '1.0.0', true );
    wp_enqueue_script( 'feryfit-footer-accordion', get_template_directory_uri() . '/assets/js/footer-accordion.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'feryfit_scripts' );

function feryfit_search_filter( $query ) {
    if ( $query->is_search() && ! is_admin() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'faq', 'blog', 'video_content' ) );
        $query->set( 'posts_per_page', 10 );
    }
}
add_action( 'pre_get_posts', 'feryfit_search_filter' );

function feryfit_register_polylang_strings() {
    if ( function_exists( 'pll_register_string' ) ) {
        pll_register_string( 'Submit', 'Submit', 'feryfit' );
        pll_register_string( 'Submitting...', 'Submitting...', 'feryfit' );
        pll_register_string( 'Share your thoughts...', 'Share your thoughts...', 'feryfit' );
        pll_register_string( 'results have been found', 'results have been found', 'feryfit' );
        pll_register_string( 'Home', 'Home', 'feryfit' );
        pll_register_string( 'Search Results', 'Search Results', 'feryfit' );
        pll_register_string( 'Page Not Found', 'Page Not Found', 'feryfit' );
    }
}
add_action( 'init', 'feryfit_register_polylang_strings' );

// 加载客服浮窗组件
require get_template_directory() . '/inc/template-functions/floating-chat.php';

function feryfit_editor_scripts() {
    wp_enqueue_script( 'feryfit-editor-script', get_template_directory_uri() . '/assets/js/editor.js', array(), '1.0.0', true );
    wp_localize_script( 'feryfit-editor-script', 'feryfitData', array(
        'templateUri' => get_template_directory_uri(),
    ) );
}
add_action( 'enqueue_block_editor_assets', 'feryfit_editor_scripts' );

function feryfit_register_blocks() {
    $blocks = array(
        'hero-banner',
        'blog-list',
        'faq-list',
        'faq-pagination',
        'video-carousel',
        'video-grid',
        'warranty-banner',
        'support-banner',
        'extended-warranty-banner',
        'stats-banner',
        'warranty-application',
        'custom-banner',
        'contact-cards',
        'contact-form',
        'breadcrumb',
        'coming-soon',
    );
    
    foreach ( $blocks as $block ) {
        $block_path = get_template_directory() . '/blocks/build/' . $block;
        
        if ( file_exists( $block_path . '/block.json' ) ) {
            register_block_type_from_metadata( $block_path );
        }
    }
}
add_action( 'init', 'feryfit_register_blocks' );

// Load Warranty Application Manager
require_once get_template_directory() . '/includes/class-warranty-manager.php';

// Load Contact Manager
require_once get_template_directory() . '/includes/class-contact-manager.php';

// Load Customer Service Manager
require_once get_template_directory() . '/includes/class-customer-service-manager.php';

add_filter('show_admin_bar', '__return_false');

// Load FAQs Manager
require_once get_template_directory() . '/includes/class-faqs-manager.php';

// Load Video Content Manager
require_once get_template_directory() . '/includes/class-video-content-manager.php';

// Load Video Content Functions
require_once get_template_directory() . '/includes/video-content-functions.php';

// Load FAQs helper functions (frontend and backend)
require_once get_template_directory() . '/includes/faqs-functions.php';

// Load Blog Manager
require_once get_template_directory() . '/includes/class-blog-manager.php';

// Load Blog helper functions (frontend and backend)
require_once get_template_directory() . '/includes/blog-functions.php';



// 隐藏后台左侧的“文章”菜单
function custom_remove_admin_menus() {
    // 移除“文章”菜单，'edit.php' 是它的菜单标识
    remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'custom_remove_admin_menus', 999 );

// 添加页脚主题自定义选项
function feryfit_customize_register( $wp_customize ) {
    // 页脚设置面板
    $wp_customize->add_section( 'feryfit_footer_settings', array(
        'title'    => __( 'Footer Settings', 'feryfit' ),
        'priority' => 100,
    ) );

    // 页脚标签文字
    $wp_customize->add_setting( 'footer_tagline', array(
        'default'           => 'Smart Tech Meets Everyday Life.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'footer_tagline', array(
        'label'    => __( 'Footer Tagline', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'text',
    ) );

    // 版权信息
    $wp_customize->add_setting( 'footer_copyright', array(
        'default'           => '&copy; ' . date( 'Y' ) . ' VeryfitVip.Store.',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'footer_copyright', array(
        'label'    => __( 'Copyright Text', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'text',
    ) );

    // 页脚菜单标题 - Product
    $wp_customize->add_setting( 'footer_title_product', array(
        'default'           => 'Product',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'footer_title_product', array(
        'label'    => __( 'Product Section Title', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'text',
    ) );

    // 页脚菜单标题 - About & Support
    $wp_customize->add_setting( 'footer_title_about', array(
        'default'           => 'About & Support',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'footer_title_about', array(
        'label'    => __( 'About & Support Section Title', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'text',
    ) );

    // 页脚菜单标题 - Policy
    $wp_customize->add_setting( 'footer_title_policy', array(
        'default'           => 'Policy',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'footer_title_policy', array(
        'label'    => __( 'Policy Section Title', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'text',
    ) );

    // 页脚菜单标题 - Contact us
    $wp_customize->add_setting( 'footer_title_contact', array(
        'default'           => 'Contact us',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'footer_title_contact', array(
        'label'    => __( 'Contact Section Title', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'text',
    ) );

    // 联系信息链接配置
    // 项目1 - Customer service
    $wp_customize->add_setting( 'footer_contact_1_url', array(
        'default'           => 'mailto:service@runstar.com',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_contact_1_url', array(
        'label'    => __( 'Customer Service Link', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'url',
        'description' => __( 'URL for "Customer service" link. Use mailto: for email.', 'feryfit' ),
    ) );

    // 项目2 - Whatsapp
    $wp_customize->add_setting( 'footer_contact_2_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_contact_2_url', array(
        'label'    => __( 'Whatsapp Link', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'url',
        'description' => __( 'URL for "Whatsapp" link. Use https://wa.me/XXX format.', 'feryfit' ),
    ) );

    // 项目3 - Phone Support
    $wp_customize->add_setting( 'footer_contact_3_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'footer_contact_3_url', array(
        'label'    => __( 'Phone Support Link', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'url',
        'description' => __( 'URL for "Phone Support" link. Use tel: for phone number.', 'feryfit' ),
    ) );

    // 社交媒体链接
    $wp_customize->add_setting( 'footer_social_items', array(
        'default'           => json_encode( array(
            array( 'icon' => 'facebook', 'url' => '#' ),
            array( 'icon' => 'twitter', 'url' => '#' ),
            array( 'icon' => 'instagram', 'url' => '#' ),
        ) ),
        'sanitize_callback' => 'wp_json_encode',
    ) );
    $wp_customize->add_control( 'footer_social_items', array(
        'label'    => __( 'Social Items (JSON)', 'feryfit' ),
        'section'  => 'feryfit_footer_settings',
        'type'     => 'textarea',
    ) );
}
add_action( 'customize_register', 'feryfit_customize_register' );

/**
 * 获取面包屑导航数据
 *
 * @return array 面包屑项目数组
 */
function feryfit_breadcrumb_get_items() {
    $items = array();

    // 添加首页
    $items[] = array(
        'title' => pll__( 'Home', 'feryfit' ),
        'url'   => home_url( '/' ),
        'is_current' => false,
    );

    // 如果是首页，直接返回
    if ( is_front_page() ) {
        $items[0]['is_current'] = true;
        return $items;
    }

    // 获取当前页面信息
    global $post;

    if ( is_singular() && $post ) {
        // 单篇文章/页面
        $post_title = get_the_title();
        $post_type = get_post_type( $post );

        // 如果是自定义文章类型（非 page 和 post），检查是否访问过归档页
        if ( $post_type && ! in_array( $post_type, array( 'page', 'post' ), true ) ) {
            // video_content 和 faq 始终显示归档页链接
            $always_show_archive = array( 'video_content', 'faq' );

            // 检查session中是否记录了访问过该文章类型的归档页
            $visited_archives = isset( $_SESSION['feryfit_visited_archives'] ) ? $_SESSION['feryfit_visited_archives'] : array();

            // 如果是需要始终显示的类型，或者用户之前访问过该类型的归档页，才显示归档页
            if ( in_array( $post_type, $always_show_archive ) || in_array( $post_type, $visited_archives ) ) {
                $post_type_obj = get_post_type_object( $post_type );
                if ( $post_type_obj ) {
                    // 对于 video_content，使用自定义 URL
                    if ( $post_type === 'video_content' ) {
                        $archive_link = home_url( '/video/' );
                        $archive_title = $post_type_obj->labels->name;
                    } else {
                        $archive_link = get_post_type_archive_link( $post_type );
                        $archive_title = $post_type_obj->labels->name;
                    }

                    $items[] = array(
                        'title' => $archive_title,
                        'url'   => $archive_link,
                        'is_current' => false,
                    );
                }
            }
        }

        // 获取父页面层级（仅适用于 page 类型）
        if ( 'page' === $post_type ) {
            $ancestors = get_post_ancestors( $post->ID );
            $ancestors = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor_id ) {
                $ancestor = get_post( $ancestor_id );
                if ( $ancestor ) {
                    $items[] = array(
                        'title' => $ancestor->post_title,
                        'url'   => get_permalink( $ancestor_id ),
                        'is_current' => false,
                    );
                }
            }
        }

        // 添加当前页面
        $items[] = array(
            'title' => $post_title,
            'url'   => get_permalink(),
            'is_current' => true,
        );
    } elseif ( is_archive() ) {
        // 归档页面 - 记录到session
        if ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            if ( ! isset( $_SESSION['feryfit_visited_archives'] ) ) {
                $_SESSION['feryfit_visited_archives'] = array();
            }
            if ( ! in_array( $post_type, $_SESSION['feryfit_visited_archives'] ) ) {
                $_SESSION['feryfit_visited_archives'][] = $post_type;
            }
        }
        
        // 归档页面
        if ( is_category() ) {
            $items[] = array(
                'title' => single_cat_title( '', false ),
                'url'   => get_category_link( get_query_var( 'cat' ) ),
                'is_current' => true,
            );
        } elseif ( is_tag() ) {
            $items[] = array(
                'title' => single_tag_title( '', false ),
                'url'   => get_tag_link( get_query_var( 'tag_id' ) ),
                'is_current' => true,
            );
        } elseif ( is_post_type_archive() ) {
            $items[] = array(
                'title' => post_type_archive_title( '', false ),
                'url'   => get_post_type_archive_link( get_query_var( 'post_type' ) ),
                'is_current' => true,
            );
        } elseif ( is_date() ) {
            $items[] = array(
                'title' => get_the_date( 'Y年m月' ),
                'url'   => '',
                'is_current' => true,
            );
        } else {
            $items[] = array(
                'title' => get_the_archive_title(),
                'url'   => '',
                'is_current' => true,
            );
        }
    } elseif ( is_search() ) {
        // 搜索结果页面
        $items[] = array(
            'title' => pll__( 'Search Results', 'feryfit' ) . ': ' . get_search_query(),
            'url'   => '',
            'is_current' => true,
        );
    } elseif ( is_404() ) {
        // 404页面
        $items[] = array(
            'title' => pll__( 'Page Not Found', 'feryfit' ),
            'url'   => '',
            'is_current' => true,
        );
    }

    return $items;
}

/**
 * 渲染面包屑导航 HTML
 *
 * @param array $args 配置参数
 * @return void
 */
function feryfit_breadcrumb_render( $args = array() ) {
    $defaults = array(
        'container_class' => 'feryfit-breadcrumb',
        'item_class'      => 'feryfit-breadcrumb__item',
        'link_class'      => 'feryfit-breadcrumb__link',
        'current_class'   => 'feryfit-breadcrumb__item--current',
        'separator'       => '/',
        'separator_class' => 'feryfit-breadcrumb__separator',
        'echo'            => true,
    );

    $args = wp_parse_args( $args, $defaults );
    $items = feryfit_breadcrumb_get_items();

    if ( empty( $items ) ) {
        return;
    }

    $html = '<div class="' . esc_attr( $args['container_class'] ) . '">';

    foreach ( $items as $index => $item ) {
        $is_last = ( $index === count( $items ) - 1 );
        $item_classes = $args['item_class'];

        if ( $item['is_current'] ) {
            $item_classes .= ' ' . $args['current_class'];
        }

        $html .= '<span class="' . esc_attr( $item_classes ) . '">';

        if ( ! $item['is_current'] && ! empty( $item['url'] ) ) {
            $html .= '<a href="' . esc_url( $item['url'] ) . '" class="' . esc_attr( $args['link_class'] ) . '">';
            $html .= esc_html( $item['title'] );
            $html .= '</a>';
        } else {
            $html .= esc_html( $item['title'] );
        }

        $html .= '</span>';

        if ( ! $is_last ) {
            $html .= '<span class="' . esc_attr( $args['separator_class'] ) . '">' . esc_html( $args['separator'] ) . '</span>';
        }
    }

    $html .= '</div>';

    if ( $args['echo'] ) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * 增加 WordPress 文件上传大小限制
 * 设置最大上传文件大小为 50MB
 */
function feryfit_increase_upload_size( $size ) {
    // 50MB = 50 * 1024 * 1024 bytes
    return 52428800;
}
add_filter( 'upload_size_limit', 'feryfit_increase_upload_size' );

/**
 * 在 WordPress 管理后台显示当前上传限制
 */
function feryfit_display_upload_limit() {
    $max_upload = wp_max_upload_size();
    $max_upload_mb = size_format( $max_upload );
    echo '<div class="notice notice-info"><p>';
    echo '<strong>当前最大上传文件大小：</strong> ' . esc_html( $max_upload_mb );
    echo '</p></div>';
}
add_action( 'admin_notices', 'feryfit_display_upload_limit' );