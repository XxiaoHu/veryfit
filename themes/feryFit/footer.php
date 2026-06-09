<?php

/**
 * The template for displaying the footer
 */

?>

</main><!-- #main -->
</div><!-- #primary -->

<footer id="colophon" class="site-footer">
    <div class="footer-content">
        <div class="footer-section footer-brand">
            <div class="footer-logo">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/footerlogo.png" alt="<?php echo get_bloginfo('name'); ?>" />
            </div>
            <p class="footer-tagline">
                <?php echo esc_html( pll__( 'Smart Tech Meets Everyday Life.', 'feryfit' ) ); ?>
            </p>
        </div>

        <div class="footer-section footer-menu">
            <button class="accordion-button" aria-expanded="false">
                <h4 class="footer-section-title"><?php echo esc_html( pll__( 'Product', 'feryfit' ) ); ?></h4>
                <span class="accordion-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="accordion-content">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-product',
                    'menu_class'     => 'footer-nav',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                    'container'      => false,
                ));
                ?>
            </div>
        </div>

        <div class="footer-section footer-menu">
            <button class="accordion-button" aria-expanded="false">
                <h4 class="footer-section-title"><?php echo esc_html( pll__( 'About & Support', 'feryfit' ) ); ?></h4>
                <span class="accordion-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="accordion-content">
                <?php
                wp_nav_menu(array('theme_location' => 'footer-about', 'menu_class' => 'footer-nav', 'fallback_cb' => false, 'depth' => 1, 'container' => false));
                ?>
            </div>
        </div>

        <div class="footer-section footer-menu">
            <button class="accordion-button" aria-expanded="false">
                <h4 class="footer-section-title"><?php echo esc_html( pll__( 'Policy', 'feryfit' ) ); ?></h4>
                <span class="accordion-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="accordion-content">
                <?php
                wp_nav_menu(array('theme_location' => 'footer-policy', 'menu_class' => 'footer-nav', 'fallback_cb' => false, 'depth' => 1, 'container' => false));
                ?>
            </div>
        </div>

        <div class="footer-section footer-contact">
            <button class="accordion-button" aria-expanded="true">
                <h4 class="footer-section-title"><?php echo esc_html( pll__( 'Contact us', 'feryfit' ) ); ?></h4>
                <span class="accordion-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="accordion-content">
                <ul class="footer-contact-list">
                    <!-- Customer service -->
                    <?php
                    $customer_email = get_option('feryfit_email', '');
                    if ( $customer_email ) :
                    ?>
                    <li class="footer-contact-item">
                        <span class="footer-contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                <path d="M4 21H22V13V5H13H4V13V21Z" stroke="#808080" stroke-width="2" stroke-linejoin="round" />
                                <path d="M4 9L13 15L22 9" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <a href="mailto:<?php echo esc_attr($customer_email); ?>" class="footer-contact-link">
                            <span class="footer-contact-label"><?php echo esc_html( pll__( 'Customer service:', 'feryfit' ) ); ?></span>
                            <span class="footer-contact-value"><?php echo esc_html($customer_email); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Whatsapp -->
                    <?php
                    $whatsapp_url = get_option('feryfit_whatsapp', '');
                    if ( $whatsapp_url ) :
                        // 获取当前页面语言
                        $current_lang = get_locale();
                        if ( empty( $current_lang ) ) {
                            $current_lang = 'en';
                        }

                        // 获取当前页面标题
                        // $page_title = wp_title( '', false );

                        // 添加页面信息到 WhatsApp 链接
                        $separator = strpos( $whatsapp_url, '?' ) !== false ? '&' : '?';
                        $message_parts = array();
                            $message_parts[] = 'Page: footer' ;

                        if ( ! empty( $current_lang ) ) {
                            $message_parts[] = 'Lang: ' . $current_lang;
                        }
                        $message_text = implode( ' | ', $message_parts );
                        if ( ! empty( $message_text ) ) {
                            $whatsapp_url .= $separator . 'text=' . urlencode( $message_text );
                        }
                    ?>
                    <li class="footer-contact-item">
                        <span class="footer-contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                <path d="M10.5083 8.01657C10.7792 8.01657 10.9958 8.1249 11.1042 8.34157L11.9167 9.80407C12.025 10.0207 12.025 10.2374 11.9167 10.4541L11.3208 11.5374C11.3208 11.5374 11.5375 12.7291 12.5125 13.7041C13.4875 14.6791 14.6792 14.8957 14.6792 14.8957L16.0333 14.5707C16.25 14.4624 16.4667 14.4624 16.6833 14.5707L18.2 15.3832C18.4167 15.4916 18.525 15.7082 18.525 15.9791V17.7124C18.525 18.5791 17.7125 19.2291 16.9 18.9582C15.2208 18.3624 12.5667 17.2791 10.8875 15.5999C9.20832 13.9207 8.12498 11.2666 7.52915 9.5874C7.25832 8.7749 7.85415 7.9624 8.77498 7.9624H10.5083V8.01657Z" stroke="#808080" stroke-width="2" />
                                <path d="M13.3792 3.0874C7.85417 3.0874 3.35834 7.58324 3.35834 13.1082C3.35834 15.0582 3.9 16.8457 4.875 18.3624L3.35834 23.0749L7.85417 21.4499C9.425 22.4791 11.3208 23.1291 13.3792 23.1291C18.9042 23.1291 23.4 18.6332 23.4 13.1082C23.4 7.58324 18.9042 3.0874 13.3792 3.0874Z" stroke="#808080" stroke-width="2" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" class="footer-contact-link">
                            <span class="footer-contact-label"><?php echo esc_html( pll__( 'Whatsapp', 'feryfit' ) ); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Phone Support -->
                    <?php
                    $phone_number = get_option('feryfit_phone', '');
                    if ( $phone_number ) :
                    ?>
                    <li class="footer-contact-item">
                        <span class="footer-contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                <path d="M18.4166 2.1665H7.58331C6.68585 2.1665 5.95831 2.89404 5.95831 3.7915V22.2082C5.95831 23.1056 6.68585 23.8332 7.58331 23.8332H18.4166C19.3141 23.8332 20.0416 23.1056 20.0416 22.2082V3.7915C20.0416 2.89404 19.3141 2.1665 18.4166 2.1665Z" stroke="#808080" stroke-width="2" />
                                <path d="M11.9167 5.4165H14.0834" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.8333 20.5835H15.1666" stroke="#808080" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <a href="tel:<?php echo esc_attr($phone_number); ?>" class="footer-contact-link">
                            <span class="footer-contact-label"><?php echo esc_html( pll__( 'Phone Support', 'feryfit' ) ); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>


    </div>
    <div class="footer-section footer-social-section">
        <div class="footer-social">
            <?php
            $facebook_url = get_option('feryfit_facebook', '#');
            if ( $facebook_url && $facebook_url !== '#' ) :
            ?>
            <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" class="footer-social-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M22.6777 0C23.4061 0 24 0.593903 24 1.32227V22.6777C24 23.4061 23.4061 24 22.6777 24V23.9912H16.5635V14.7021H19.6836L20.1514 11.0781H16.5635V8.76758C16.5635 7.7155 16.8516 7.00488 18.3623 7.00488H20.2773V3.76758C19.9446 3.72262 18.8023 3.62402 17.4805 3.62402C14.7111 3.62412 12.8234 5.31429 12.8232 8.40723V11.0781H9.69336V14.7021H12.8232V23.9912H1.32227C0.593903 23.9912 0 23.3973 0 22.6689V1.32227C0 0.593903 0.593903 0 1.32227 0H22.6777Z" fill="white" />
                </svg>
                <span><?php echo esc_html( pll__( 'Follow us', 'feryfit' ) ); ?></span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <p class="footer-copyright">
            &copy; <?php echo date('Y'); ?> <?php echo esc_html( pll__( 'VeryfitVip.Store', 'feryfit' ) ); ?>.
        </p>
    </div>
</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>

</html>