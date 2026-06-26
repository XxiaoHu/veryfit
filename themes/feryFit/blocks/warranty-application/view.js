document.addEventListener('DOMContentLoaded', function () {
	const form = document.getElementById('warranty-application-form');
	if (!form) return;

	let lastOrderNumber = '';

	const orderHelpLink = form.querySelector('.order-help-link');
	const orderHelpTooltip = form.querySelector('#order-help-tooltip');
	const orderInput = form.querySelector('input[name="order_number"]');

	if (orderHelpLink && orderHelpTooltip) {
		function positionTooltip() {
			const isMobile = window.innerWidth <= 768;
			let targetElement = orderHelpLink;

			if (isMobile && orderInput) {
				targetElement = orderInput;
			}

			const rect = targetElement.getBoundingClientRect();
			const centerX = rect.left + rect.width / 2;
			const top = rect.top;

			orderHelpTooltip.style.right = 'auto';
			orderHelpTooltip.style.left = centerX + 'px';
			orderHelpTooltip.style.top = top + 'px';
			orderHelpTooltip.style.transform = 'translateX(-50%) translateY(-100%) translateY(-8px)';
		}

		orderHelpLink.addEventListener('click', function (e) {
			e.preventDefault();
			if (orderHelpTooltip.classList.contains('visible')) {
				orderHelpTooltip.classList.remove('visible');
			} else {
				positionTooltip();
				orderHelpTooltip.classList.add('visible');
			}
		});

		document.addEventListener('click', function (e) {
			if (!orderHelpLink.contains(e.target) && !orderHelpTooltip.contains(e.target)) {
				orderHelpTooltip.classList.remove('visible');
			}
		});

		window.addEventListener('resize', function () {
			if (orderHelpTooltip.classList.contains('visible')) {
				positionTooltip();
			}
		});
	}

	const stars = form.querySelectorAll('.star');
	const ratingValue = form.querySelector('#rating-value');

	function updateStars(rating) {
		stars.forEach((star, index) => {
			const svg = star.querySelector('svg path');
			if (svg) {
				if (index < rating) {
					svg.setAttribute('fill', '#CD3D1C');
				} else {
					svg.setAttribute('fill', '#D9D9D9');
				}
			}
		});
	}

	function getSafeHttpUrl(value) {
		try {
			const url = new URL(value, window.location.origin);
			if (url.protocol === 'http:' || url.protocol === 'https:') {
				return url.href;
			}
		} catch (e) {}

		return '';
	}

	stars.forEach(star => {
		star.addEventListener('click', function () {
			const rating = parseInt(this.dataset.rating);
			ratingValue.value = rating;
			updateStars(rating);
		});
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		const formData = new FormData(form);

		lastOrderNumber = formData.get('order_number') || '';

		// 获取当前页面语言
		const currentLanguage = document.documentElement.lang || 'en';
		formData.append('language', currentLanguage);

		const submitButton = form.querySelector('.submit-btn');

		if (submitButton.classList.contains('loading')) {
			return;
		}

		submitButton.classList.add('loading');
		submitButton.disabled = true;
		const originalText = submitButton.textContent;
		submitButton.innerHTML = '<span class="loading-spinner"></span> Loading...';

		fetch('/wp-json/feryfit/v1/warranty-nonce', {
			method: 'GET',
			credentials: 'same-origin'
		})
		.then(response => response.json())
		.then(data => {
			if (data.nonce) {
				return fetch('/wp-json/feryfit/v1/submit-warranty', {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
					headers: {
						'X-FeryFit-Nonce': data.nonce
					}
				});
			} else {
				throw new Error('Failed to get nonce');
			}
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					form.reset();
					updateStars(0);
					ratingValue.value = '';
					showSuccessModal();
				} else {
					showErrorModal(data.message);
				}
			})
			.catch(error => {
				showErrorModal();
			})
			.finally(() => {
				submitButton.classList.remove('loading');
				submitButton.disabled = false;
				submitButton.textContent = originalText;
			});
	});

	function showSuccessModal() {
		const warrantyBlock = document.querySelector('.warranty-application');
		const modalTitle = warrantyBlock?.dataset.modalTitle || 'Application Submitted';
		const modalMessage = warrantyBlock?.dataset.modalMessage || 'Your request is in progress.';
		const modalDescription = warrantyBlock?.dataset.modalDescription || 'To complete activation and ensure everything is set up correctly, you may continue with our support team below.';
		const modalWhatsapp = warrantyBlock?.dataset.modalWhatsapp || 'Chat on WhatsApp';
		const modalFacebook = warrantyBlock?.dataset.modalFacebook || 'Message us on Facebook';
		const modalBenefits = warrantyBlock?.dataset.modalBenefits || 'Warranty activation benefits apply once activation is completed.';

		let images = [];
		try {
			images = JSON.parse(warrantyBlock?.dataset.productImages || '[]');
		} catch (e) {
			images = [
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=smartwatch%20with%20multiple%20watch%20bands%20rose%20gold%20black%20silver%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=leopard%20print%20elastic%20watch%20band%20fashion%20accessory%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=blue%20marble%20pattern%20watch%20band%20stylish%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=smartwatch%20with%20metallic%20watch%20bands%20black%20gold%20silver%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=rose%20gold%20metal%20watch%20band%20elegant%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=smartwatch%20with%20pearl%20beaded%20band%20luxury%20elegant%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=square%20smartwatch%20with%20digital%20display%20black%20band%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=collection%20of%20silicone%20watch%20bands%20various%20colors%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=row%20of%20colorful%20watch%20bands%20pastel%20colors%20minimal%20background&image_size=square_hd',
				'https://neeko-copilot.bytedance.net/api/text2image?prompt=woman%20hand%20wearing%20smartwatch%20with%20beaded%20band%20elegant%20minimal%20background&image_size=square_hd'
			];
		}

		const modal = document.createElement('div');
		modal.className = 'success-modal';
		modal.innerHTML = `
			<div class="success-modal-overlay"></div>
			<div class="success-modal-content">
				<div class="success-modal-header">
					<span class="success-checkmark">
					<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
  <path d="M16.0001 29.3333C19.6819 29.3333 23.0153 27.8409 25.4281 25.428C27.841 23.0152 29.3334 19.6818 29.3334 16C29.3334 12.3181 27.841 8.98476 25.4281 6.57187C23.0153 4.15901 19.6819 2.66663 16.0001 2.66663C12.3182 2.66663 8.98488 4.15901 6.57199 6.57187C4.15913 8.98476 2.66675 12.3181 2.66675 16C2.66675 19.6818 4.15913 23.0152 6.57199 25.428C8.98488 27.8409 12.3182 29.3333 16.0001 29.3333Z" stroke="white" stroke-width="3" stroke-linejoin="round"/>
  <path d="M10.6667 16L14.6667 20L22.6667 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg></span>
					<span class="success-title"></span>
					<button class="success-modal-close">
					<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
  <path d="M8.75 8.75L21.25 21.25" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8.75 21.25L21.25 8.75" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
					</button>
				</div>
				<div class="success-modal-body">
					<p class="success-message"></p>
					<p class="success-description"></p>
					<div class="support-options">
						<div class="support-option" data-contact-type="whatsapp">
							<div class="support-icon whatsapp-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
  <path d="M43.9921 38.874C44.3131 38.874 45.5101 39.414 47.5831 40.494C49.6591 41.574 50.7571 42.225 50.8801 42.444C50.9281 42.567 50.9551 42.753 50.9551 42.999C50.9551 43.809 50.7451 44.739 50.3251 45.798C49.9351 46.755 49.0651 47.559 47.7121 48.21C46.3621 48.861 45.1111 49.188 43.9561 49.185C42.5551 49.185 40.2241 48.423 36.9571 46.905C34.6227 45.8482 32.5009 44.374 30.6961 42.555C28.9261 40.764 27.1111 38.493 25.2451 35.745C23.4751 33.114 22.6051 30.735 22.6291 28.599V28.302C22.7041 26.067 23.6131 24.129 25.3531 22.482C25.9441 21.942 26.5831 21.672 27.2731 21.672C27.4171 21.672 27.6391 21.693 27.9331 21.732C28.2271 21.768 28.4611 21.786 28.6321 21.786C29.0971 21.786 29.4241 21.864 29.6071 22.026C29.7931 22.185 29.9821 22.521 30.1771 23.037C30.3751 23.529 30.7801 24.609 31.3921 26.277C32.0071 27.948 32.3131 28.866 32.3131 29.037C32.3131 29.556 31.8901 30.261 31.0441 31.158C30.1951 32.055 29.7721 32.625 29.7721 32.868C29.7721 33.042 29.8321 33.228 29.9551 33.423C30.7891 35.214 32.0431 36.897 33.7111 38.466C35.0881 39.768 36.9391 41.007 39.2731 42.186C39.5155 42.3421 39.795 42.4311 40.0831 42.444C40.4521 42.444 41.1151 41.85 42.0721 40.659C43.0291 39.468 43.6681 38.874 43.9861 38.874H43.9921ZM36.5161 58.392C39.6007 58.3994 42.6539 57.7724 45.4861 56.55C48.2392 55.3925 50.7446 53.7172 52.8661 51.615C54.9697 49.493 56.6461 46.9866 57.8041 44.232C59.0265 41.3999 59.6534 38.3467 59.6461 35.262C59.653 32.1784 59.026 29.1262 57.8041 26.295C56.6466 23.5418 54.9713 21.0365 52.8691 18.915C50.7461 16.8111 48.2386 15.1347 45.4831 13.977C42.6519 12.755 39.5997 12.1281 36.5161 12.135C33.4324 12.1281 30.3803 12.755 27.5491 13.977C24.7948 15.1342 22.2884 16.8095 20.1661 18.912C18.0635 21.0344 16.3882 23.5408 15.2311 26.295C14.0087 29.1271 13.3817 32.1804 13.3891 35.265C13.3891 40.248 14.8591 44.766 17.8081 48.816L14.8981 57.396L23.8081 54.561C27.5663 57.0787 31.9925 58.413 36.5161 58.392ZM36.5161 7.49402C40.2721 7.49402 43.8661 8.23202 47.2891 9.70202C50.5942 11.0943 53.6015 13.1077 56.1481 15.633C58.6724 18.1797 60.6848 21.1871 62.0761 24.492C63.5442 27.8923 64.2968 31.5583 64.2871 35.262C64.2871 39.021 63.5491 42.612 62.0761 46.035C60.6848 49.34 58.6724 52.3473 56.1481 54.894C53.6016 57.4194 50.5942 59.4328 47.2891 60.825C43.8886 62.2921 40.2225 63.0437 36.5191 63.033C31.8138 63.0552 27.1825 61.8628 23.0731 59.571L7.71606 64.506L12.7261 49.59C10.0908 45.2791 8.71301 40.3175 8.74806 35.265C8.74806 31.506 9.48306 27.915 10.9561 24.492C12.3479 21.1858 14.3614 18.1774 16.8871 15.63C19.432 13.1063 22.4373 11.094 25.7401 9.70202C29.1415 8.23452 32.8086 7.48293 36.5131 7.49402H36.5161Z" fill="#999999"/>
</svg>
							</div>
							<span class="support-text" data-support-text="whatsapp"></span>
						</div>
						<div class="support-option" data-contact-type="facebook">
							<div class="support-icon facebook-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
  <g clip-path="url(#clip0_133_486)">
    <path d="M47.2417 0H2.7583C1.23545 0 0 1.2354 0 2.7583V47.2417C0 48.7666 1.2354 50 2.7583 50H26.7042V30.6375H20.1854V23.0938H26.7042V17.5208C26.7042 11.0646 30.65 7.5521 36.4104 7.5521C39.1708 7.5521 41.5438 7.75415 42.2354 7.84585V14.5958H38.2334C35.1084 14.5958 34.5 16.0979 34.5 18.2854V23.1H41.9667L40.9979 30.6625H34.5V50H47.2396C48.7667 50 50 48.7667 50 47.2417V2.7583C50 1.23545 48.7667 0 47.2417 0Z" fill="#999999"/>
  </g>
  <defs>
    <clipPath id="clip0_133_486">
      <rect width="50" height="50" fill="white"/>
    </clipPath>
  </defs>
</svg>
							</div>
							<span class="support-text" data-support-text="facebook"></span>
						</div>
					</div>
					<p class="benefits-note"></p>
					<div class="product-showcase">
						<div class="product-column product-column-left">
							<div class="product-item product-item-1"><img data-image-index="0" alt="Product 1"></div>
							<div class="product-item product-item-2"><img data-image-index="1" alt="Product 2"></div>
							<div class="product-item product-item-3"><img data-image-index="2" alt="Product 3"></div>
						</div>
						<div class="product-column product-column-center">
							<div class="product-item product-item-4"><img data-image-index="3" alt="Product 4"></div>
							<div class="product-item product-item-5"><img data-image-index="4" alt="Product 5"></div>
							<div class="product-item product-item-6"><img data-image-index="5" alt="Product 6"></div>
							<div class="product-item product-item-7"><img data-image-index="6" alt="Product 7"></div>
						</div>
						<div class="product-column product-column-right">
							<div class="product-item product-item-8"><img data-image-index="7" alt="Product 8"></div>
							<div class="product-item product-item-9"><img data-image-index="8" alt="Product 9"></div>
							<div class="product-item product-item-10"><img data-image-index="9" alt="Product 10"></div>
						</div>
					</div>
				</div>
			</div>
		`;
		modal.querySelector('.success-title').textContent = modalTitle;
		modal.querySelector('.success-message').textContent = modalMessage;
		modal.querySelector('.success-description').textContent = modalDescription;
		modal.querySelector('[data-support-text="whatsapp"]').textContent = modalWhatsapp;
		modal.querySelector('[data-support-text="facebook"]').textContent = modalFacebook;
		modal.querySelector('.benefits-note').textContent = modalBenefits;
		modal.querySelectorAll('[data-image-index]').forEach(function (image) {
			const imageUrl = getSafeHttpUrl(images[parseInt(image.dataset.imageIndex, 10)]);
			if (imageUrl) {
				image.src = imageUrl;
			} else {
				image.closest('.product-item')?.remove();
			}
		});
		document.body.appendChild(modal);
		document.body.style.overflow = 'hidden';

		function closeModal() {
			modal.remove();
			document.body.style.overflow = '';
		}

		const overlay = modal.querySelector('.success-modal-overlay');
		const closeButton = modal.querySelector('.success-modal-close');

		if (overlay) {
			overlay.addEventListener('click', closeModal);
		}

		if (closeButton) {
			closeButton.addEventListener('click', closeModal);
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeModal();
			}
		}, { once: true });

		const currentLang = document.documentElement.lang || 'en';
		const pageTitle = document.title || '';

		fetch('/wp-json/feryfit/v1/customer-service')
			.then(function (response) {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(function (data) {
				const supportOptions = modal.querySelectorAll('.support-option');
				supportOptions.forEach(function (option) {
					const type = option.getAttribute('data-contact-type');
					if (type === 'whatsapp' && data.whatsapp) {
						let whatsappUrl = getSafeHttpUrl(data.whatsapp);
						if (!whatsappUrl) {
							return;
						}
						const separator = whatsappUrl.indexOf('?') !== -1 ? '&' : '?';
						const messageParts = [];
						if (pageTitle) {
							messageParts.push('Page:' + pageTitle);
						}
						if (currentLang) {
							messageParts.push('lang:' + currentLang);
						}
						if (lastOrderNumber) {
							messageParts.push('Order:' + lastOrderNumber);
						}
						const messageText = messageParts.join('\n');
						whatsappUrl += separator + 'text=' + encodeURIComponent(messageText);
						option.addEventListener('click', function () {
							window.open(whatsappUrl, '_blank');
						});
					} else if (type === 'facebook' && data.facebook) {
						const facebookUrl = getSafeHttpUrl(data.facebook);
						if (!facebookUrl) {
							return;
						}
						option.addEventListener('click', function () {
							window.open(facebookUrl, '_blank');
						});
					}
				});
			})
			.catch(function (error) {
				console.error('Warranty modal: Failed to load contact data:', error);
			});
	}

	function showErrorModal(customMessage) {
		const warrantyBlock = document.querySelector('.warranty-application');
		const modalTitle = warrantyBlock?.dataset.errorModalTitle || '提交失败';
		const modalMessage = customMessage || warrantyBlock?.dataset.errorModalMessage || '抱歉，提交失败，请稍后重试或联系客服。';

		const modal = document.createElement('div');
		modal.className = 'error-modal';
		modal.innerHTML = `
			<div class="error-modal-overlay"></div>
			<div class="error-modal-content">
				<button class="error-modal-close">
					<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
  <path d="M8.75 8.75L21.25 21.25" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8.75 21.25L21.25 8.75" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
				</button>
				<h3 class="error-modal-title"></h3>
				<p class="error-modal-message"></p>
			</div>
		`;
		modal.querySelector('.error-modal-title').textContent = modalTitle;
		modal.querySelector('.error-modal-message').textContent = modalMessage;
		document.body.appendChild(modal);
		document.body.style.overflow = 'hidden';

		function closeModal() {
			modal.remove();
			document.body.style.overflow = '';
		}

		const overlay = modal.querySelector('.error-modal-overlay');
		const closeButton = modal.querySelector('.error-modal-close');

		if (overlay) {
			overlay.addEventListener('click', closeModal);
		}

		if (closeButton) {
			closeButton.addEventListener('click', closeModal);
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeModal();
			}
		}, { once: true });
	}
});
