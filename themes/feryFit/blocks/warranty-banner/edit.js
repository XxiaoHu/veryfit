/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { backgroundImageId, backgroundImage, mobileBackgroundImageId, mobileBackgroundImage, title, subtitle, buttonText, buttonUrl } = attributes;
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
				<PanelBody title={ __( '背景设置', 'feryfit' ) }>
					<div style={{ marginBottom: '20px', paddingBottom: '20px', borderBottom: '1px solid #eee' }}>
						<h4 style={{ margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }}>{ __( 'PC端背景图片', 'feryfit' ) }</h4>
						<p style={{ margin: '0 0 10px 0', fontSize: '13px', color: '#666' }}>{ __( 'PC端尺寸：宽1400px，高393px', 'feryfit' ) }</p>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( media ) => setAttributes( { 
									backgroundImageId: media.id,
									backgroundImage: media.url 
								} ) }
								allowedTypes={ [ 'image' ] }
								value={ backgroundImageId }
								render={ ( { open } ) => (
									<Button onClick={ open } variant="primary">
										{ backgroundImage ? __( '更换背景图片', 'feryfit' ) : __( '选择背景图片', 'feryfit' ) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
						{ backgroundImage && (
							<Button 
								onClick={ () => setAttributes( { 
									backgroundImageId: 0,
									backgroundImage: '' 
								} ) } 
								variant="secondary" 
								style={{ marginTop: '10px' }}
							>
								{ __( '移除背景图片', 'feryfit' ) }
							</Button>
						) }
					</div>
					<div style={{ marginBottom: '20px' }}>
						<h4 style={{ margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }}>{ __( '移动端背景图片', 'feryfit' ) }</h4>
						<p style={{ margin: '0 0 10px 0', fontSize: '13px', color: '#666' }}>{ __( '移动端尺寸：宽100%，高370px', 'feryfit' ) }</p>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( media ) => setAttributes( { 
									mobileBackgroundImageId: media.id,
									mobileBackgroundImage: media.url 
								} ) }
								allowedTypes={ [ 'image' ] }
								value={ mobileBackgroundImageId }
								render={ ( { open } ) => (
									<Button onClick={ open } variant="primary">
										{ mobileBackgroundImage ? __( '更换移动端背景图片', 'feryfit' ) : __( '选择移动端背景图片', 'feryfit' ) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
						{ mobileBackgroundImage && (
							<Button 
								onClick={ () => setAttributes( { 
									mobileBackgroundImageId: 0,
									mobileBackgroundImage: '' 
								} ) } 
								variant="secondary" 
								style={{ marginTop: '10px' }}
							>
								{ __( '移除移动端背景图片', 'feryfit' ) }
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
					<TextControl
						label={ __( '副标题', 'feryfit' ) }
						value={ subtitle }
						onChange={ ( value ) => setAttributes( { subtitle: value } ) }
						placeholder={ __( '输入副标题...', 'feryfit' ) }
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
				className="warranty-banner"
				style={ { 
					backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'linear-gradient(135deg, #1a1a1a 0%, #2d1810 50%, #c73e1d 100%)',
					backgroundSize: 'cover',
					backgroundPosition: 'center',
					minHeight: '200px',
					padding: '40px',
					borderRadius: '8px',
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'center'
				} }
			>
				<div className="warranty-banner__content" style={{ textAlign: 'center', color: '#fff' }}>
					<RichText
						tagName="h2"
						className="warranty-banner__title"
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						placeholder={ __( '输入标题...', 'feryfit' ) }
						style={{ 
							color: '#fff',
							fontSize: '28px',
							fontWeight: '700',
							marginBottom: '12px',
							marginTop: '0'
						}}
					/>
					<RichText
						tagName="p"
						className="warranty-banner__subtitle"
						value={ subtitle }
						onChange={ ( value ) => setAttributes( { subtitle: value } ) }
						placeholder={ __( '输入副标题...', 'feryfit' ) }
						style={{ 
							color: '#fff',
							fontSize: '14px',
							marginBottom: '24px',
							marginTop: '0',
							opacity: '0.9'
						}}
					/>
					<div className="warranty-banner__button-wrapper">
						<span 
							className="warranty-banner__button"
							style={{
								display: 'inline-block',
								backgroundColor: '#c73e1d',
								color: '#fff',
								padding: '12px 24px',
								borderRadius: '4px',
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
		</div>
	);
}