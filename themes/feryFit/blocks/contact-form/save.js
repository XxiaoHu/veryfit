/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

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
	const { emailLabel, emailPlaceholder, nameLabel, namePlaceholder, messageLabel, submitButtonText, errorTitle, errorMessage, successMessage } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div {...blockProps}>
			<div className="contact-form-container">
				<form id="contact-form" className="contact-form" data-error-title={ errorTitle } data-error-message={ errorMessage } data-success-message={ successMessage }>
					<div className="contact-form__row contact-form__row--honeypot">
						<div className="contact-form__field">
							<label className="contact-form__label" htmlFor="hp_website_cf">
								Leave this empty
							</label>
							<input
								type="text"
								name="website"
								id="hp_website_cf"
								className="contact-form__input"
								tabIndex="-1"
								autoComplete="off"
							/>
						</div>
					</div>
					<input type="hidden" name="language" id="contact-form-language" value="" />
					<div className="contact-form__row">
						<div className="contact-form__field">
							<label className="contact-form__label">
								{ emailLabel }
							</label>
							<input
								type="email"
								name="email"
								className="contact-form__input"
								placeholder={ emailPlaceholder }
								required
							/>
							<span className="contact-form__required">*</span>
						</div>
					</div>
					<div className="contact-form__row">
						<div className="contact-form__field">
							<label className="contact-form__label">
								{ nameLabel }
							</label>
							<input
								type="text"
								name="name"
								className="contact-form__input"
								placeholder={ namePlaceholder }
								required
							/>
							<span className="contact-form__required">*</span>
						</div>
					</div>
					<div className="contact-form__row">
						<div className="contact-form__field">
							<label className="contact-form__label">
								{ messageLabel }
							</label>
							<textarea
								name="message"
								className="contact-form__textarea"
								rows={ 4 }
								required
							></textarea>
							<span className="contact-form__required">*</span>
						</div>
					</div>
					<div className="contact-form__submit">
						<button type="submit" className="contact-form__button">
							{ submitButtonText }
						</button>
					</div>
				</form>
			</div>
		</div>
	);
}