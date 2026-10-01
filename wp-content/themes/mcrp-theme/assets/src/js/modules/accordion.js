// FAQ accordion.
export default function initAccordions(root = document) {
	root.querySelectorAll('[data-accordion]').forEach((accordion) => {
		const triggers = [...accordion.querySelectorAll('.accordion__trigger')];

		triggers.forEach((trigger, index) => {
			trigger.addEventListener('click', () => {
				const expanded = trigger.getAttribute('aria-expanded') === 'true';
				trigger.setAttribute('aria-expanded', String(!expanded));
				document.getElementById(trigger.getAttribute('aria-controls')).hidden = expanded;
			});

			trigger.addEventListener('keydown', (e) => {
				const keys = { ArrowDown: index + 1, ArrowUp: index - 1, Home: 0, End: triggers.length - 1 };
				if (e.key in keys) {
					e.preventDefault();
					triggers[(keys[e.key] + triggers.length) % triggers.length].focus();
				}
			});
		});
	});
}
