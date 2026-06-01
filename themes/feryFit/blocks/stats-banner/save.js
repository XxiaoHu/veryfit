/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

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
	const { title, subtitle, backgroundImage } = attributes;
	const blockProps = useBlockProps.save();

	const backgroundStyle = backgroundImage
		? `url(${backgroundImage}) center/cover`
		: 'linear-gradient(to right, #d44000 0%, #ff6b35 100%)';

	return (
		<div { ...blockProps }>
			<div 
				className="stats-banner"
				style={ { background: backgroundStyle } }
			>
				<div className="stats-banner__content">
					<div className="stats-banner__title">
						<RichText.Content value={ title } />
					</div>
					<div className="stats-banner__subtitle">
						<RichText.Content value={ subtitle } />
					</div>
				</div>
			</div>
		</div>
	);
}