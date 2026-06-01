/**
 * Video Admin JavaScript
 */

jQuery(document).ready(function($) {
    // Handle delete video
    $('.video-delete-btn').on('click', function(e) {
        e.preventDefault();
        
        var post_id = $(this).data('post-id');
        
        if (confirm(videoAdmin.delete_confirm)) {
            $.ajax({
                url: videoAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'video_delete',
                    post_id: post_id,
                    nonce: videoAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        window.location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('操作失败，请重试');
                }
            });
        }
    });
    
    // Handle toggle status
    $('.video-toggle-status').on('click', function(e) {
        e.preventDefault();
        
        var post_id = $(this).data('post-id');
        var $this = $(this);
        
        $.ajax({
            url: videoAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'video_toggle_status',
                post_id: post_id,
                nonce: videoAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.status === 'publish') {
                        $this.text('发布');
                    } else {
                        $this.text('草稿');
                    }
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('操作失败，请重试');
            }
        });
    });
    
    // Handle toggle pin
    $('.video-toggle-pin').on('click', function(e) {
        e.preventDefault();
        
        var post_id = $(this).data('post-id');
        var $this = $(this);
        
        $.ajax({
            url: videoAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'video_toggle_pin',
                post_id: post_id,
                nonce: videoAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.pinned) {
                        $this.text('取消置顶');
                    } else {
                        $this.text('置顶');
                    }
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('操作失败，请重试');
            }
        });
    });
    
});
