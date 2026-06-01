function initContactCards() {
	const cards = document.querySelectorAll('.wp-block-feryfit-contact-cards .contact-card');

	if (!cards.length) {
		return;
	}

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
					card.setAttribute('href', data.whatsapp);
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
