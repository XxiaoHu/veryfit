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
	const { iconUrl, title, buttonText, buttonUrl } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<div className="support-banner">
				<div className="support-banner__icon-wrapper">
					{ iconUrl && (
						<img 
							src={ iconUrl } 
							alt=""
							className="support-banner__icon"
						/>
					) }
				</div>
				<div className="support-banner__content">
					<RichText.Content 
						tagName="h3" 
						className="support-banner__title" 
						value={ title } 
					/>
					<div className="support-banner__button-wrapper">
						<a 
							href={ buttonUrl }
							className="support-banner__button"
						>
							{ buttonText }
						</a>
					</div>
				</div>
			</div>
		</div>
	);
}
