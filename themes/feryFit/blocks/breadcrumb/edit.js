/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The edit function describes the structure of your block in the context of the editor.
 * This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<div className="breadcrumb">
				<span className="breadcrumb__item">
					<a href="/" className="breadcrumb__link">Home</a>
				</span>
				<span className="breadcrumb__separator">/</span>
				<span className="breadcrumb__item">
					<a href="#" className="breadcrumb__link">Help Center</a>
				</span>
				<span className="breadcrumb__separator">/</span>
				<span className="breadcrumb__item breadcrumb__item--current">
					FAQ
				</span>
			</div>
		</div>
	);
}
