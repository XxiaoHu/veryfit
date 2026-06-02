/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save({ attributes }) {
	const { whatsappText, emailText, facebookText, contactText } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div {...blockProps}>
			<div className="contact-cards">
				<a href="#" data-contact-type="whatsapp" className="contact-card contact-card--whatsapp" target="_blank" rel="noopener noreferrer">
					<div className="contact-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="73" height="74" viewBox="0 0 73 74" fill="none">
							<path d="M46.6793 40.3793C47.0923 40.3793 48.6326 41.0742 51.3001 42.4639C53.9715 43.8536 55.3843 44.6913 55.5426 44.9731C55.6044 45.1314 55.6391 45.3708 55.6391 45.6873C55.6391 46.7296 55.3689 47.9263 54.8284 49.289C54.3266 50.5205 53.2071 51.555 51.4661 52.3927C49.7289 53.2304 48.1192 53.6512 46.6329 53.6473C44.8302 53.6473 41.8307 52.6668 37.6268 50.7135C34.6229 49.3535 31.8927 47.4566 29.5702 45.116C27.2926 42.8114 24.9571 39.8891 22.556 36.353C20.2784 32.9675 19.1589 29.9062 19.1898 27.1577V26.7755C19.2863 23.8995 20.456 21.4058 22.695 19.2864C23.4554 18.5916 24.2777 18.2441 25.1656 18.2441C25.3509 18.2441 25.6365 18.2712 26.0148 18.3213C26.3932 18.3677 26.6943 18.3908 26.9143 18.3908C27.5127 18.3908 27.9334 18.4912 28.1689 18.6997C28.4083 18.9043 28.6515 19.3366 28.9024 20.0006C29.1572 20.6337 29.6783 22.0234 30.4658 24.1698C31.2572 26.32 31.6509 27.5012 31.6509 27.7213C31.6509 28.3891 31.1066 29.2963 30.018 30.4505C28.9255 31.6048 28.3812 32.3382 28.3812 32.6509C28.3812 32.8748 28.4584 33.1142 28.6167 33.3651C29.6899 35.6697 31.3035 37.8354 33.4499 39.8543C35.2218 41.5297 37.6036 43.124 40.6069 44.6412C40.9189 44.8419 41.2786 44.9565 41.6492 44.9731C42.1241 44.9731 42.9772 44.2088 44.2086 42.6762C45.4401 41.1437 46.2623 40.3793 46.6715 40.3793H46.6793ZM37.0593 65.4947C41.0286 65.5042 44.9574 64.6974 48.6017 63.1245C52.1444 61.635 55.3682 59.4792 58.0982 56.7742C60.8051 54.0436 62.9622 50.8184 64.4523 47.2739C66.0252 43.6296 66.832 39.7008 66.8225 35.7315C66.8315 31.7635 66.0247 27.836 64.4523 24.1929C62.9629 20.6502 60.8071 17.4263 58.102 14.6965C55.3702 11.9892 52.1437 9.83206 48.5979 8.34236C44.9547 6.76993 41.0273 5.96316 37.0593 5.97211C33.0913 5.96316 29.1639 6.76993 25.5207 8.34236C21.9766 9.83135 18.7514 11.9871 16.0204 14.6926C13.3149 17.4236 11.1591 20.6488 9.67016 24.1929C8.09722 27.8372 7.29043 31.7661 7.29991 35.7353C7.29991 42.1474 9.19148 47.961 12.9862 53.1725L9.24166 64.2131L20.7069 60.5651C25.5429 63.8048 31.2384 65.5218 37.0593 65.4947ZM37.0593 0.000155669C41.8924 0.000155669 46.5171 0.9498 50.9218 2.84137C55.1747 4.63293 59.0445 7.22371 62.3214 10.4733C65.5697 13.7503 68.1592 17.6201 69.9494 21.8729C71.8386 26.2483 72.807 30.9657 72.7945 35.7315C72.7945 40.5685 71.8448 45.1893 69.9494 49.594C68.1592 53.8467 65.5697 57.7165 62.3214 60.9936C59.0446 64.2432 55.1748 66.834 50.9218 68.6255C46.5461 70.5133 41.8287 71.4804 37.0631 71.4667C31.0086 71.4952 25.0491 69.9608 19.7611 67.0118L0 73.3621L6.44677 54.1685C3.05582 48.6213 1.28285 42.2367 1.32796 35.7353C1.32796 30.8983 2.27374 26.2775 4.16917 21.8729C5.96022 17.6185 8.55103 13.7473 11.8011 10.4694C15.0759 7.22198 18.943 4.63256 23.1929 2.84137C27.5698 0.953025 32.2886 -0.0141156 37.0554 0.000155669H37.0593Z" fill="#929292" />
						</svg>
					</div>
					<RichText.Content tagName="span" className="contact-card__text contact-card__text--whatsapp" value={whatsappText} />
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
					<RichText.Content tagName="span" className="contact-card__text contact-card__text--email" value={emailText} />
				</a>

				<a href="#" data-contact-type="facebook" className="contact-card contact-card--facebook" target="_blank" rel="noopener noreferrer">
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
					<RichText.Content tagName="span" className="contact-card__text contact-card__text--facebook" value={facebookText} />
				</a>
			</div>
			<div className='contact-text'>
				{contactText}
			</div>
		</div>
	);
}
