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
        pll_register_string( 'Sleep & Health', 'Sleep & Health', 'feryfit' );
        pll_register_string( 'Chat on WhatsApp', 'Chat on WhatsApp', 'feryfit' );
        pll_register_string( 'Send an Email', 'Send an Email', 'feryfit' );
        pll_register_string( 'Message us on Facebook', 'Message us on Facebook', 'feryfit' );
        pll_register_string( 'Video', 'Video', 'feryfit' );
        pll_register_string( 'FAQ', 'FAQ', 'feryfit' );

        // Footer 文本字符串
        pll_register_string( 'Smart Tech Meets Everyday Life.', 'Smart Tech Meets Everyday Life.', 'feryfit' );
        pll_register_string( 'Product', 'Product', 'feryfit' );
        pll_register_string( 'About & Support', 'About & Support', 'feryfit' );
        pll_register_string( 'Policy', 'Policy', 'feryfit' );
        pll_register_string( 'Contact us', 'Contact us', 'feryfit' );
        pll_register_string( 'Customer service:', 'Customer service:', 'feryfit' );
        pll_register_string( 'Whatsapp', 'Whatsapp', 'feryfit' );
        pll_register_string( 'Phone Support', 'Phone Support', 'feryfit' );
        pll_register_string( 'Follow us', 'Follow us', 'feryfit' );
        pll_register_string( 'VeryfitVip.Store', 'VeryfitVip.Store', 'feryfit' );
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

/**
 * 获取支持多语言的归档页面链接
 *
 * @param string $post_type 文章类型
 * @return string 归档页面URL
 */
function feryfit_get_archive_link( $post_type ) {
    // 对于 video_content，使用自定义 /video/ URL
    if ( $post_type === 'video_content' ) {
        $archive_link = home_url( '/video/' );

        // 支持 Polylang 多语言
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language();
            $default_lang = pll_default_language();
            if ( $current_lang && $current_lang !== $default_lang ) {
                $archive_link = home_url( '/' . $current_lang . '/video/' );
            }
        }

        return $archive_link;
    }

    // 其他文章类型使用默认的归档链接
    return get_post_type_archive_link( $post_type );
}

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
                    // 获取归档链接（支持多语言）
                    $archive_link = feryfit_get_archive_link( $post_type );

                    // 获取归档标题（支持翻译）
                    if ( $post_type === 'video_content' ) {
                        $archive_title = pll__( 'Video', 'feryfit' );
                    } elseif ( $post_type === 'faq' ) {
                        $archive_title = pll__( 'FAQ', 'feryfit' );
                    } else {
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
            $post_type = get_query_var( 'post_type' );
            $archive_title = post_type_archive_title( '', false );

            // 为特定文章类型使用翻译
            if ( $post_type === 'video_content' || $post_type === 'video' ) {
                $archive_title = pll__( 'Video', 'feryfit' );
            } elseif ( $post_type === 'faq' ) {
                $archive_title = pll__( 'FAQ', 'feryfit' );
            }

            // 获取归档链接（支持多语言）
            $archive_link = feryfit_get_archive_link( $post_type );

            $items[] = array(
                'title' => $archive_title,
                'url'   => $archive_link,
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