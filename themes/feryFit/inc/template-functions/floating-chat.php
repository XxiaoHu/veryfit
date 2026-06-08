<?php

/**
 * 客服浮窗组件
 *
 * @package feryfit
 */

/**
 * 渲染客服浮窗
 *
 * @param array $options 浮窗配置选项
 */
function feryfit_floating_chat($options = array())
{
	$defaults = array(
		'whatsapp' => '',
		'email' => '',
		'facebook' => '',
	);

	$options = wp_parse_args($options, $defaults);

	// 获取当前页面语言
	$current_lang = get_locale();
	if (empty($current_lang)) {
		$current_lang = 'en';
	}

	// 获取当前页面标题
	$page_title = wp_title('', false);

	// 处理链接
	$whatsapp_url = '';
	if (!empty($options['whatsapp'])) {
		$whatsapp_url = $options['whatsapp'];
		// 添加页面信息到 WhatsApp 链接
		$separator = strpos($whatsapp_url, '?') !== false ? '&' : '?';
		$message_parts = array();
		if (!empty($page_title)) {
			$message_parts[] = 'Page:' . $page_title;
		}
		if (!empty($current_lang)) {
			$message_parts[] = 'lang:' . $current_lang;
		}
		$message_text = implode(' ', $message_parts);
		if (!empty($message_text)) {
			$whatsapp_url .= $separator . 'text=' . urlencode($message_text);
		}
	}

	// 处理 email 链接
	$email_url = '';
	if (!empty($options['email'])) {
		$email_url = $options['email'];
		if (strpos($email_url, 'mailto:') !== 0) {
			$email_url = 'mailto:' . $email_url;
		}
	}
?>
	<!-- 客服浮窗 -->
	<div class="feryfit-floating-chat">
		<!-- 遮罩层（移动端） -->
		<div class="feryfit-chat-backdrop" id="feryfitChatBackdrop"></div>

		<!-- 移动端抽屉 -->
		<div class="feryfit-chat-drawer" id="feryfitChatDrawer">
			<!-- 抽屉头部 -->
			<div class="feryfit-drawer-header">
				<button class="feryfit-drawer-close" id="feryfitDrawerClose">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M6 18L18 6M6 6l12 12" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
			</div>

			<!-- 抽屉内容 -->
			<div class="feryfit-drawer-content">
				<ul class="feryfit-drawer-options">
					<?php if (!empty($options['whatsapp'])) : ?>
						<li class="feryfit-drawer-option">
							<a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" class="feryfit-drawer-link feryfit-contact-link" data-contact-type="whatsapp">
								<span class="feryfit-drawer-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
										<path d="M14.2221 1.75C7.57996 1.75 2.19483 7.09302 2.19483 13.6846C2.19483 15.9393 2.82548 18.0481 3.92071 19.8471L1.75 26.25L8.40897 24.1346C10.1321 25.0799 12.1138 25.6191 14.2221 25.6191C20.8652 25.6191 26.25 20.2753 26.25 13.6846C26.25 7.09302 20.8652 1.75 14.2221 1.75ZM20.2029 18.217C19.9199 18.9184 18.6402 19.5586 18.0756 19.5881C17.5115 19.618 17.4955 20.0252 14.4203 18.6893C11.3455 17.3531 9.49555 14.1041 9.3497 13.8949C9.20374 13.6864 8.15861 12.197 8.21499 10.6981C8.27176 9.19898 9.09256 8.49171 9.38552 8.19755C9.67816 7.90295 10.0137 7.85017 10.2185 7.84684C10.4607 7.8429 10.6175 7.83962 10.7967 7.84623C10.9758 7.85302 11.2447 7.80877 11.4776 8.42805C11.7103 9.04728 12.2674 10.5692 12.3387 10.7243C12.4099 10.8796 12.454 11.0593 12.3443 11.2587C12.2343 11.4585 12.1779 11.5833 12.0183 11.7554C11.8578 11.9276 11.6806 12.1405 11.5375 12.2721C11.3778 12.4179 11.211 12.5769 11.3789 12.8884C11.5467 13.1998 12.1255 14.2204 13.0074 15.0615C14.1408 16.1427 15.1182 16.5014 15.419 16.6648C15.7206 16.829 15.9006 16.8108 16.0876 16.614C16.2738 16.4171 16.8882 15.7528 17.1046 15.4563C17.3209 15.1589 17.5236 15.2171 17.8014 15.3289C18.0791 15.4411 19.56 16.2343 19.8617 16.3981C20.163 16.5616 20.3644 16.6458 20.4364 16.7754C20.5085 16.9056 20.4857 17.5154 20.2029 18.217Z" fill="#999999" />
									</svg>
								</span>
								<span class="feryfit-drawer-text"><?php echo esc_html( pll__( 'Chat on WhatsApp', 'feryfit' ) ); ?></span>
								<span class="feryfit-drawer-arrow">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<path d="M9.5 6L15.5 12L9.5 18" stroke="#B8B8B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
							</a>
						</li>
					<?php endif; ?>

					<?php if (!empty($options['email'])) : ?>
						<li class="feryfit-drawer-option">
							<a href="<?php echo esc_url($email_url); ?>" class="feryfit-drawer-link">
								<span class="feryfit-drawer-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
										<path d="M4.18976 4.45996H23.7341C25.0687 4.45996 26.16 5.51202 26.1768 6.80752L13.9689 13.4389L1.75826 6.81311C1.76945 5.51482 2.8523 4.45996 4.18976 4.45996ZM1.75826 9.35094L1.74707 21.0831C1.74707 22.3898 2.8467 23.4587 4.18976 23.4587H23.7341C25.0771 23.4587 26.1768 22.3898 26.1768 21.0831V9.34534L14.2543 15.6689C14.0724 15.7668 13.8514 15.7668 13.6695 15.6689L1.75826 9.35094Z" fill="#999999" />
									</svg>
								</span>
								<span class="feryfit-drawer-text"><?php echo esc_html( pll__( 'Send an Email', 'feryfit' ) ); ?></span>
								<span class="feryfit-drawer-arrow">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<path d="M9.5 6L15.5 12L9.5 18" stroke="#B8B8B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
							</a>
						</li>
					<?php endif; ?>

					<?php if (!empty($options['facebook'])) : ?>
						<li class="feryfit-drawer-option">
							<a href="<?php echo esc_url($options['facebook']); ?>" target="_blank" class="feryfit-drawer-link">
								<span class="feryfit-drawer-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
										<g clip-path="url(#clip0_713_388)">
											<path d="M21.1643 0H1.23572C0.553481 0 0 0.553459 0 1.23572V21.1643C0 21.8474 0.553459 22.4 1.23572 22.4H11.9635V13.7256H9.04306V10.346H11.9635V7.84934C11.9635 4.95694 13.7312 3.38334 16.3119 3.38334C17.5485 3.38334 18.6116 3.47386 18.9215 3.51494V6.53894H17.1286C15.7286 6.53894 15.456 7.21186 15.456 8.19186V10.3488H18.8011L18.3671 13.7368H15.456V22.4H21.1633C21.8475 22.4 22.4 21.8475 22.4 21.1643V1.23572C22.4 0.553481 21.8475 0 21.1643 0Z" fill="#999999" />
										</g>
										<defs>
											<clipPath id="clip0_713_388">
												<rect width="22.4" height="22.4" fill="white" />
											</clipPath>
										</defs>
									</svg>
								</span>
								<span class="feryfit-drawer-text"><?php echo esc_html( pll__( 'Message us on Facebook', 'feryfit' ) ); ?></span>
								<span class="feryfit-drawer-arrow">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<path d="M9.5 6L15.5 12L9.5 18" stroke="#B8B8B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

		<!-- PC端气泡框 -->
		<div class="feryfit-chat-bubble">
			<ul class="feryfit-chat-options">
				<?php if (!empty($options['whatsapp'])) : ?>
					<li class="feryfit-chat-option">
						<a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" class="feryfit-chat-link feryfit-contact-link" data-contact-type="whatsapp">
							<span class="feryfit-chat-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
									<path d="M14.2221 1.75C7.57996 1.75 2.19483 7.09302 2.19483 13.6846C2.19483 15.9393 2.82548 18.0481 3.92071 19.8471L1.75 26.25L8.40897 24.1346C10.1321 25.0799 12.1138 25.6191 14.2221 25.6191C20.8652 25.6191 26.25 20.2753 26.25 13.6846C26.25 7.09302 20.8652 1.75 14.2221 1.75ZM20.2029 18.217C19.9199 18.9184 18.6402 19.5586 18.0756 19.5881C17.5115 19.618 17.4955 20.0252 14.4203 18.6893C11.3455 17.3531 9.49555 14.1041 9.3497 13.8949C9.20374 13.6864 8.15861 12.197 8.21499 10.6981C8.27176 9.19898 9.09256 8.49171 9.38552 8.19755C9.67816 7.90295 10.0137 7.85017 10.2185 7.84684C10.4607 7.8429 10.6175 7.83962 10.7967 7.84623C10.9758 7.85302 11.2447 7.80877 11.4776 8.42805C11.7103 9.04728 12.2674 10.5692 12.3387 10.7243C12.4099 10.8796 12.454 11.0593 12.3443 11.2587C12.2343 11.4585 12.1779 11.5833 12.0183 11.7554C11.8578 11.9276 11.6806 12.1405 11.5375 12.2721C11.3778 12.4179 11.211 12.5769 11.3789 12.8884C11.5467 13.1998 12.1255 14.2204 13.0074 15.0615C14.1408 16.1427 15.1182 16.5014 15.419 16.6648C15.7206 16.829 15.9006 16.8108 16.0876 16.614C16.2738 16.4171 16.8882 15.7528 17.1046 15.4563C17.3209 15.1589 17.5236 15.2171 17.8014 15.3289C18.0791 15.4411 19.56 16.2343 19.8617 16.3981C20.163 16.5616 20.3644 16.6458 20.4364 16.7754C20.5085 16.9056 20.4857 17.5154 20.2029 18.217Z" fill="#999999" />
								</svg>
							</span>
							<span class="feryfit-chat-text"><?php echo esc_html( pll__( 'Chat on WhatsApp', 'feryfit' ) ); ?></span>
							<span class="feryfit-chat-arrow">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M9.5 6L15.5 12L9.5 18" stroke="#B8B8B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (!empty($options['email'])) : ?>
					<li class="feryfit-chat-option">
						<a href="<?php echo esc_url($email_url); ?>" class="feryfit-chat-link">
							<span class="feryfit-chat-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
									<path d="M4.18976 4.45996H23.7341C25.0687 4.45996 26.16 5.51202 26.1768 6.80752L13.9689 13.4389L1.75826 6.81311C1.76945 5.51482 2.8523 4.45996 4.18976 4.45996ZM1.75826 9.35094L1.74707 21.0831C1.74707 22.3898 2.8467 23.4587 4.18976 23.4587H23.7341C25.0771 23.4587 26.1768 22.3898 26.1768 21.0831V9.34534L14.2543 15.6689C14.0724 15.7668 13.8514 15.7668 13.6695 15.6689L1.75826 9.35094Z" fill="#999999" />
								</svg>
							</span>
							<span class="feryfit-chat-text"><?php echo esc_html( pll__( 'Send an Email', 'feryfit' ) ); ?></span>
							<span class="feryfit-chat-arrow">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M9.5 6L15.5 12L9.5 18" stroke="#B8B8B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (!empty($options['facebook'])) : ?>
					<li class="feryfit-chat-option">
						<a href="<?php echo esc_url($options['facebook']); ?>" target="_blank" class="feryfit-chat-link">
							<span class="feryfit-chat-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
									<g clip-path="url(#clip0_713_388)">
										<path d="M21.1643 0H1.23572C0.553481 0 0 0.553459 0 1.23572V21.1643C0 21.8474 0.553459 22.4 1.23572 22.4H11.9635V13.7256H9.04306V10.346H11.9635V7.84934C11.9635 4.95694 13.7312 3.38334 16.3119 3.38334C17.5485 3.38334 18.6116 3.47386 18.9215 3.51494V6.53894H17.1286C15.7286 6.53894 15.456 7.21186 15.456 8.19186V10.3488H18.8011L18.3671 13.7368H15.456V22.4H21.1633C21.8475 22.4 22.4 21.8475 22.4 21.1643V1.23572C22.4 0.553481 21.8475 0 21.1643 0Z" fill="#999999" />
									</g>
									<defs>
										<clipPath id="clip0_713_388">
											<rect width="22.4" height="22.4" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</span>
							<span class="feryfit-chat-text"><?php echo esc_html( pll__( 'Message us on Facebook', 'feryfit' ) ); ?></span>
							<span class="feryfit-chat-arrow">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M9.5 6L15.5 12L9.5 18" stroke="#B8B8B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<!-- 点击按钮 -->
		<div class="feryfit-floating-btn" id="feryfitFloatingChatBtn">
			<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
				<g filter="url(#filter0_d_713_394)">
					<circle cx="30" cy="30" r="24" fill="#CD3D1C" />
				</g>
				<path d="M40.4 26.1197H40.309C39.9763 23.6143 38.7476 21.3153 36.8514 19.6501C34.9552 17.985 32.5205 17.0671 30 17.0671C27.4795 17.0671 25.0448 17.985 23.1486 19.6501C21.2524 21.3153 20.0237 23.6143 19.691 26.1197H19.6C18.9104 26.1197 18.2491 26.3944 17.7615 26.8834C17.2739 27.3723 17 28.0355 17 28.727V31.3343C17 32.0258 17.2739 32.689 17.7615 33.1779C18.2491 33.6669 18.9104 33.9416 19.6 33.9416H20.9C21.2448 33.9416 21.5754 33.8042 21.8192 33.5598C22.063 33.3153 22.2 32.9837 22.2 32.6379V27.4233C22.2 25.3489 23.0218 23.3593 24.4846 21.8924C25.9474 20.4255 27.9313 19.6015 30 19.6015C32.0687 19.6015 34.0526 20.4255 35.5154 21.8924C36.9782 23.3593 37.8 25.3489 37.8 27.4233V32.6379C37.8 33.6651 37.5982 34.6823 37.2063 35.6313C36.8143 36.5803 36.2397 37.4425 35.5154 38.1689C34.7911 38.8952 33.9313 39.4713 32.9849 39.8644C32.0386 40.2575 31.0243 40.4598 30 40.4598C29.6552 40.4598 29.3246 40.5972 29.0808 40.8417C28.837 41.0862 28.7 41.4177 28.7 41.7635C28.7 42.1092 28.837 42.4408 29.0808 42.6853C29.3246 42.9298 29.6552 43.0671 30 43.0671C32.5315 43.0648 34.9752 42.1367 36.8731 40.4567C38.7709 38.7767 39.9926 36.4603 40.309 33.9416H40.4C41.0896 33.9416 41.7509 33.6669 42.2385 33.1779C42.7261 32.689 43 32.0258 43 31.3343V28.727C43 28.0355 42.7261 27.3723 42.2385 26.8834C41.7509 26.3944 41.0896 26.1197 40.4 26.1197Z" fill="white" />
				<path d="M34.544 31.9654C34.8007 31.6892 34.9627 31.2915 34.9943 30.8597C35.0259 30.4279 34.9246 29.9975 34.7126 29.6631C34.5006 29.3286 34.1953 29.1176 33.8638 29.0764C33.5324 29.0352 33.202 29.1672 32.9453 29.4434C32.1037 30.3011 31.066 30.767 29.9976 30.767C28.9291 30.767 27.8914 30.3011 27.0499 29.4434C26.7949 29.1672 26.466 29.0344 26.1358 29.074C25.8055 29.1137 25.5008 29.3226 25.2888 29.6549C25.0768 29.9872 24.9748 30.4156 25.0053 30.8458C25.0357 31.2761 25.1961 31.6729 25.4512 31.9491C26.7331 33.3186 28.3397 34.0671 29.9976 34.0671C31.6555 34.0671 33.2621 33.3186 34.544 31.9491V31.9654Z" fill="white" />
				<defs>
					<filter id="filter0_d_713_394" x="0" y="0" width="60" height="60" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
						<feFlood flood-opacity="0" result="BackgroundImageFix" />
						<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
						<feMorphology radius="2" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_713_394" />
						<feOffset />
						<feGaussianBlur stdDeviation="2" />
						<feComposite in2="hardAlpha" operator="out" />
						<feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
						<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_713_394" />
						<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_713_394" result="shape" />
					</filter>
				</defs>
			</svg>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var floatingChat = document.querySelector('.feryfit-floating-chat');
			var floatingBtn = document.getElementById('feryfitFloatingChatBtn');
			var chatBubble = document.querySelector('.feryfit-chat-bubble');
			var chatDrawer = document.getElementById('feryfitChatDrawer');
			var chatBackdrop = document.getElementById('feryfitChatBackdrop');
			var drawerClose = document.getElementById('feryfitDrawerClose');

			if (!floatingChat || !floatingBtn) return;

			function isMobile() {
				return window.innerWidth < 768;
			}

			function closeDrawer() {
				chatDrawer.classList.remove('active');
				chatBackdrop.classList.remove('active');
				document.body.style.overflow = '';
			}

			function openDrawer() {
				chatDrawer.classList.add('active');
				chatBackdrop.classList.add('active');
				document.body.style.overflow = 'hidden';
			}

			// 点击按钮
			floatingBtn.addEventListener('click', function(e) {
				e.stopPropagation();
				
				if (isMobile()) {
					// 移动端：打开抽屉
					openDrawer();
				} else {
					// PC端：切换气泡框
					chatBubble.classList.toggle('active');
				}
			});

			// 点击页面其他地方关闭
			document.addEventListener('click', function(e) {
				if (!floatingChat.contains(e.target)) {
					if (!isMobile()) {
						chatBubble.classList.remove('active');
					}
				}
			});

			// 移动端关闭按钮
			if (drawerClose) {
				drawerClose.addEventListener('click', function(e) {
					e.stopPropagation();
					closeDrawer();
				});
			}

			// 点击遮罩层关闭抽屉
			if (chatBackdrop) {
				chatBackdrop.addEventListener('click', function() {
					closeDrawer();
				});
			}

			// ESC键关闭抽屉
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape') {
					closeDrawer();
				}
			});
		});
	</script>
<?php
}
