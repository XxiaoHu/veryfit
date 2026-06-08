<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="page" class="site">

<header id="masthead" class="site-header">
    <div class="header-container">
        <!-- 移动端：菜单汉堡按钮（左侧） -->
        <button class="menu-toggle" aria-label="<?php esc_attr_e( '打开菜单', 'feryfit' ); ?>" aria-expanded="false">
            <span class="menu-toggle-icon"></span>
        </button>

        <!-- Logo 位置 -->
        <div class="site-branding">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    ?>
                    <span class="site-title"><?php bloginfo( 'name' ); ?></span>
                    <?php
                }
                ?>
            </a>
        </div>

        <!-- 桌面端导航菜单 -->
        <nav id="site-navigation" class="main-navigation">
            <?php
            $menu_exists = has_nav_menu('primary');
            
            if ($menu_exists) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'depth'          => 1,
                ) );
            } else {
                ?>
                <ul id="primary-menu" class="menu">
                    <li><a href="<?php echo admin_url('nav-menus.php'); ?>">请配置导航菜单</a></li>
                </ul>
                <?php
            }
            ?>
        </nav>

        <!-- Polylang 语言切换器（桌面端下拉 / 移动端触发右侧抽屉） -->
        <div class="language-switcher">
            <?php
            if ( function_exists( 'pll_the_languages' ) ) {
                ?>
                <!-- 桌面端下拉 -->
                <div class="lang-dropdown">
                    <button class="lang-dropdown-toggle" aria-expanded="false">
                        <img style="width: 24px;" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Language 1.png' ); ?>" alt="Language" class="lang-icon" />
                        <span class="lang-current">
                            <?php 
                            $current_lang = pll_current_language('name');
                            echo esc_html($current_lang);
                            ?>
                        </span>
                        <svg class="lang-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="lang-dropdown-menu">
                        <?php
                        pll_the_languages( array(
                            'show_flags'    => true,
                            'show_names'    => true,
                            'display_names_as' => 'name',
                            'hide_if_empty' => true,
                            'dropdown'      => false,
                            'echo'          => true,
                        ) );
                        ?>
                    </div>
                </div>
                <!-- 移动端语言切换按钮 -->
                <button class="lang-toggle" aria-label="<?php esc_attr_e( '切换语言', 'feryfit' ); ?>" aria-expanded="false">
                    <img style="width: 24px;" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Language 1.png' ); ?>" alt="Language" class="lang-icon" />
                </button>
                <?php
            }
            ?>
        </div>
    </div>

    <!-- 移动端：左侧菜单抽屉 -->
    <div class="mobile-menu-drawer" id="mobile-menu-drawer">
        <div class="drawer-header">
            <button class="drawer-close" aria-label="<?php esc_attr_e( '关闭菜单', 'feryfit' ); ?>">&times;</button>
            <?php
            if ( has_custom_logo() ) {
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $logo = wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'drawer-logo-image' ) );
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="drawer-logo-link">
                    <?php echo $logo; ?>
                </a>
                <?php
            } else {
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="drawer-logo-link">
                    <span class="drawer-site-title"><?php bloginfo( 'name' ); ?></span>
                </a>
                <?php
            }
            ?>
        </div>
        <nav class="mobile-navigation">
            <?php
            if ($menu_exists) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'mobile-primary-menu',
                    'container'      => false,
                    'depth'          => 1,
                ) );
            } else {
                ?>
                <ul id="mobile-primary-menu" class="menu">
                    <li><a href="<?php echo admin_url('nav-menus.php'); ?>">请配置导航菜单</a></li>
                </ul>
                <?php
            }
            ?>
        </nav>
    </div>

    <!-- 移动端：右侧语言选择抽屉 -->
    <div class="lang-drawer" id="lang-drawer">
        <div class="drawer-header">
            <h3><?php _e( 'Language', 'feryfit' ); ?></h3>
            <button class="drawer-close" aria-label="<?php esc_attr_e( '关闭', 'feryfit' ); ?>">&times;</button>
        </div>
        <div class="lang-drawer-list">
            <?php
            if ( function_exists( 'pll_the_languages' ) ) {
                pll_the_languages( array(
                    'show_flags'    => true,
                    'show_names'    => true,
                    'display_names_as' => 'name',
                    'hide_if_empty' => true,
                    'dropdown'      => false,
                    'echo'          => true,
                ) );
            }
            ?>
        </div>
    </div>

    <!-- 遮罩层 -->
    <div class="drawer-overlay" id="drawer-overlay"></div>
</header>