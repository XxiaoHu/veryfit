jQuery(document).ready(function($) {
    var currentPage = 1;
    var currentFilters = {
        search: '',
        language: '',
        dateFrom: '',
        dateTo: ''
    };

    // 初始化日期选择器
    $('#feryfit-contact-date-from, #feryfit-contact-date-to').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+0',
        maxDate: 0,
        showButtonPanel: false,
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.css({
                    fontSize: '12px'
                });
            }, 0);
        },
        onSelect: function(selectedDate) {
            if ($(this).attr('id') === 'feryfit-contact-date-from') {
                $('#feryfit-contact-date-to').datepicker('option', 'minDate', selectedDate);
            } else {
                $('#feryfit-contact-date-from').datepicker('option', 'maxDate', selectedDate);
            }
        }
    }).attr('readonly', 'readonly');

    // 加载语言列表
    function loadLanguages() {
        $.ajax({
            url: feryfitContact.rest_url + 'feryfit/v1/contact-languages',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', feryfitContact.nonce);
            },
            success: function(response) {
                if (response.languages && response.languages.length > 0) {
                    var $select = $('#feryfit-contact-language-filter');
                    $.each(response.languages, function(index, lang) {
                        if (lang) {
                            $select.append('<option value="' + escapeHtml(lang) + '">' + escapeHtml(lang) + '</option>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('加载语言列表失败:', error);
            }
        });
    }

    function loadMessages(page, filters) {
        $('#feryfit-contact-container').html('<div class="loading">加载中...</div>');

        var url = feryfitContact.rest_url + 'feryfit/v1/contact-messages?page=' + page;

        if (filters.search) {
            url += '&search=' + encodeURIComponent(filters.search);
        }
        if (filters.language) {
            url += '&language=' + encodeURIComponent(filters.language);
        }
        if (filters.dateFrom) {
            url += '&date_from=' + encodeURIComponent(filters.dateFrom);
        }
        if (filters.dateTo) {
            url += '&date_to=' + encodeURIComponent(filters.dateTo);
        }

        console.log('请求URL:', url);
        console.log('筛选条件:', filters);

        $.ajax({
            url: url,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', feryfitContact.nonce);
            },
            success: function(response) {
                console.log('返回数据:', response);
                currentPage = page;
                currentFilters = filters;
                renderMessages(response);
            },
            error: function(xhr, status, error) {
                console.error('加载失败:', error, xhr.responseText);
                $('#feryfit-contact-container').html('<div class="empty-message">加载失败: ' + error + '</div>');
            }
        });
    }

    function renderMessages(data) {
        if (!data.data || data.data.length === 0) {
            var message = '<div class="empty-message">暂无联系消息。</div>';
            if (currentFilters.search || currentFilters.language || currentFilters.dateFrom || currentFilters.dateTo) {
                message = '<div class="empty-message">没有符合筛选条件的记录。请尝试调整筛选条件。</div>';
            }
            $('#feryfit-contact-container').html(message);
            return;
        }

        var html = '<div class="result-info">共找到 ' + data.total + ' 条记录</div>';
        html += '<table class="feryfit-contact-table">';
        html += '<thead><tr>';
        html += '<th>ID</th>';
        html += '<th>邮箱</th>';
        html += '<th>姓名</th>';
        html += '<th>消息</th>';
        html += '<th>语言</th>';
        html += '<th>创建时间</th>';
        html += '<th>操作</th>';
        html += '</tr></thead><tbody>';

        $.each(data.data, function(index, item) {
            html += '<tr data-id="' + item.id + '">';
            html += '<td>' + item.id + '</td>';
            html += '<td><a href="mailto:' + escapeHtml(item.email) + '">' + escapeHtml(item.email) + '</a></td>';
            html += '<td>' + escapeHtml(item.name) + '</td>';
            html += '<td><span class="message-preview" title="' + escapeHtml(item.message) + '">' + escapeHtml(item.message) + '</span></td>';
            html += '<td>' + escapeHtml(item.language) + '</td>';
            html += '<td>' + formatDate(item.created_at) + '</td>';
            html += '<td><button class="btn-delete" data-id="' + item.id + '">删除</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';

        if (data.pages > 1) {
            html += '<div class="feryfit-contact-pagination">';
            if (currentPage > 1) {
                html += '<a href="#" data-page="' + (currentPage - 1) + '">上一页</a>';
            }

            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(data.pages, currentPage + 2);

            if (startPage > 1) {
                html += '<a href="#" data-page="1">1</a>';
                if (startPage > 2) {
                    html += '<span class="page-dots">...</span>';
                }
            }

            for (var i = startPage; i <= endPage; i++) {
                html += '<a href="#" data-page="' + i + '"' + (i === currentPage ? ' class="current"' : '') + '>' + i + '</a>';
            }

            if (endPage < data.pages) {
                if (endPage < data.pages - 1) {
                    html += '<span class="page-dots">...</span>';
                }
                html += '<a href="#" data-page="' + data.pages + '">' + data.pages + '</a>';
            }

            if (currentPage < data.pages) {
                html += '<a href="#" data-page="' + (currentPage + 1) + '">下一页</a>';
            }
            html += '</div>';
        }

        $('#feryfit-contact-container').html(html);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
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
        return String(str).replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;');
    }

    function getFilters() {
        return {
            search: $('#feryfit-contact-search-input').val().trim(),
            language: $('#feryfit-contact-language-filter').val(),
            dateFrom: $('#feryfit-contact-date-from').val(),
            dateTo: $('#feryfit-contact-date-to').val()
        };
    }

    // 导出数据
    $('#feryfit-contact-export-btn').on('click', function() {
        var filters = getFilters();

        // 检查是否有数据
        if ($('.feryfit-contact-table tbody tr').length === 0) {
            alert('没有可导出的数据');
            return;
        }

        var url = feryfitContact.rest_url + 'feryfit/v1/contact-messages/export?_wpnonce=' + feryfitContact.nonce;

        if (filters.search) {
            url += '&search=' + encodeURIComponent(filters.search);
        }
        if (filters.language) {
            url += '&language=' + encodeURIComponent(filters.language);
        }
        if (filters.dateFrom) {
            url += '&date_from=' + encodeURIComponent(filters.dateFrom);
        }
        if (filters.dateTo) {
            url += '&date_to=' + encodeURIComponent(filters.dateTo);
        }

        console.log('导出URL:', url);
        window.location.href = url;
    });

    $(document).on('click', '.feryfit-contact-pagination a', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page && page !== currentPage) {
            loadMessages(page, currentFilters);
        }
    });

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (confirm('确定要删除这条消息吗？')) {
            $.ajax({
                url: feryfitContact.rest_url + 'feryfit/v1/contact-messages/' + id,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', feryfitContact.nonce);
                },
                success: function() {
                    loadMessages(currentPage, currentFilters);
                },
                error: function() {
                    alert('删除失败。');
                }
            });
        }
    });

    $('#feryfit-contact-search-btn').on('click', function() {
        currentPage = 1;
        var filters = getFilters();
        console.log('开始筛选，条件:', filters);
        loadMessages(1, filters);
    });

    $('#feryfit-contact-reset-btn').on('click', function() {
        $('#feryfit-contact-search-input').val('');
        $('#feryfit-contact-language-filter').val('');
        $('#feryfit-contact-date-from').val('');
        $('#feryfit-contact-date-to').val('');
        $('#feryfit-contact-date-from').datepicker('option', 'maxDate', 0);
        $('#feryfit-contact-date-to').datepicker('option', 'minDate', null);
        currentPage = 1;
        loadMessages(1, {
            search: '',
            language: '',
            dateFrom: '',
            dateTo: ''
        });
    });

    $('#feryfit-contact-search-input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#feryfit-contact-search-btn').click();
        }
    });

    // 初始化
    loadLanguages();
    loadMessages(1, currentFilters);
});
