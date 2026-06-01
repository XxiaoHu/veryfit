/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import './editor.scss';

// Use absolute path for arrow icon
const arrowIcon = '/wp-content/themes/feryFit/assets/images/right.png';

// Helper function to limit text length
const limitTitle = (title, length = 60) => {
	if (title.length > length) {
		return title.substring(0, length) + '...';
	}
	return title;
};

export default function Edit({ attributes, setAttributes }) {
	const { title, learnMoreText, postsPerPage, desktopTopMargin, mobileTopMargin } = attributes;
	const blockProps = useBlockProps();

	// Get FAQ posts - same logic as frontend render.php
	// 排序规则：置顶优先（使用 _faq_is_pinned meta 字段），然后按修改时间降序（最新的在前面）
	const faqs = useSelect((select) => {
		const records = select('core').getEntityRecords('postType', 'faq', {
			per_page: postsPerPage,
			status: 'publish',
			order: 'DESC',
			orderby: 'modified',
		});
		
		if (!records) return records;
		
		// 手动排序：置顶文章优先（使用 meta._faq_is_pinned 字段）
		const stickyFaqs = [];
		const normalFaqs = [];
		
		records.forEach((faq) => {
			const isPinned = faq.meta?._faq_is_pinned === '1';
			if (isPinned) {
				stickyFaqs.push(faq);
			} else {
				normalFaqs.push(faq);
			}
		});
		
		// 按修改时间降序排序
		const sortByModified = (a, b) => {
			return new Date(b.modified) - new Date(a.modified);
		};
		
		stickyFaqs.sort(sortByModified);
		normalFaqs.sort(sortByModified);
		
		// 合并：置顶在前，并限制数量
		return [...stickyFaqs, ...normalFaqs].slice(0, postsPerPage);
	}, [postsPerPage]);

	// Check if we have any real data
	const hasRealData = faqs && faqs.length > 0;
	const isLoading = faqs === undefined;

	return (
		<div {...blockProps} style={{ marginTop: desktopTopMargin + 'px' }}>
			<InspectorControls>
				<PanelBody title={__('FAQ列表设置', 'feryfit')}>
					<TextControl
						label={__('标题', 'feryfit')}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
					/>
					<TextControl
						label={__('Learn More 文本', 'feryfit')}
						value={learnMoreText}
						onChange={(value) => setAttributes({ learnMoreText: value })}
					/>
					<RangeControl
						label={__('显示数量', 'feryfit')}
						value={postsPerPage}
						onChange={(value) => setAttributes({ postsPerPage: value })}
						min={1}
						max={20}
						step={1}
					/>
					<RangeControl
						label={__('PC端上边距 (px)', 'feryfit')}
						value={desktopTopMargin}
						onChange={(value) => setAttributes({ desktopTopMargin: value })}
						min={0}
						max={200}
						step={1}
					/>
					<RangeControl
						label={__('移动端上边距 (px)', 'feryfit')}
						value={mobileTopMargin}
						onChange={(value) => setAttributes({ mobileTopMargin: value })}
						min={0}
						max={200}
						step={1}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="faq-list__preview">
				<div className="faq-list">
					<div className="faq-list__header">
						<h2 className="faq-list__title">{title}</h2>
						<a href="/archives/faq/" className="faq-list__learn-more" onClick={(e) => e.preventDefault()}>
							{learnMoreText}
						</a>
					</div>
					
					<ul className="faq-list__items">
						{isLoading ? (
							// Loading state
							Array.from({ length: Math.min(postsPerPage, 8) }).map((_, i) => (
								<li key={i} className="faq-list__item">
									<a href="#" onClick={(e) => e.preventDefault()}>
										<span className="faq-list__item-number">{i + 1}.</span>
										<span className="faq-list__item-title faq-list__item-title--loading">
											Loading...
										</span>
										<img src={arrowIcon} alt="" className="faq-list__item-arrow" />
									</a>
								</li>
							))
						) : hasRealData ? (
							// Real data
							faqs.map((faq, index) => (
								<li key={faq.id} className="faq-list__item">
									<a href="#" onClick={(e) => e.preventDefault()}>
										<span className="faq-list__item-number">{index + 1}.</span>
										<span className="faq-list__item-title">
											{limitTitle(faq.title.rendered, 60)}
										</span>
										<img src={arrowIcon} alt="" className="faq-list__item-arrow" />
									</a>
								</li>
							))
						) : (
							// Fallback dummy data when no real data exists
							Array.from({ length: Math.min(postsPerPage, 8) }).map((_, i) => (
								<li key={i} className="faq-list__item">
									<a href="#" onClick={(e) => e.preventDefault()}>
										<span className="faq-list__item-number">{i + 1}.</span>
										<span className="faq-list__item-title">
											How can I charge and turn on the watch?
										</span>
										<img src={arrowIcon} alt="" className="faq-list__item-arrow" />
									</a>
								</li>
							))
						)}
					</ul>
				</div>
			</div>
		</div>
	);
}
