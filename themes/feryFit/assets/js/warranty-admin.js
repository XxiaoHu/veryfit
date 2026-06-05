jQuery(document).ready(function($) {
    var currentPage = 1;
    var currentSearch = '';

    function loadApplications(page, search) {
        $('#feryfit-warranty-container').html('<div class="loading">加载中...</div>');

        var url = feryfitWarranty.rest_url + 'feryfit/v1/warranty-applications?page=' + page;
        if (search) {
            url += '&search=' + encodeURIComponent(search);
        }

        $.ajax({
            url: url,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', feryfitWarranty.nonce);
            },
            success: function(response) {
                currentPage = page;
                currentSearch = search;
                renderApplications(response);
            },
            error: function() {
                $('#feryfit-warranty-container').html('<div class="empty-message">加载失败。</div>');
            }
        });
    }

    function renderApplications(data) {
        if (!data.data || data.data.length === 0) {
            $('#feryfit-warranty-container').html('<div class="empty-message">暂无保修申请记录。</div>');
            return;
        }

        var html = '<table class="feryfit-warranty-table">';
        html += '<thead><tr>';
        html += '<th>ID</th>';
        html += '<th>订单号</th>';
        html += '<th>邮箱</th>';
        html += '<th>姓名</th>';
        html += '<th>国家</th>';
        html += '<th>语言</th>';
        html += '<th>评分</th>';
        html += '<th>接收更新</th>';
        html += '<th>创建时间</th>';
        html += '<th>操作</th>';
        html += '</tr></thead><tbody>';

        $.each(data.data, function(index, item) {
            html += '<tr data-id="' + item.id + '">';
            html += '<td>' + item.id + '</td>';
            html += '<td>' + escapeHtml(item.order_number) + '</td>';
            html += '<td><a href="mailto:' + escapeHtml(item.email) + '">' + escapeHtml(item.email) + '</a></td>';
            html += '<td>' + escapeHtml(item.name) + '</td>';
            html += '<td>' + escapeHtml(item.country) + '</td>';
            html += '<td>' + escapeHtml(item.language) + '</td>';
            html += '<td><span class="star-rating">' + getStarRating(item.rating) + '</span></td>';
            html += '<td><span class="' + (item.receive_updates ? 'checkbox-yes' : 'checkbox-no') + '">' + (item.receive_updates ? '是' : '否') + '</span></td>';
            html += '<td>' + formatDate(item.created_at) + '</td>';
            html += '<td><button class="btn-delete" data-id="' + item.id + '">删除</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';

        if (data.pages > 1) {
            html += '<div class="feryfit-warranty-pagination">';
            if (currentPage > 1) {
                html += '<a href="#" data-page="' + (currentPage - 1) + '">上一页</a>';
            }
            for (var i = 1; i <= data.pages; i++) {
                html += '<a href="#" data-page="' + i + '"' + (i === currentPage ? ' class="current"' : '') + '>' + i + '</a>';
            }
            if (currentPage < data.pages) {
                html += '<a href="#" data-page="' + (currentPage + 1) + '">下一页</a>';
            }
            html += '</div>';
        }

        $('#feryfit-warranty-container').html(html);
    }

    function getStarRating(rating) {
        var stars = '';
        for (var i = 1; i <= 5; i++) {
            stars += (i <= rating) ? '★' : '☆';
        }
        return stars;
    }

    function formatDate(dateStr) {
        var date = new Date(dateStr);
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        var hours = String(date.getHours()).padStart(2, '0');
        var minutes = String(date.getMinutes()).padStart(2, '0');
        var seconds = String(date.getSeconds()).padStart(2, '0');
        return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;');
    }

    $(document).on('click', '.feryfit-warranty-pagination a', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page && page !== currentPage) {
            loadApplications(page, currentSearch);
        }
    });

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (confirm('确定要删除这条保修申请吗？')) {
            $.ajax({
                url: feryfitWarranty.rest_url + 'feryfit/v1/warranty-applications/' + id,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', feryfitWarranty.nonce);
                },
                success: function() {
                    $('tr[data-id="' + id + '"]').remove();
                    if ($('.feryfit-warranty-table tbody tr').length === 0) {
                        $('#feryfit-warranty-container').html('<div class="empty-message">暂无保修申请记录。</div>');
                    }
                },
                error: function() {
                    alert('删除失败。');
                }
            });
        }
    });

    $('#feryfit-search-btn').on('click', function() {
        var search = $('#feryfit-search-input').val().trim();
        currentPage = 1;
        loadApplications(1, search);
    });

    $('#feryfit-reset-btn').on('click', function() {
        $('#feryfit-search-input').val('');
        currentSearch = '';
        currentPage = 1;
        loadApplications(1, '');
    });

    $('#feryfit-search-input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#feryfit-search-btn').click();
        }
    });

    loadApplications(1, '');
});