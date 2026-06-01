/**
 * FAQs Admin JavaScript
 */
(function($) {
    'use strict';
    
    var FAQsAdmin = {
        
        init: function() {
            this.initTableTogglePin();
        },
        
        /**
         * Initialize table toggle pin button
         */
        initTableTogglePin: function() {
            $(document).on('click', '.faq-table-toggle-pin', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var faqId = $btn.data('faq-id');
                
                $btn.prop('disabled', true);
                
                $.post(faqsAdmin.ajaxUrl, {
                    action: 'faq_toggle_pin',
                    nonce: faqsAdmin.nonce,
                    faq_id: faqId
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
        FAQsAdmin.init();
    });
    
})(jQuery);