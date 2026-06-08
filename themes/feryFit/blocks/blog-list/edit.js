/**
 * Retrieves the translation of text.
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import './editor.scss';

// Fixed categories
const CATEGORIES = [
	{
		name: 'Setup & Pairing',
		slug: 'setup-pairing',
		tagClass: 'blog-list__category-tag--red',
		iconSrc: '/wp-content/themes/feryFit/assets/images/peidui2.png',
	},
	{
		name: 'Daily Use',
		slug: 'daily-use',
		tagClass: 'blog-list__category-tag--black',
		iconSrc: '/wp-content/themes/feryFit/assets/images/log1.png',
	},
];

// Helper function to limit text length
const limitTitle = (title, length = 30) => {
	if (title.length > length) {
		return title.substring(0, length) + '...';
	}
	return title;
};

export default function Edit({ attributes, setAttributes }) {
	const { desktopTopMargin, mobileTopMargin, postsPerCategory, category1Title, category2Title } = attributes;
	const blockProps = useBlockProps();

	// Fixed categories with dynamic titles
	const CATEGORIES = [
		{
			name: category1Title || 'Setup & Pairing',
			slug: 'setup-pairing',
			tagClass: 'blog-list__category-tag--red',
			iconSrc: '/wp-content/themes/feryFit/assets/images/peidui2.png',
		},
		{
			name: category2Title || 'Daily Use',
			slug: 'daily-use',
			tagClass: 'blog-list__category-tag--black',
			iconSrc: '/wp-content/themes/feryFit/assets/images/log1.png',
		},
	];

	// Get blog posts for each category using correct REST API params
	const postsByCategory = useSelect((select) => {
		const posts = {};
		const allPosts = select('core').getEntityRecords('postType', 'blog', {
			per_page: postsPerCategory * 2,
			status: 'publish',
		});

		if (!allPosts) {
			return {};
		}

		CATEGORIES.forEach((cat) => {
			posts[cat.slug] = [];
		});

		// Filter posts by category manually
		allPosts.forEach((post) => {
			const categories = post.blog_category || [];
			CATEGORIES.forEach((cat) => {
				if (categories.includes(cat.slug)) {
					if (posts[cat.slug].length < postsPerCategory) {
						posts[cat.slug].push(post);
					}
				}
			});
		});

		return posts;
	}, [postsPerCategory]);

	// Check if we have any real data
	const hasRealData = Object.values(postsByCategory).some(posts => posts && posts.length > 0);

	return (
		<div {...blockProps} style={{ marginTop: desktopTopMargin + 'px' }}>
			<InspectorControls>
				<PanelBody title={__('博客列表设置', 'feryfit')}>
					<TextControl
						label={__('第一个分类标题', 'feryfit')}
						value={category1Title}
						onChange={(value) => setAttributes({ category1Title: value })}
						placeholder="Setup & Pairing"
					/>
					<TextControl
						label={__('第二个分类标题', 'feryfit')}
						value={category2Title}
						onChange={(value) => setAttributes({ category2Title: value })}
						placeholder="Daily Use"
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
					<RangeControl
						label={__('每个分类显示文章数', 'feryfit')}
						value={postsPerCategory}
						onChange={(value) => setAttributes({ postsPerCategory: value })}
						min={1}
						max={12}
						step={1}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="blog-list__preview">
				<div className="blog-list__container">
					{CATEGORIES.map((category) => {
						const posts = postsByCategory[category.slug] || [];
						const useDummyData = !hasRealData || posts.length === 0;

						return (
							<div key={category.slug} className="blog-list__card">
								<div className="blog-list__card-header">
									<span className={`blog-list__category-tag ${category.tagClass}`}>
										{category.name}
									</span>
									<span className="blog-list__card-icon">
										<img 
											src={category.iconSrc} 
											alt="" 
											onError={(e) => {
												e.target.style.display = 'none';
											}}
										/>
									</span>
								</div>
								<ul className="blog-list__items">
									{useDummyData ? (
										// Fallback dummy data
										Array.from({ length: Math.min(postsPerCategory, 6) }).map((_, i) => (
											<li key={i} className="blog-list__item">
												<span className="blog-list__item-title">
													How does the FANY Luna smartwatch measure ................
												</span>
												<span className="blog-list__item-date">2025-11-18</span>
												<span className="blog-list__item-arrow">›</span>
											</li>
										))
									) : (
										posts.map((post) => (
											<li key={post.id} className="blog-list__item">
												<a href="#" onClick={(e) => e.preventDefault()}>
													<span className="blog-list__item-title">
														{limitTitle(post.title.rendered, 30)}
													</span>
													<span className="blog-list__item-date">
														{new Date(post.date).toLocaleDateString('zh-CN')}
													</span>
													<span className="blog-list__item-arrow">›</span>
												</a>
											</li>
										))
									)}
								</ul>
							</div>
						);
					})}
				</div>
			</div>
		</div>
	);
}
