function initContactCards() {
	const cards = document.querySelectorAll('.wp-block-feryfit-contact-cards .contact-card');

	if (!cards.length) {
		return;
	}
	// 获取当前页面语言（从 html 标签的 lang 属性获取）
	var currentLang = document.documentElement.lang || 'en';
	// 直接从 title 标签获取页面标题
	var pageTitle = document.title || '';

	fetch('/wp-json/feryfit/v1/customer-service')
		.then(function (response) {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(function (data) {
			cards.forEach(function (card) {
				var type = card.getAttribute('data-contact-type');
				if (type === 'whatsapp' && data.whatsapp) {
					// 添加页面信息到 WhatsApp 链接
					var whatsappUrl = data.whatsapp;
					var separator = whatsappUrl.indexOf('?') !== -1 ? '&' : '?';
					// 构建消息文本：包含页面标题、语言和URL
					var messageParts = [];
					if (pageTitle) {
						messageParts.push('Page:'+pageTitle);
					}
					if (currentLang) {
						messageParts.push('lang:'+currentLang);
					}
					var messageText = messageParts.join(' ');
					whatsappUrl += separator + 'text=' + encodeURIComponent(messageText);
					card.setAttribute('href', whatsappUrl);
				} else if (type === 'email' && data.email) {
					card.setAttribute('href', 'mailto:' + data.email);
				} else if (type === 'facebook' && data.facebook) {
					card.setAttribute('href', data.facebook);
				}
			});
		})
		.catch(function (error) {
			console.error('Contact cards: Failed to load contact data:', error);
		});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initContactCards);
} else {
	initContactCards();
}
