jQuery(document).ready(function($) {
	var currentPage = 1;
	var currentSearch = '';

	function loadMessages(page, search) {
		$('#feryfit-contact-container').html('<div class="loading">加载中...</div>');

		$.ajax({
			url: feryfitContact.rest_url + 'feryfit/v1/contact-messages',
			type: 'GET',
			data: {
				page: page,
				per_page: 10,
				search: search
			},
			beforeSend: function(xhr) {
				xhr.setRequestHeader('X-WP-Nonce', feryfitContact.nonce);
			},
			success: function(response) {
				if (response.data && response.data.length > 0) {
					renderTable(response);
				} else {
					$('#feryfit-contact-container').html('<div class="empty-message">暂无联系消息</div>');
				}
			},
			error: function() {
				$('#feryfit-contact-container').html('<div class="empty-message">加载失败，请刷新重试</div>');
			}
		});
	}

	function renderTable(response) {
		var html = '<table class="feryfit-contact-table">';
		html += '<thead><tr><th>ID</th><th>邮箱</th><th>姓名</th><th>消息</th><th>提交时间</th><th>操作</th></tr></thead>';
		html += '<tbody>';

		$.each(response.data, function(index, item) {
			html += '<tr data-id="' + item.id + '">';
			html += '<td>' + item.id + '</td>';
			html += '<td><a href="mailto:' + item.email + '">' + item.email + '</a></td>';
			html += '<td>' + item.name + '</td>';
			html += '<td><span class="message-preview" title="' + item.message + '">' + item.message + '</span></td>';
			html += '<td>' + formatDate(item.created_at) + '</td>';
			html += '<td><button class="btn-delete" data-id="' + item.id + '">删除</button></td>';
			html += '</tr>';
		});

		html += '</tbody></table>';

		html += '<div class="feryfit-contact-pagination">';
		if (response.current_page > 1) {
			html += '<a href="#" data-page="' + (response.current_page - 1) + '">上一页</a>';
		}
		for (var i = 1; i <= response.pages; i++) {
			if (i == response.current_page) {
				html += '<a href="#" data-page="' + i + '" class="current">' + i + '</a>';
			} else {
				html += '<a href="#" data-page="' + i + '">' + i + '</a>';
			}
		}
		if (response.current_page < response.pages) {
			html += '<a href="#" data-page="' + (response.current_page + 1) + '">下一页</a>';
		}
		html += '</div>';

		$('#feryfit-contact-container').html(html);
	}

	function formatDate(dateString) {
		var date = new Date(dateString);
		return date.toLocaleString('zh-CN');
	}

	$('#feryfit-contact-search-btn').click(function() {
		currentSearch = $('#feryfit-contact-search-input').val();
		currentPage = 1;
		loadMessages(currentPage, currentSearch);
	});

	$('#feryfit-contact-reset-btn').click(function() {
		$('#feryfit-contact-search-input').val('');
		currentSearch = '';
		currentPage = 1;
		loadMessages(currentPage, currentSearch);
	});

	$('#feryfit-contact-search-input').keypress(function(e) {
		if (e.which == 13) {
			$('#feryfit-contact-search-btn').click();
		}
	});

	$(document).on('click', '.feryfit-contact-pagination a', function(e) {
		e.preventDefault();
		currentPage = $(this).data('page');
		loadMessages(currentPage, currentSearch);
	});

	$(document).on('click', '.btn-delete', function() {
		var id = $(this).data('id');
		if (confirm('确定要删除这条消息吗？')) {
			$.ajax({
				url: feryfitContact.rest_url + 'feryfit/v1/contact-messages/' + id,
				type: 'DELETE',
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', feryfitContact.nonce);
				},
				success: function(response) {
					if (response.success) {
						loadMessages(currentPage, currentSearch);
					}
				}
			});
		}
	});

	loadMessages(currentPage, currentSearch);
});