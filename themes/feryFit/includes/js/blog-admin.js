/**
 * Blog Admin JavaScript
 */
(function($) {
    'use strict';
    
    var BlogAdmin = {
        
        init: function() {
            this.initTableTogglePin();
        },
        
        /**
         * Initialize table toggle pin button
         */
        initTableTogglePin: function() {
            $(document).on('click', '.blog-table-toggle-pin', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var blogId = $btn.data('blog-id');
                
                $btn.prop('disabled', true);
                
                $.post(blogAdmin.ajaxUrl, {
                    action: 'blog_toggle_pin',
                    nonce: blogAdmin.nonce,
                    blog_id: blogId
                })
                .done(function(response) {
                    if (response.success) {
                        // 刷新页面以更新排序
                        location.reload();
                    }
                })
                .fail(function() {
                    console.log('置顶操作失败');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
            });
        }
    };
    
    $(document).ready(function() {
        BlogAdmin.init();
    });
    
})(jQuery);
