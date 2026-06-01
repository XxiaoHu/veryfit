<?php
/**
 * Template Name: 全屏页面
 * Description: 适用于需要全屏显示内容的页面，如首页横幅等
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php
            while ( have_posts() ) : the_post();

                the_content();

            endwhile; // End of the loop.
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();
