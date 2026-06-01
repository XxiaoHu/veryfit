/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { backgroundImageId, backgroundImage, mobileBackgroundImageId, mobileBackgroundImage, title } = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( '背景设置', 'feryfit' ) }>
					<div style={{ marginBottom: '20px', paddingBottom: '20px', borderBottom: '1px solid #eee' }}>
						<h4 style={{ margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }}>{ __( 'PC端背景图片', 'feryfit' ) }</h4>
						<p style={{ margin: '0 0 10px 0', fontSize: '13px', color: '#666' }}>{ __( 'PC端尺寸：宽100%，高410px', 'feryfit' ) }</p>
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
							<Button onClick={ () => setAttributes( { 
								backgroundImageId: 0,
								backgroundImage: '' 
							} ) } variant="secondary" style={{ marginTop: '10px' }}>
								{ __( '移除背景图片', 'feryfit' ) }
							</Button>
						) }
					</div>
					<div style={{ marginBottom: '20px', paddingBottom: '20px', borderBottom: '1px solid #eee' }}>
						<h4 style={{ margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }}>{ __( '移动端背景图片', 'feryfit' ) }</h4>
						<p style={{ margin: '0 0 10px 0', fontSize: '13px', color: '#666' }}>{ __( '移动端尺寸：宽100%，高400px', 'feryfit' ) }</p>
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
							<Button onClick={ () => setAttributes( { 
								mobileBackgroundImageId: 0,
								mobileBackgroundImage: '' 
							} ) } variant="secondary" style={{ marginTop: '10px' }}>
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
				</PanelBody>
			</InspectorControls>

			{ !backgroundImage && (
				<div className="custom-banner__upload-prompt">
					<p>{ __( '请在右侧面板中选择背景图片', 'feryfit' ) }</p>
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
									{ __( '选择背景图片', 'feryfit' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
				</div>
			) }

			{ backgroundImage && (
				<div 
					className="custom-banner__background"
					style={ { backgroundImage: `url(${backgroundImage})` } }
				>
					<div className="custom-banner__content">
						<RichText
							tagName="h2"
							className="custom-banner__title"
							value={ title }
							onChange={ ( value ) => setAttributes( { title: value } ) }
							placeholder={ __( '输入标题...', 'feryfit' ) }
						/>
					</div>
				</div>
			) }
		</div>
	);
}
