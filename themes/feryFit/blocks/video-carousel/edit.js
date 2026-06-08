/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const { sectionTitle, learnMoreText } = attributes;

	const itemsPerRow = 4;
	const rowsPerSlide = 2;

	const blockProps = useBlockProps( {
		className: 'feryfit-video-carousel',
	} );

	// Video archive URL (for editor preview only, actual URL is handled by PHP render.php)
	const videoArchiveUrl = '/archives/video/';

	const mockVideos = [
		{
			id: 1,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop',
			duration: '2:08',
		},
		{
			id: 2,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 3,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop',
			duration: '2:05',
		},
		{
			id: 4,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 5,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop',
			duration: '2:08',
		},
		{
			id: 6,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 7,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop',
			duration: '2:05',
		},
		{
			id: 8,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			thumbnail: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=200&fit=crop',
			duration: '2:06',
		},
	];

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( '视频轮播设置', 'feryfit' ) } initialOpen={ true }>
					<TextControl
						label={ __( '区块标题', 'feryfit' ) }
						value={ sectionTitle }
						onChange={ ( value ) => setAttributes( { sectionTitle: value } ) }
					/>
					<TextControl
						label={ __( '查看更多按钮文字', 'feryfit' ) }
						value={ learnMoreText }
						onChange={ ( value ) => setAttributes( { learnMoreText: value } ) }
						placeholder="Learn More"
					/>
				</PanelBody>
			</InspectorControls>

			<div className="video-carousel-container">
				<div className="video-carousel-header">
					<h2 className="video-carousel-title">{ sectionTitle }</h2>
					<a href={videoArchiveUrl} className="video-carousel-learn-more" onClick={(e) => e.preventDefault()}>
						{ learnMoreText || 'Learn More' }
						<span className="arrow">&gt;</span>
					</a>
				</div>

				<div className="video-carousel-items">
					{ mockVideos.slice( 0, itemsPerRow * rowsPerSlide ).map( ( video ) => (
						<div key={ video.id } className="video-carousel-item">
							<div className="video-thumbnail-wrapper">
								<img
									src={ video.thumbnail }
									alt={ video.title }
									className="video-thumbnail"
								/>
								<div className="video-duration">{ video.duration }</div>
							</div>
							<div className="video-title">{ video.title }</div>
						</div>
					) ) }
				</div>
			</div>
		</div>
	);
}
