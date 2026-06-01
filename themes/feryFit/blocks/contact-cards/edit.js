/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { whatsappText, emailText, facebookText, contactText } = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'WhatsApp 设置', 'feryfit' ) }>
					<TextControl
						label={ __( '显示文字', 'feryfit' ) }
						value={ whatsappText }
						onChange={ ( value ) => setAttributes( { whatsappText: value } ) }
						placeholder={ __( '输入 WhatsApp 显示文字...', 'feryfit' ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Email 设置', 'feryfit' ) }>
					<TextControl
						label={ __( '显示文字', 'feryfit' ) }
						value={ emailText }
						onChange={ ( value ) => setAttributes( { emailText: value } ) }
						placeholder={ __( '输入 Email 显示文字...', 'feryfit' ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Facebook 设置', 'feryfit' ) }>
					<TextControl
						label={ __( '显示文字', 'feryfit' ) }
						value={ facebookText }
						onChange={ ( value ) => setAttributes( { facebookText: value } ) }
						placeholder={ __( '输入 Facebook 显示文字...', 'feryfit' ) }
					/>
				</PanelBody>

				<PanelBody title={ __( '底部文案设置', 'feryfit' ) }>
					<TextControl
						label={ __( '底部文字', 'feryfit' ) }
						value={ contactText }
						onChange={ ( value ) => setAttributes( { contactText: value } ) }
						placeholder={ __( '输入底部提示文字...', 'feryfit' ) }
						multiline={ true }
						rows={ 3 }
					/>
				</PanelBody>
			</InspectorControls>

			<div className="contact-cards">
				<a href="#" data-contact-type="whatsapp" className="contact-card contact-card--whatsapp">
					<div className="contact-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="93" height="93" viewBox="0 0 93 93" fill="none">
							<path d="M56.608 50.0224C57.021 50.0224 58.5613 50.7173 61.2288 52.107C63.9002 53.4967 65.313 54.3344 65.4713 54.6162C65.5331 54.7745 65.5678 55.0138 65.5678 55.3304C65.5678 56.3727 65.2976 57.5694 64.7572 58.9321C64.2553 60.1635 63.1358 61.1981 61.3948 62.0358C59.6576 62.8735 58.0479 63.2943 56.5616 63.2904C54.7589 63.2904 51.7594 62.3099 47.5555 60.3565C44.5517 58.9966 41.8214 57.0997 39.4989 54.759C37.2213 52.4544 34.8858 49.5321 32.4847 45.9961C30.2071 42.6105 29.0876 39.5493 29.1185 36.8007V36.4186C29.215 33.5426 30.3847 31.0488 32.6237 28.9295C33.3841 28.2346 34.2064 27.8872 35.0943 27.8872C35.2796 27.8872 35.5652 27.9142 35.9436 27.9644C36.3219 28.0107 36.623 28.0339 36.843 28.0339C37.4414 28.0339 37.8621 28.1343 38.0976 28.3427C38.337 28.5473 38.5802 28.9797 38.8311 29.6437C39.0859 30.2768 39.607 31.6665 40.3945 33.8128C41.1859 35.963 41.5797 37.1443 41.5797 37.3643C41.5797 38.0322 41.0353 38.9394 39.9467 40.0936C38.8543 41.2478 38.3099 41.9813 38.3099 42.294C38.3099 42.5179 38.3872 42.7572 38.5454 43.0082C39.6186 45.3128 41.2322 47.4784 43.3786 49.4974C45.1505 51.1728 47.5323 52.7671 50.5356 54.2842C50.8477 54.485 51.2073 54.5996 51.5779 54.6162C52.0528 54.6162 52.9059 53.8519 54.1374 52.3193C55.3688 50.7868 56.1911 50.0224 56.6003 50.0224H56.608ZM46.988 75.1378C50.9573 75.1473 54.8861 74.3405 58.5304 72.7675C62.0731 71.278 65.297 69.1223 68.0269 66.4173C70.7338 63.6867 72.8909 60.4615 74.381 56.917C75.9539 53.2727 76.7607 49.3438 76.7512 45.3745C76.7602 41.4066 75.9534 37.4791 74.381 33.836C72.8916 30.2932 70.7358 27.0694 68.0307 24.3395C65.2989 21.6323 62.0724 19.4751 58.5266 17.9854C54.8834 16.413 50.956 15.6062 46.988 15.6152C43.02 15.6062 39.0926 16.413 35.4494 17.9854C31.9053 19.4744 28.6801 21.6302 25.9491 24.3357C23.2437 27.0667 21.0879 30.2919 19.5989 33.836C18.0259 37.4803 17.2191 41.4091 17.2286 45.3784C17.2286 51.7904 19.1202 57.6041 22.9149 62.8156L19.1704 73.8562L30.6356 70.2081C35.4716 73.4478 41.1672 75.1648 46.988 75.1378ZM46.988 9.64322C51.8211 9.64322 56.4458 10.5929 60.8505 12.4844C65.1034 14.276 68.9732 16.8668 72.2501 20.1163C75.4984 23.3934 78.0879 27.2632 79.8781 31.5159C81.7673 35.8913 82.7357 40.6087 82.7232 45.3745C82.7232 50.2116 81.7735 54.8324 79.8781 59.237C78.0879 63.4898 75.4984 67.3596 72.2501 70.6366C68.9733 73.8863 65.1035 76.4771 60.8505 78.2685C56.4748 80.1563 51.7574 81.1235 46.9919 81.1097C40.9373 81.1383 34.9778 79.6039 29.6898 76.6549L9.92871 83.0052L16.3755 63.8116C12.9845 58.2644 11.2116 51.8798 11.2567 45.3784C11.2567 40.5414 12.2025 35.9206 14.0979 31.5159C15.8889 27.2615 18.4797 23.3904 21.7298 20.1125C25.0046 16.865 28.8717 14.2756 33.1216 12.4844C37.4985 10.5961 42.2173 9.62895 46.9841 9.64322H46.988Z" fill="#929292" />
						</svg>
					</div>
					<RichText
						tagName="span"
						className="contact-card__text contact-card__text--whatsapp"
						value={ whatsappText }
						onChange={ ( value ) => setAttributes( { whatsappText: value } ) }
						placeholder={ __( 'WhatsApp 文字...', 'feryfit' ) }
					/>
				</a>

				<a href="mailto:" data-contact-type="email" className="contact-card contact-card--email">
					<div className="contact-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="71" height="65" viewBox="0 0 71 65" fill="none">
							<g clip-path="url(#clip0_137_596)">
								<path d="M64.5996 64.339H5.87269C4.31516 64.339 2.82141 63.7228 1.72007 62.6259C0.618728 61.529 0 60.0413 0 58.49V5.849C0 4.29775 0.618728 2.81003 1.72007 1.71313C2.82141 0.616233 4.31516 0 5.87269 0L64.5996 0C66.1571 0 67.6509 0.616233 68.7522 1.71313C69.8536 2.81003 70.4723 4.29775 70.4723 5.849V58.49C70.4723 60.0413 69.8536 61.529 68.7522 62.6259C67.6509 63.7228 66.1571 64.339 64.5996 64.339ZM5.87269 58.49H64.5996V22.1414L37.782 37.5623C36.2527 38.1087 34.6686 38.4891 33.0574 38.697L5.87269 23.0655V58.49ZM5.87269 5.849V16.3099L34.5167 32.7837C34.6176 32.6769 34.7277 32.579 34.8456 32.4912L64.5996 15.3858V5.849H5.87269Z" fill="#929292" />
							</g>
							<defs>
								<clipPath id="clip0_137_596">
									<rect width="70.4666" height="64.339" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</div>
					<RichText
						tagName="span"
						className="contact-card__text contact-card__text--email"
						value={ emailText }
						onChange={ ( value ) => setAttributes( { emailText: value } ) }
						placeholder={ __( 'Email 文字...', 'feryfit' ) }
					/>
				</a>

				<a href="#" data-contact-type="facebook" className="contact-card contact-card--facebook">
					<div className="contact-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 65 65" fill="none">
							<g clip-path="url(#clip0_137_590)">
								<path d="M60.7897 0H3.54933C1.58975 0 0 1.58969 0 3.54933V60.7897C0 62.7519 1.58969 64.339 3.54933 64.339H34.3624V39.4237H25.9742V29.7166H34.3624V22.5455C34.3624 14.2377 39.4398 9.7179 46.8522 9.7179C50.4043 9.7179 53.4577 9.97789 54.3477 10.0959V18.7817H49.198C45.1768 18.7817 44.394 20.7145 44.394 23.5293V29.7246H54.0019L52.7554 39.4559H44.3939V64.339H60.787C62.752 64.339 64.339 62.752 64.339 60.7897V3.54933C64.339 1.58975 62.752 0 60.7897 0Z" fill="#999999" />
							</g>
							<defs>
								<clipPath id="clip0_137_590">
									<rect width="64.339" height="64.339" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</div>
					<RichText
						tagName="span"
						className="contact-card__text contact-card__text--facebook"
						value={ facebookText }
						onChange={ ( value ) => setAttributes( { facebookText: value } ) }
						placeholder={ __( 'Facebook 文字...', 'feryfit' ) }
					/>
				</a>
			</div>
			<div className="contact-text">
				{ contactText }
			</div>
		</div>
	);
}
