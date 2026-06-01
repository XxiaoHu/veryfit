document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('contact-form');
	if (!form) return;

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

		fetch('/wp-json/feryfit/v1/submit-contact', {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
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

				if (submitButton) {
					submitButton.style.display = 'none';
					const successHtml = `
						<div class="contact-form__success" id="contact-form-success">
							<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
								<circle cx="16" cy="16" r="14" stroke="#22C55E" stroke-width="3" stroke-linejoin="round"/>
								<path d="M10 16L14 20L24 10" stroke="#22C55E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<span>Submission Successful</span>
						</div>
					`;
					submitButton.insertAdjacentHTML('afterend', successHtml);

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
		const modalTitle = 'Submission Failed';
		const modalMessage = customMessage || 'Failed to submit. Please try again later.';

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
				<h3 class="contact-form__error-modal-title">${modalTitle}</h3>
				<p class="contact-form__error-modal-message">${modalMessage}</p>
			</div>
		`;
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