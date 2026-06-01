/**
 * Frontend view script for the hero banner block.
 * Add any interactive functionality here.
 */

function initHeroBanner() {
	const searchForm = document.querySelector('.hero-banner__search-form');
	const searchInput = document.querySelector('.hero-banner__search-input');
	const keywordTags = document.querySelectorAll('.hero-banner__keyword-tag');

	console.log('Hero banner view script loaded');
	console.log('Search form found:', !!searchForm);
	console.log('Search input found:', !!searchInput);
	console.log('Keyword tags found:', keywordTags.length);

	keywordTags.forEach(tag => {
		tag.addEventListener('click', () => {
			const keyword = tag.textContent.trim();
			console.log('Keyword clicked:', keyword);
			
			if (searchInput) {
				searchInput.value = keyword;
			}
			if (searchForm) {
				searchForm.submit();
			}
		});
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initHeroBanner);
} else {
	initHeroBanner();
}
