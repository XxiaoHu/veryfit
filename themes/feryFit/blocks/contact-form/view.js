document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('contact-form');
	if (!form) return;

	// 自动填充语言字段
	const languageInput = document.getElementById('contact-form-language');
	if (languageInput) {
		const currentLanguage = document.documentElement.lang || 'en-US';
		languageInput.value = currentLanguage;
		console.log('Contact form language set to:', currentLanguage);
	}

	const submitButton = form.querySelector('button[type="submit"]');
	let originalButtonText = '';

	form.addEventListener('submit', function(e) {
		e.preventDefault();
		const formData = new FormData(form);

		if (submitButton && submitButton.classList.contains('loading')) {
			return;
		}

		if (submitButton) {
			originalButtonText = submitButton.textContent;
			submitButton.disabled = true;
			submitButton.classList.add('loading');
			submitButton.innerHTML = '<span class="loading-spinner"></span> Loading...';
		}

		fetch('/wp-json/feryfit/v1/contact-nonce', {
			method: 'GET',
			credentials: 'same-origin'
		})
		.then(response => response.json())
		.then(nonceData => {
			if (!nonceData.nonce) {
				throw new Error('Failed to get contact nonce');
			}

			return fetch('/wp-json/feryfit/v1/submit-contact', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
				headers: {
					'X-FeryFit-Nonce': nonceData.nonce
				}
			});
		})
		.then(response => response.json())
		.then(data => {
			if (submitButton) {
				submitButton.disabled = false;
				submitButton.classList.remove('loading');
				submitButton.innerHTML = originalButtonText;
			}

			if (data.success) {
				form.reset();

				// 重新设置语言字段
				if (languageInput) {
					const currentLanguage = document.documentElement.lang || 'en-US';
					languageInput.value = currentLanguage;
				}

				if (submitButton) {
					submitButton.style.display = 'none';
					const successElement = document.createElement('div');
					successElement.className = 'contact-form__success';
					successElement.id = 'contact-form-success';
					successElement.innerHTML = `
						<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
							<circle cx="16" cy="16" r="14" stroke="#22C55E" stroke-width="3" stroke-linejoin="round"/>
							<path d="M10 16L14 20L24 10" stroke="#22C55E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					`;
					const successText = document.createElement('span');
					successText.textContent = form.getAttribute('data-success-message') || 'Submission Successful';
					successElement.appendChild(successText);
					submitButton.insertAdjacentElement('afterend', successElement);

					setTimeout(() => {
						const successElement = document.getElementById('contact-form-success');
						if (successElement) {
							successElement.remove();
						}
						submitButton.style.display = '';
					}, 3000);
				}
			} else {
				showErrorModal(data.message);
			}
		})
		.catch(error => {
			if (submitButton) {
				submitButton.disabled = false;
				submitButton.classList.remove('loading');
				submitButton.innerHTML = originalButtonText;
			}
			showErrorModal();
		});
	});

	function showErrorModal(customMessage) {
		const modalTitle = form.getAttribute('data-error-title') || 'Submission Failed';
		const modalMessage = customMessage || form.getAttribute('data-error-message') || 'Failed to submit. Please try again later.';

		const modal = document.createElement('div');
		modal.className = 'contact-form__error-modal';
		modal.innerHTML = `
			<div class="contact-form__error-modal-overlay"></div>
			<div class="contact-form__error-modal-content">
				<button class="contact-form__error-modal-close">
					<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
						<path d="M8.75 8.75L21.25 21.25" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M8.75 21.25L21.25 8.75" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<h3 class="contact-form__error-modal-title"></h3>
				<p class="contact-form__error-modal-message"></p>
			</div>
		`;
		modal.querySelector('.contact-form__error-modal-title').textContent = modalTitle;
		modal.querySelector('.contact-form__error-modal-message').textContent = modalMessage;
		document.body.appendChild(modal);
		document.body.style.overflow = 'hidden';

		function closeModal() {
			modal.remove();
			document.body.style.overflow = '';
		}

		const overlay = modal.querySelector('.contact-form__error-modal-overlay');
		const closeButton = modal.querySelector('.contact-form__error-modal-close');

		if (overlay) {
			overlay.addEventListener('click', closeModal);
		}

		if (closeButton) {
			closeButton.addEventListener('click', closeModal);
		}

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeModal();
			}
		}, { once: true });
	}
});
