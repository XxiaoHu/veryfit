/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, RangeControl, TextControl } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { backgroundImageId, backgroundImage, mobileBackgroundImageId, mobileBackgroundImage, title, subtitle, keywords, bannerWidth, bannerHeight, searchPlaceholder, searchButtonText } = attributes;
	const blockProps = useBlockProps();
	const [newKeyword, setNewKeyword] = useState('');

	const addKeyword = () => {
		if (newKeyword.trim()) {
			setAttributes({ keywords: [...keywords, newKeyword.trim()] });
			setNewKeyword('');
		}
	};

	const removeKeyword = (index) => {
		const newKeywords = [...keywords];
		newKeywords.splice(index, 1);
		setAttributes({ keywords: newKeywords });
	};

	const handleKeyPress = (e) => {
		if (e.key === 'Enter') {
			e.preventDefault();
			addKeyword();
		}
	};

	return (
		<div { ...blockProps } style={ { ...blockProps.style, maxWidth: bannerWidth + 'px', height: bannerHeight + 'px' } }>
			<InspectorControls>
				<PanelBody title={ __( '背景设置', 'feryfit' ) }>
					<div style={{ marginBottom: '20px', paddingBottom: '20px', borderBottom: '1px solid #eee' }}>
						<h4 style={{ margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }}>{ __( 'PC端背景图片', 'feryfit' ) }</h4>
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
						<p style={{ margin: '0 0 10px 0', fontSize: '13px', color: '#666' }}>{ __( '移动端尺寸：宽100%，高524px', 'feryfit' ) }</p>
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
					<TextControl
						label={ __( 'PC端横幅宽度 (px)', 'feryfit' ) }
						type="number"
						value={ bannerWidth }
						onChange={ ( value ) => setAttributes( { bannerWidth: parseInt( value, 10 ) || 1940 } ) }
						style={{ marginTop: '10px' }}
					/>
					<TextControl
						label={ __( 'PC端横幅高度 (px)', 'feryfit' ) }
						type="number"
						value={ bannerHeight }
						onChange={ ( value ) => setAttributes( { bannerHeight: parseInt( value, 10 ) || 480 } ) }
					/>
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

				<PanelBody title={ __( '搜索框设置', 'feryfit' ) }>
					<TextControl
						label={ __( '搜索框占位符', 'feryfit' ) }
						value={ searchPlaceholder }
						onChange={ ( value ) => setAttributes( { searchPlaceholder: value } ) }
						placeholder={ __( '输入搜索框占位符...', 'feryfit' ) }
					/>
					<TextControl
						label={ __( '搜索按钮文字', 'feryfit' ) }
						value={ searchButtonText }
						onChange={ ( value ) => setAttributes( { searchButtonText: value } ) }
						placeholder={ __( '输入搜索按钮文字...', 'feryfit' ) }
					/>
				</PanelBody>

				<PanelBody title={ __( '关键词设置', 'feryfit' ) }>
					<div style={{ display: 'flex', gap: '10px', marginBottom: '10px' }}>
						<input
							type="text"
							value={ newKeyword }
							onChange={ ( e ) => setNewKeyword( e.target.value ) }
							onKeyPress={ handleKeyPress }
							placeholder={ __( '添加关键词...', 'feryfit' ) }
							style={{ padding: '8px 12px', flex: 1, borderRadius: '4px', border: '1px solid #ccc' }}
						/>
						<Button onClick={ addKeyword } variant="primary">
							{ __( '添加', 'feryfit' ) }
						</Button>
					</div>
					{ keywords.length > 0 && (
						<div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
							{ keywords.map( ( keyword, index ) => (
								<span 
									key={ index } 
									style={{ 
										display: 'inline-flex', 
										alignItems: 'center', 
										gap: '6px',
										padding: '4px 10px',
										background: '#f0f0f0',
										borderRadius: '12px',
										fontSize: '13px'
									}}
								>
									{ keyword }
									<button
										onClick={ () => removeKeyword( index ) }
										style={{
											background: '#ddd',
											border: 'none',
											borderRadius: '50%',
											width: '16px',
											height: '16px',
											display: 'flex',
											alignItems: 'center',
											justifyContent: 'center',
											cursor: 'pointer',
											fontSize: '12px',
											lineHeight: '1',
											padding: '0'
										}}
									>
										×
									</button>
								</span>
							) ) }
						</div>
					) }
				</PanelBody>
			</InspectorControls>

			{ !backgroundImage && (
				<div className="hero-banner__upload-prompt">
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
				<div className="hero-banner__background"
					style={ { backgroundImage: `url(${backgroundImage})` } }>
					<div className="hero-banner__content">
						<h1 className="hero-banner__title">{ title || __( '标题', 'feryfit' ) }</h1>
						<p className="hero-banner__subtitle">{ subtitle || __( '副标题', 'feryfit' ) }</p>
						<form className="hero-banner__search-form" role="search">
							<span className="hero-banner__search-icon">
								<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
									<circle cx="11" cy="11" r="8"></circle>
									<path d="m21 21-4.35-4.35"></path>
								</svg>
							</span>
							<input
								type="search"
								className="hero-banner__search-input"
								placeholder={ searchPlaceholder }
								disabled
							/>
							<button type="submit" className="hero-banner__search-button" disabled>
								{ searchButtonText }
							</button>
						</form>
						{ keywords.length > 0 && (
							<div className="hero-banner__keywords-list">
								{ keywords.map( ( keyword, index ) => (
									<span key={ index } className="hero-banner__keyword-tag">
										{ keyword }
									</span>
								) ) }
							</div>
						) }
						
					</div>
				</div>
			) }
		</div>
	);
}