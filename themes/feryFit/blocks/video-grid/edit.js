/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { sectionTitle } = attributes;
	const blockProps = useBlockProps();

	const mockVideos = [
		{
			id: 1,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop',
			duration: '2:08',
		},
		{
			id: 2,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 3,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop',
			duration: '2:05',
		},
		{
			id: 4,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 5,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop',
			duration: '2:08',
		},
		{
			id: 6,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 7,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop',
			duration: '2:05',
		},
		{
			id: 8,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 9,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop',
			duration: '2:08',
		},
		{
			id: 10,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=300&h=200&fit=crop',
			duration: '2:06',
		},
		{
			id: 11,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop',
			duration: '2:05',
		},
		{
			id: 12,
			title: '[AR-01] Ultra-clear screen activity tracker with heart rate/sleep......',
			image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=200&fit=crop',
			duration: '2:06',
		},
	];

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('视频网格设置', 'feryfit')} initialOpen={true}>
					<TextControl
						label={__('区块标题', 'feryfit')}
						value={sectionTitle}
						onChange={(value) => setAttributes({ sectionTitle: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<h2 className="video-grid__title">{sectionTitle}</h2>
			<div className="video-grid">
				{mockVideos.map((video) => (
					<div key={video.id} className="video-card">
						<div className="video-card__thumbnail">
							<img
								src={video.image}
								alt={video.title}
								className="video-card__image"
							/>
							<button className="video-card__play-btn" aria-label={__('Play video', 'feryfit')}>
								<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="20" cy="20" r="20" fill="white" opacity="0.9" />
									<path d="M15 12.5L25 20L15 27.5V12.5Z" fill="black" />
								</svg>
							</button>
							<span className="video-card__duration">{video.duration}</span>
						</div>
						<div className="video-card__title">
							{video.title}
						</div>
					</div>
				))}
			</div>
		</div>
	);
}