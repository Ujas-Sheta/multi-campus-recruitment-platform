// Tabs (ARIA tabs pattern).
export default function initTabs(root = document) {
	root.querySelectorAll('[data-tabs]').forEach((wrap) => {
		const tabs = [...wrap.querySelectorAll('[role="tab"]')];

		const select = (tab) => {
			tabs.forEach((t) => {
				const selected = t === tab;
				t.setAttribute('aria-selected', String(selected));
				t.tabIndex = selected ? 0 : -1;
				document.getElementById(t.getAttribute('aria-controls')).hidden = !selected;
			});
			tab.focus();
		};

		tabs.forEach((tab, i) => {
			tab.addEventListener('click', () => select(tab));
			tab.addEventListener('keydown', (e) => {
				if (e.key === 'ArrowRight') select(tabs[(i + 1) % tabs.length]);
				if (e.key === 'ArrowLeft') select(tabs[(i - 1 + tabs.length) % tabs.length]);
			});
		});
	});
}
