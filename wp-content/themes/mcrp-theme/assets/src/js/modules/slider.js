// Simple scroll-snap slider, just adds prev/next buttons.
export default function initSliders(root = document) {
	root.querySelectorAll('[data-slider]').forEach((track) => {
		const section = track.closest('.mcrp-block') || track.parentElement;
		const prev = section.querySelector('[data-slider-prev]');
		const next = section.querySelector('[data-slider-next]');
		if (!prev || !next) return;

		const step = () => track.querySelector('.slider__slide')?.getBoundingClientRect().width + 20 || track.clientWidth;

		const update = () => {
			const max = track.scrollWidth - track.clientWidth - 2;
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max;
		};

		prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
		next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
		track.addEventListener('scroll', () => window.requestAnimationFrame(update), { passive: true });
		track.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowRight') next.click();
			if (e.key === 'ArrowLeft') prev.click();
		});
		window.addEventListener('resize', update);
		update();
	});
}
