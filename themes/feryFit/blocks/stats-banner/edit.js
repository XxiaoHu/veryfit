/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { title, subtitle, backgroundImage } = attributes;
	const blockProps = useBlockProps();

	const onSelectImage = (media) => {
		setAttributes({ backgroundImage: media.url });
	};

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
						label={ __( '副标题', 'feryfit' ) }
						value={ subtitle }
						onChange={ ( value ) => setAttributes( { subtitle: value } ) }
						placeholder={ __( '输入副标题...', 'feryfit' ) }
					/>
				</PanelBody>
				<PanelBody title={ __( '背景图片', 'feryfit' ) }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes={ [ 'image' ] }
							value={ backgroundImage }
							render={ ( { open } ) => (
								<Button
									onClick={ open }
									className="components-button is-primary"
								>
									{ backgroundImage ? __( '更换图片', 'feryfit' ) : __( '上传图片', 'feryfit' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ backgroundImage && (
						<div style={ { marginTop: '10px' } }>
							<img 
								src={ backgroundImage } 
								alt="" 
								style={ { maxWidth: '100%', height: 'auto', maxHeight: '100px' } } 
							/>
							<Button
								onClick={ () => setAttributes( { backgroundImage: '' } ) }
								isDestructive
								style={ { marginTop: '10px' } }
							>
								{ __( '移除图片', 'feryfit' ) }
							</Button>
						</div>
					) }
				</PanelBody>
			</InspectorControls>

			<div 
				className="stats-banner"
				style={ {
					background: backgroundImage 
						? `url(${backgroundImage}) center/cover`
						: 'linear-gradient(to right, #d44000 0%, #ff6b35 100%)',
					minHeight: '80px',
					borderRadius: '8px',
					padding: '0 40px'
				} }
			>
				<div className="stats-banner__content" style={{ 
					display: 'flex',
					height: '100%',
					justifyContent: 'center',
					alignItems: 'center',
					gap: '20px'
				}}>
					<RichText
						tagName="div"
						className="stats-banner__title"
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						placeholder={ __( '输入标题...', 'feryfit' ) }
						style={{ 
							color: '#FFF',
							textAlign: 'center',
							fontFamily: 'Inter',
							fontSize: '42px',
							fontStyle: 'normal',
							fontWeight: '700',
							lineHeight: '26px',
							margin: '0'
						}}
					/>
					<RichText
						tagName="div"
						className="stats-banner__subtitle"
						value={ subtitle }
						onChange={ ( value ) => setAttributes( { subtitle: value } ) }
						placeholder={ __( '输入副标题...', 'feryfit' ) }
						style={{ 
							maxWidth: '311px',
							paddingLeft: '20px',
							color: '#FFF',
							fontFamily: 'Inter',
							fontSize: '16px',
							fontStyle: 'normal',
							fontWeight: '400',
							lineHeight: '20px',
							margin: '0'
						}}
					/>
				</div>
			</div>
		</div>
	);
}