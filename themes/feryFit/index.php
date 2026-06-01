<?php
/**
 * The main template file
 */

get_header();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            else :
                ?>
                <div class="welcome-container">
                    <h1 class="welcome-title">🎉 欢迎来到我的主题 (FeryFit)</h1>
                    <p class="welcome-message">
                        感谢您使用 FeryFit 主题！这是一个极简风格的 WordPress 主题。
                    </p>
                    <div class="instruction-section">
                        <h2 class="instruction-title">⚙️ 后台设置指南</h2>
                        <ol class="instruction-list">
                            <li><strong>安装主题后：</strong> 进入 <strong>外观 > 主题</strong>，找到 "FeryFit" 并点击 <strong>启用</strong></li>
                            <li><strong>设置首页：</strong> 进入 <strong>设置 > 阅读</strong>，选择 "静态首页" 或 "您的最新文章"</li>
                            <li><strong>编辑页面内容：</strong> 进入 <strong>页面 > 所有页面</strong>，创建或编辑页面内容</li>
                            <li><strong>自定义主题：</strong> 进入 <strong>外观 > 自定义</strong>，可以修改站点标题、描述等</li>
                            <li><strong>添加文章：</strong> 进入 <strong>文章 > 写文章</strong>，发布您的第一篇博客文章</li>
                        </ol>
                    </div>
                </div>
                <?php
            endif;
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();
