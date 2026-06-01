/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

import './editor.scss';

const arrowIcon = '/wp-content/themes/feryFit/assets/images/right.png';

const limitTitle = (title, length = 80) => {
	if (title.length > length) {
		return title.substring(0, length) + '...';
	}
	return title;
};

export default function Edit({ attributes, setAttributes }) {
	const { title, postsPerPage } = attributes;
	const blockProps = useBlockProps();
	
	const [currentPage, setCurrentPage] = useState(1);

	const faqs = useSelect((select) => {
		const coreData = select('core');
		return coreData.getEntityRecords('postType', 'faq', {
			per_page: postsPerPage,
			page: currentPage,
			status: 'publish',
			order: 'ASC',
			orderby: 'menu_order ID',
		});
	}, [postsPerPage, currentPage]);

	const hasRealData = faqs && faqs.length > 0;
	const isLoading = faqs === undefined;
	
	const totalPosts = useSelect((select) => {
		const coreData = select('core');
		const query = coreData.getEntityRecords('postType', 'faq', {
			per_page: 1,
			status: 'publish',
		});
		
		if (query && query._paging) {
			return query._paging.total || query._paging.totalItems || 0;
		}
		
		if (typeof coreData.getTotalEntityRecords === 'function') {
			return coreData.getTotalEntityRecords('postType', 'faq', { status: 'publish' }) || 0;
		}
		
		return hasRealData ? 100 : 0;
	}, [hasRealData]);

	const totalPages = Math.max(1, Math.ceil(totalPosts / postsPerPage));

	const dummyFaqs = [
		'How can I charge and turn on the watch?',
		'First connection?',
		'How to check the model of the watch?',
		'How to sync data?',
		'How to change C to F/ Mile to KM/ Military Time?',
		'How to receive Facebook, SMS, or incoming call notifications on my device?',
		'How to set a walking reminder?',
		'How to use the Music Control feature?',
		'How to set the Weather Reminder?',
		'How to enable the Find Phone feature?',
	];

	const displayedFaqs = isLoading || !hasRealData 
		? dummyFaqs.slice((currentPage - 1) * postsPerPage, currentPage * postsPerPage)
		: faqs;

	const getDisplayedTotal = () => {
		if (isLoading || !hasRealData) {
			return { start: (currentPage - 1) * postsPerPage + 1, end: Math.min(currentPage * postsPerPage, dummyFaqs.length), total: dummyFaqs.length };
		}
		const start = (currentPage - 1) * postsPerPage + 1;
		const end = Math.min(start + postsPerPage - 1, totalPosts);
		return { start, end, total: totalPosts };
	};

	const { start, end, total } = getDisplayedTotal();

	const renderPagination = () => {
		const pages = [];
		const maxVisible = 5;
		let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
		let endPage = Math.min(totalPages, startPage + maxVisible - 1);
		
		if (endPage - startPage + 1 < maxVisible) {
			startPage = Math.max(1, endPage - maxVisible + 1);
		}

		pages.push(
			<button
				key="prev"
				className={`faq-pagination__nav faq-pagination__nav--prev ${currentPage === 1 ? 'faq-pagination__nav--disabled' : ''}`}
				onClick={() => currentPage > 1 && setCurrentPage(currentPage - 1)}
				disabled={currentPage === 1}
			>
				<span className="faq-pagination__arrow">‹</span>
			</button>
		);

		if (startPage > 1) {
			pages.push(
				<button
					key="first"
					className="faq-pagination__page"
					onClick={() => setCurrentPage(1)}
				>
					1
				</button>
			);
			if (startPage > 2) {
				pages.push(<span key="dots1" className="faq-pagination__dots">...</span>);
			}
		}

		for (let i = startPage; i <= endPage; i++) {
			pages.push(
				<button
					key={i}
					className={`faq-pagination__page ${currentPage === i ? 'faq-pagination__page--active' : ''}`}
					onClick={() => setCurrentPage(i)}
				>
					{i}
				</button>
			);
		}

		if (endPage < totalPages) {
			if (endPage < totalPages - 1) {
				pages.push(<span key="dots2" className="faq-pagination__dots">...</span>);
			}
			pages.push(
				<button
					key="last"
					className="faq-pagination__page"
					onClick={() => setCurrentPage(totalPages)}
				>
					{totalPages}
				</button>
			);
		}

		pages.push(
			<button
				key="next"
				className={`faq-pagination__nav faq-pagination__nav--next ${currentPage === totalPages ? 'faq-pagination__nav--disabled' : ''}`}
				onClick={() => currentPage < totalPages && setCurrentPage(currentPage + 1)}
				disabled={currentPage === totalPages}
			>
				<span className="faq-pagination__arrow">›</span>
			</button>
		);

		return pages;
	};

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('FAQ分页列表设置', 'feryfit')}>
					<TextControl
						label={__('标题', 'feryfit')}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
					/>
					<RangeControl
						label={__('每页显示数量', 'feryfit')}
						value={postsPerPage}
						onChange={(value) => {
							setAttributes({ postsPerPage: value });
							setCurrentPage(1);
						}}
						min={1}
						max={20}
						step={1}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="faq-pagination__preview">
				<div className="faq-pagination">
					<div className="faq-pagination__header">
						<h2 className="faq-pagination__title">{title}</h2>
					</div>

					<ul className="faq-pagination__items">
						{isLoading ? (
							Array.from({ length: Math.min(postsPerPage, 10) }).map((_, i) => (
								<li key={i} className="faq-pagination__item">
									<a href="#" onClick={(e) => e.preventDefault()}>
										<span className="faq-pagination__item-number">{(currentPage - 1) * postsPerPage + i + 1}.</span>
										<span className="faq-pagination__item-title faq-pagination__item-title--loading">
											Loading...
										</span>
										<img src={arrowIcon} alt="" className="faq-pagination__item-arrow" />
									</a>
								</li>
							))
						) : (
							displayedFaqs.map((faq, index) => (
								<li key={index} className="faq-pagination__item">
									<a href="#" onClick={(e) => e.preventDefault()}>
										<span className="faq-pagination__item-number">{start + index}.</span>
										<span className="faq-pagination__item-title">
											{typeof faq === 'string' ? faq : limitTitle(faq.title.rendered, 80)}
										</span>
										<img src={arrowIcon} alt="" className="faq-pagination__item-arrow" />
									</a>
								</li>
							))
						)}
					</ul>

					{(totalPages > 1 || (!hasRealData && dummyFaqs.length > postsPerPage)) && (
						<div className="faq-pagination__nav-wrapper">
							<div className="faq-pagination__info">
								{start}-{end} of {total}
							</div>
							<div className="faq-pagination__nav-container">
								{renderPagination()}
							</div>
						</div>
					)}
				</div>
			</div>
		</div>
	);
}