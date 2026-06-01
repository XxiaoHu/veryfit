/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
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
	const { backgroundImage, mobileBackgroundImage, title } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<div 
				className="custom-banner__background"
				style={ { backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'none' } }
			>
				{ mobileBackgroundImage && (
					<div 
						className="custom-banner__mobile-background"
						style={ { backgroundImage: `url(${mobileBackgroundImage})` } }
					></div>
				) }
				<div className="custom-banner__content">
					<RichText.Content tagName="h2" className="custom-banner__title" value={ title } />
				</div>
			</div>
		</div>
	);
}
