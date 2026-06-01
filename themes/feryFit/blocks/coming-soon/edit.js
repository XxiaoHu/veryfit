/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.scss';

const qidaiImage = '/wp-content/themes/feryFit/assets/images/qidai.png';

export default function Edit({ attributes, setAttributes }) {
	const { mainText } = attributes;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('敬请期待设置', 'feryfit')}>
					<TextControl
						label={__('文本内容', 'feryfit')}
						value={mainText}
						onChange={(value) => setAttributes({ mainText: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="coming-soon">
				<div className="coming-soon__icon">
					<img src={qidaiImage} alt="Coming Soon" />
				</div>
				<p className="coming-soon__text">{mainText}</p>
			</div>
		</div>
	);
}
