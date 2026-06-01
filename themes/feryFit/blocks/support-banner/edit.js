/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { iconId, iconUrl, title, buttonText, buttonUrl } = attributes;
	const blockProps = useBlockProps();

	const pages = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'page', { per_page: -1 } );
	}, [] );

	const pageOptions = [
		{ value: '', label: __( '选择页面...', 'feryfit' ) },
	];

	if ( pages ) {
		pages.forEach( ( page ) => {
			pageOptions.push( {
				value: page.link,
				label: page.title.rendered || page.id.toString(),
			} );
		} );
	}

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( '图标设置', 'feryfit' ) }>
					<div style={{ marginBottom: '20px' }}>
						<h4 style={{ margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }}>{ __( '图标图片', 'feryfit' ) }</h4>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( media ) => setAttributes( { 
									iconId: media.id,
									iconUrl: media.url 
								} ) }
								allowedTypes={ [ 'image' ] }
								value={ iconId }
								render={ ( { open } ) => (
									<Button onClick={ open } variant="primary">
										{ iconUrl ? __( '更换图标', 'feryfit' ) : __( '选择图标', 'feryfit' ) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
						{ iconUrl && (
							<Button 
								onClick={ () => setAttributes( { 
									iconId: 0,
									iconUrl: '' 
								} ) } 
								variant="secondary" 
								style={{ marginTop: '10px' }}
							>
								{ __( '移除图标', 'feryfit' ) }
							</Button>
						) }
					</div>
				</PanelBody>
				<PanelBody title={ __( '内容设置', 'feryfit' ) }>
					<TextControl
						label={ __( '标题', 'feryfit' ) }
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						placeholder={ __( '输入标题...', 'feryfit' ) }
					/>
				</PanelBody>
				<PanelBody title={ __( '按钮设置', 'feryfit' ) }>
					<TextControl
						label={ __( '按钮文字', 'feryfit' ) }
						value={ buttonText }
						onChange={ ( value ) => setAttributes( { buttonText: value } ) }
					/>
					<SelectControl
						label={ __( '按钮链接', 'feryfit' ) }
						value={ buttonUrl }
						options={ pageOptions }
						onChange={ ( value ) => setAttributes( { buttonUrl: value } ) }
					/>
					{ !buttonUrl && (
						<TextControl
							label={ __( '或手动输入链接', 'feryfit' ) }
							value=""
							onChange={ ( value ) => setAttributes( { buttonUrl: value } ) }
							placeholder={ __( '输入链接地址...', 'feryfit' ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div 
				className="support-banner"
				style={ { 
					background: '#fff',
					padding: '60px 40px',
					borderRadius: '8px',
					display: 'flex',
					flexDirection: 'column',
					alignItems: 'center',
					textAlign: 'center'
				} }
			>
				<div className="support-banner__icon-wrapper" style={{ marginBottom: '24px' }}>
					{ iconUrl ? (
						<img 
							src={ iconUrl } 
							alt=""
							style={{ maxWidth: '100px', maxHeight: '100px' }}
						/>
					) : (
						<div 
							style={{
								width: '80px',
								height: '80px',
								background: '#f5f5f5',
								borderRadius: '50%',
								display: 'flex',
								alignItems: 'center',
								justifyContent: 'center',
								fontSize: '32px',
								color: '#666'
							}}
						>
							?
						</div>
					) }
				</div>
				<RichText
					tagName="h3"
					className="support-banner__title"
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
					placeholder={ __( '输入标题...', 'feryfit' ) }
					style={{ 
						fontSize: '24px',
						fontWeight: '700',
						color: '#000',
						marginBottom: '24px',
						marginTop: '0'
					}}
				/>
				<div className="support-banner__button-wrapper">
					<span 
						className="support-banner__button"
						style={{
							display: 'inline-block',
							backgroundColor: '#c73e1d',
							color: '#fff',
							padding: '14px 36px',
							borderRadius: '6px',
							textDecoration: 'none',
							fontSize: '14px',
							fontWeight: '500'
						}}
					>
						{ buttonText || __( '按钮文字', 'feryfit' ) }
					</span>
				</div>
			</div>
		</div>
	);
}
