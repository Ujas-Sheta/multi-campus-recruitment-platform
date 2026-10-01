// Program page: active state for the sticky subnav + show mobile CTA bar after the hero.
export default function initSubnav() {
	const subnav = document.querySelector('[data-subnav]');
	if (subnav && 'IntersectionObserver' in window) {
		const links = [...subnav.querySelectorAll('a[href^="#"]')];
		const map = new Map(links.map((a) => [a.getAttribute('href').slice(1), a]));
		const io = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						links.forEach((l) => l.classList.remove('is-active'));
						const link = map.get(entry.target.id);
						link?.classList.add('is-active');
						link?.scrollIntoView({ block: 'nearest', inline: 'center' });
					}
				});
			},
			{ rootMargin: '-40% 0px -55% 0px' }
		);
		map.forEach((_, id) => {
			const el = document.getElementById(id);
			if (el) io.observe(el);
		});
	}

	const cta = document.querySelector('[data-mobile-cta]');
	const hero = document.querySelector('.page-hero');
	if (cta && hero && 'IntersectionObserver' in window) {
		new IntersectionObserver(([entry]) => cta.classList.toggle('is-visible', !entry.isIntersecting)).observe(hero);
	}
}
