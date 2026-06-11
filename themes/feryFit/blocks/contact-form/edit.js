/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { emailLabel, emailPlaceholder, nameLabel, namePlaceholder, messageLabel, submitButtonText, errorTitle, errorMessage, successMessage } = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( '表单设置', 'feryfit' ) }>
					<TextControl
						label={ __( 'Email 标签', 'feryfit' ) }
						value={ emailLabel }
						onChange={ ( value ) => setAttributes( { emailLabel: value } ) }
						placeholder={ __( '输入 Email 标签...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( 'Email 占位符', 'feryfit' ) }
						value={ emailPlaceholder }
						onChange={ ( value ) => setAttributes( { emailPlaceholder: value } ) }
						placeholder={ __( '输入 Email 占位符...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( 'Name 标签', 'feryfit' ) }
						value={ nameLabel }
						onChange={ ( value ) => setAttributes( { nameLabel: value } ) }
						placeholder={ __( '输入 Name 标签...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( 'Name 占位符', 'feryfit' ) }
						value={ namePlaceholder }
						onChange={ ( value ) => setAttributes( { namePlaceholder: value } ) }
						placeholder={ __( '输入 Name 占位符...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( 'Message 标签', 'feryfit' ) }
						value={ messageLabel }
						onChange={ ( value ) => setAttributes( { messageLabel: value } ) }
						placeholder={ __( '输入 Message 标签...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '提交按钮文字', 'feryfit' ) }
						value={ submitButtonText }
						onChange={ ( value ) => setAttributes( { submitButtonText: value } ) }
						placeholder={ __( '输入按钮文字...', 'feryfit' ) }
					/>
				</PanelBody>
				<PanelBody title={ __( '弹框文字', 'feryfit' ) }>
					<TextControl
						label={ __( '失败弹框标题', 'feryfit' ) }
						value={ errorTitle }
						onChange={ ( value ) => setAttributes( { errorTitle: value } ) }
						placeholder={ __( '输入失败弹框标题...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '失败弹框消息', 'feryfit' ) }
						value={ errorMessage }
						onChange={ ( value ) => setAttributes( { errorMessage: value } ) }
						placeholder={ __( '输入失败弹框消息...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '成功提示语', 'feryfit' ) }
						value={ successMessage }
						onChange={ ( value ) => setAttributes( { successMessage: value } ) }
						placeholder={ __( '输入成功提示语...', 'feryfit' ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div className="contact-form-container">
				<form id="contact-form" className="contact-form">
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
					<div id="contact-form-message" className="contact-form__message"></div>
				</form>
			</div>
		</div>
	);
}