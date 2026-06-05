import { useBlockProps } from '@wordpress/block-editor';

const StarIcon = ({ filled }) => (
	<svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
		<path
			d="M11.4126 0L14.1068 8.2918H22.8253L15.7719 13.4164L18.466 21.7082L11.4126 16.5836L4.35917 21.7082L7.05334 13.4164L-8.01086e-05 8.2918H8.71843L11.4126 0Z"
			fill={filled ? '#CD3D1C' : '#D9D9D9'}
		/>
	</svg>
);

const DEFAULT_IMAGES = [
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

export default function save({ attributes }) {
	const {
		title,
		subtitle,
		orderNumberLabel,
		orderNumberPlaceholder,
		orderHelpLink,
		orderHelpTooltip,
		emailLabel,
		nameLabel,
		namePlaceholder,
		countryLabel,
		optionalLabel,
		experienceRatingLabel,
		ratingQuestion,
		checkboxText,
		submitButtonText,
		noOrderLinkText,
		noOrderLinkUrl,
		modalTitle,
		modalMessage,
		modalDescription,
		modalWhatsappText,
		modalFacebookText,
		modalBenefitsNote,
		errorModalTitle,
		errorModalMessage,
		productImage1,
		productImage2,
		productImage3,
		productImage4,
		productImage5,
		productImage6,
		productImage7,
		productImage8,
		productImage9,
		productImage10
	} = attributes;

	const images = [
		productImage1 || DEFAULT_IMAGES[0],
		productImage2 || DEFAULT_IMAGES[1],
		productImage3 || DEFAULT_IMAGES[2],
		productImage4 || DEFAULT_IMAGES[3],
		productImage5 || DEFAULT_IMAGES[4],
		productImage6 || DEFAULT_IMAGES[5],
		productImage7 || DEFAULT_IMAGES[6],
		productImage8 || DEFAULT_IMAGES[7],
		productImage9 || DEFAULT_IMAGES[8],
		productImage10 || DEFAULT_IMAGES[9]
	];
	const blockProps = useBlockProps.save();

	return (
		<div {...blockProps}>
			<div className="warranty-application"
				data-modal-title={modalTitle}
				data-modal-message={modalMessage}
				data-modal-description={modalDescription}
				data-modal-whatsapp={modalWhatsappText}
				data-modal-facebook={modalFacebookText}
				data-modal-benefits={modalBenefitsNote}
				data-error-modal-title={errorModalTitle}
				data-error-modal-message={errorModalMessage}
				data-product-images={JSON.stringify(images)}
			>
				<h2 className="warranty-application__title">{title}</h2>
				<p className="warranty-application__subtitle">{subtitle}</p>
				<form className="warranty-application__form" id="warranty-application-form">
					<div className="form-group form-group--honeypot">
						<label for="hp_website">Leave this empty</label>
						<div className="form-content">
							<input type="text" name="website" id="hp_website" tabIndex="-1" autoComplete="off" />
						</div>
					</div>
					<div className="form-group">
						<label>{orderNumberLabel} <span className="required">*</span></label>
						<div className="form-content">
							<input
								type="text"
								name="order_number"
								placeholder={orderNumberPlaceholder}
								required
							/>

						</div>
					</div>
					<div className='form-group'>
						<label></label>
						<a href="#" className="order-help-link">{orderHelpLink}</a>
						<div className="order-help-tooltip" id="order-help-tooltip">
							{orderHelpTooltip}
						</div>
					</div>
					<div className="form-group">
						<label>{emailLabel} <span className="required">*</span></label>
						<div className="form-content">
							<input
								type="email"
								name="email"
								required
							/>
						</div>
					</div>
					<div className="form-group">
						<label>{nameLabel}</label>
						<div className="form-content">
							<input type="text" name="name" placeholder={namePlaceholder} />
						</div>
					</div>
					<div className="form-group">
						<label>{countryLabel}</label>
						<div className="form-content">
							<input type="text" name="country" />
						</div>
					</div>
					<div className="form-group">
						<label>{optionalLabel}</label>
						<div className="form-content">
							<div className="rating-section">
								<div>{experienceRatingLabel}
									<div className="rating-labels">
										<p>{ratingQuestion}</p>
									</div>
								</div>
								<div className="stars">
									{[1, 2, 3, 4, 5].map((star) => (
										<span
											key={star}
											className="star"
											data-rating={star}
										>
											<StarIcon filled={false} />
										</span>
									))}
								</div>
								<input type="hidden" name="rating" id="rating-value" value="" />
							</div>

						</div>
					</div>
					<div className="form-group">
						<label></label>
						<div className="form-content">

							<div className='text-box'>
								<input className='input-box' type="checkbox" name="receive_updates" value="1" />
								{checkboxText}</div>
						</div>
					</div>
					<button type="submit" className="submit-btn">{submitButtonText}</button>
					<a href={noOrderLinkUrl} className="no-order-link">{noOrderLinkText} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M22 12C22 6.47715 17.5229 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5229 6.47715 22 12 22C17.5229 22 22 17.5229 22 12Z" stroke="#CD3D1C" stroke-width="1.6" stroke-linejoin="round" />
						<path d="M10.5 7.5L15 12L10.5 16.5" stroke="#CD3D1C" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
					</svg></a>
					<div className="form-message" id="form-message"></div>
				</form>
			</div>
		</div>
	);
}
