// Mobile menu + header shadow on scroll.
export default function initNav() {
	const toggle = document.querySelector('[data-nav-toggle]');
	const nav = document.querySelector('[data-nav]');
	const header = document.querySelector('[data-header]');

	if (toggle && nav) {
		const close = () => {
			toggle.setAttribute('aria-expanded', 'false');
			nav.classList.remove('is-open');
			document.body.classList.remove('nav-open');
		};

		toggle.addEventListener('click', () => {
			const open = toggle.getAttribute('aria-expanded') !== 'true';
			toggle.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
			document.body.classList.toggle('nav-open', open);
			if (open) {
				nav.querySelector('a, input')?.focus();
			}
		});

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				close();
				toggle.focus();
			}
		});

		window.matchMedia('(min-width: 1024px)').addEventListener('change', (mq) => mq.matches && close());
	}

	if (header) {
		const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 40);
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}
}
