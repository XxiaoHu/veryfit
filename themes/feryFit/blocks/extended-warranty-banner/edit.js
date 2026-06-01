/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { title, feature1Text, feature2Text, feature3Text } = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( '内容设置', 'feryfit' ) }>
					<TextControl
						label={ __( '标题', 'feryfit' ) }
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						placeholder={ __( '输入标题...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '特性1', 'feryfit' ) }
						value={ feature1Text }
						onChange={ ( value ) => setAttributes( { feature1Text: value } ) }
						placeholder={ __( '输入特性1...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '特性2', 'feryfit' ) }
						value={ feature2Text }
						onChange={ ( value ) => setAttributes( { feature2Text: value } ) }
						placeholder={ __( '输入特性2...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '特性3', 'feryfit' ) }
						value={ feature3Text }
						onChange={ ( value ) => setAttributes( { feature3Text: value } ) }
						placeholder={ __( '输入特性3...', 'feryfit' ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div 
				className="extended-warranty-banner"
				style={ { 
					background: 'linear-gradient(to bottom, #FFF9E6 0%, #FFFFFF 100%)',
					minHeight: '170px',
					borderRadius: '12px',
					padding: '20px'
				} }
			>
				<div className="extended-warranty-banner__content">
					<RichText
						tagName="h2"
						className="extended-warranty-banner__title"
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						placeholder={ __( '输入标题...', 'feryfit' ) }
						style={{ 
							color: '#c73e1d',
							fontSize: '24px',
							fontWeight: '700',
							textAlign: 'center',
							marginBottom: '20px',
							marginTop: '0'
						}}
					/>
					<div className="extended-warranty-banner__features">
						<div className="extended-warranty-banner__feature">
							<div className="extended-warranty-banner__icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M15.2208 2.5C16.203 2.5 17.1325 2.72388 17.9604 3.12303L16.3586 4.71334C15.4353 5.63005 15.4353 7.11635 16.3586 8.03305C17.2819 8.94975 18.7789 8.94975 19.7022 8.03305L21.1285 6.61685C21.3689 7.2779 21.5 7.9909 21.5 8.73425C21.5 12.1774 18.6888 14.9685 15.2208 14.9685C14.3913 14.9685 13.5993 14.8088 12.8743 14.5187L6.535 20.8127C5.6119 21.7291 4.11535 21.7291 3.1923 20.8127C2.26924 19.8962 2.26924 18.4104 3.1923 17.4939L9.47615 11.2549C9.13255 10.4842 8.9417 9.63135 8.9417 8.73425C8.9417 5.29115 11.7529 2.5 15.2208 2.5Z" stroke="#CD3D1C" stroke-width="2" stroke-linejoin="round" />
								</svg>
							</div>
							<span className="extended-warranty-banner__feature-text" style={{ fontSize: '14px', color: '#333' }}>
								{ feature1Text }
							</span>
						</div>
						<div className="extended-warranty-banner__feature">
							<div className="extended-warranty-banner__icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M18 16C20.2092 16 22 14.2092 22 12C22 9.79085 20.2092 8 18 8" stroke="#CD3D1C" stroke-width="2" stroke-linejoin="round" />
									<path d="M6 8C3.79086 8 2 9.79085 2 12C2 14.2092 3.79086 16 6 16" stroke="#CD3D1C" stroke-width="2" stroke-linejoin="round" />
									<path d="M6 16V15.75V14.5V12V8C6 4.68629 8.6863 2 12 2C15.3137 2 18 4.68629 18 8V16C18 19.3137 15.3137 22 12 22" stroke="#CD3D1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</div>
							<span className="extended-warranty-banner__feature-text" style={{ fontSize: '14px', color: '#333' }}>
								{ feature2Text }
							</span>
						</div>
						<div className="extended-warranty-banner__feature">
							<div className="extended-warranty-banner__icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M4.5 8.00001L16.9999 3L19.0001 8.00001" stroke="#CD3D1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M2 8H22V11C20.5 11 19 12 19 13.75C19 15.5 20.5 17 22 17V20H2V17C3.50008 17 5 16 5 14C5 12 3.5 11 2 11V8Z" stroke="#CD3D1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M8.5 12.6924H11.5" stroke="#CD3D1C" stroke-width="2" stroke-linecap="round" />
									<path d="M8.5 15.6924H15.5" stroke="#CD3D1C" stroke-width="2" stroke-linecap="round" />
								</svg>
							</div>
							<span className="extended-warranty-banner__feature-text" style={{ fontSize: '14px', color: '#333' }}>
								{ feature3Text }
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}