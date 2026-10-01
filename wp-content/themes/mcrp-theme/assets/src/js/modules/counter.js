// Count-up animation for the stats block.
export default function initCounters(root = document) {
	const counters = root.querySelectorAll('[data-count-to]');
	if (!counters.length) return;

	const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const format = (n, decimals) => n.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });

	const run = (el) => {
		const target = parseFloat(el.dataset.countTo);
		const decimals = target % 1 === 0 ? 0 : 1;
		if (reduce) {
			el.textContent = format(target, decimals);
			return;
		}
		const duration = 1600;
		const start = performance.now();
		const tick = (now) => {
			const p = Math.min((now - start) / duration, 1);
			const eased = 1 - Math.pow(1 - p, 3);
			el.textContent = format(target * eased, decimals);
			if (p < 1) requestAnimationFrame(tick);
		};
		requestAnimationFrame(tick);
	};

	if (!('IntersectionObserver' in window)) {
		counters.forEach(run);
		return;
	}

	const io = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					run(entry.target);
					io.unobserve(entry.target);
				}
			});
		},
		{ threshold: 0.4 }
	);

	// not resetting to 0 up front - otherwise anything that never scrolls shows 0
	counters.forEach((el) => io.observe(el));
}
