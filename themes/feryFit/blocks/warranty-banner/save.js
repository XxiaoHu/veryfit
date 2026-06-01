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
	const { backgroundImage, mobileBackgroundImage, title, subtitle, buttonText, buttonUrl } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<div 
				className="warranty-banner"
				style={ { 
					backgroundImage: backgroundImage ? `url(${backgroundImage})` : undefined 
				} }
			>
				{ mobileBackgroundImage && (
					<div 
						className="warranty-banner__mobile-bg"
						style={ { 
							backgroundImage: `url(${mobileBackgroundImage})` 
						} }
					></div>
				) }
				<div className="warranty-banner__content">
					<RichText.Content 
						tagName="h2" 
						className="warranty-banner__title" 
						value={ title } 
					/>
					<RichText.Content 
						tagName="p" 
						className="warranty-banner__subtitle" 
						value={ subtitle } 
					/>
					<div className="warranty-banner__button-wrapper">
						<a 
							href={ buttonUrl }
							className="warranty-banner__button"
						>
							{ buttonText }
						</a>
					</div>
				</div>
			</div>
		</div>
	);
}
