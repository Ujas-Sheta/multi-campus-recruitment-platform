// Program finder - reloads results from the REST API when filters change.
const debounce = (fn, wait = 300) => {
	let t;
	return (...args) => {
		clearTimeout(t);
		t = setTimeout(() => fn(...args), wait);
	};
};

export default function initProgramFinders(root = document) {
	const data = window.mcrpData || {};
	if (!data.programsEndpoint) return;

	root.querySelectorAll('[data-program-finder]').forEach((finder) => {
		const form = finder.querySelector('[data-finder-form]');
		const results = finder.querySelector('[data-finder-results]');
		const count = finder.querySelector('[data-finder-count]');
		const perPage = parseInt(finder.dataset.perPage, 10) || 12;
		const syncUrl = finder.id === 'program-finder'; // Only the main archive finder owns the URL.
		let page = 1;
		let controller;

		let pager = finder.querySelector('[data-finder-pager]');
		if (!pager) {
			pager = document.createElement('nav');
			pager.className = 'program-finder__pager';
			pager.setAttribute('data-finder-pager', '');
			finder.appendChild(pager);
		}

		const params = () => {
			const p = new URLSearchParams();
			if (form) {
				new FormData(form).forEach((value, key) => value && p.set(key, value));
			} else {
				// Locked finder without a filter bar - nothing to change, but keep paging.
				finder.querySelectorAll('input[type="hidden"]').forEach((i) => i.value && p.set(i.name, i.value));
			}
			return p;
		};

		const renderCount = (total) => {
			const i18n = data.i18n || {};
			count.textContent = total === 0 ? i18n.noResults : total === 1 ? i18n.oneResult : (i18n.results || '%d').replace('%d', total);
		};

		const renderPager = (pages) => {
			pager.innerHTML = '';
			if (page < pages) {
				const btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'btn btn--outline';
				btn.textContent = finder.querySelector('[data-finder-more]')?.textContent || 'Load more programs';
				btn.addEventListener('click', () => load({ append: true }));
				pager.appendChild(btn);
			}
		};

		const load = async ({ append = false } = {}) => {
			controller?.abort();
			controller = new AbortController();
			page = append ? page + 1 : 1;

			const p = params();
			const api = new URL(data.programsEndpoint, window.location.origin);
			p.forEach((v, k) => api.searchParams.set(k, v));
			api.searchParams.set('page', page);
			api.searchParams.set('per_page', perPage);

			results.classList.add('is-loading');
			results.setAttribute('aria-busy', 'true');

			try {
				const res = await fetch(api, { signal: controller.signal, headers: { Accept: 'application/json' } });
				if (!res.ok) throw new Error(res.statusText);
				const json = await res.json();

				if (append) {
					results.insertAdjacentHTML('beforeend', json.html);
				} else {
					results.innerHTML = json.html;
				}
				renderCount(json.total);
				renderPager(json.pages);

				if (syncUrl && form) {
					const url = new URL(window.location.href);
					['q', 'area', 'credential', 'delivery', 'campus', 'pg'].forEach((k) => url.searchParams.delete(k));
					p.forEach((v, k) => url.searchParams.set(k, v));
					window.history.replaceState({}, '', url);
				}
			} catch (err) {
				if (err.name !== 'AbortError') {
					count.textContent = (data.i18n && data.i18n.error) || 'Error';
				}
			} finally {
				results.classList.remove('is-loading');
				results.removeAttribute('aria-busy');
			}
		};

		// Enhance existing "load more" link.
		finder.querySelector('[data-finder-more]')?.addEventListener('click', (e) => {
			e.preventDefault();
			load({ append: true });
		});

		if (!form) return;

		const onChange = debounce(() => load());
		form.addEventListener('input', (e) => e.target.matches('input[type="search"]') && onChange());
		form.addEventListener('change', (e) => e.target.matches('select') && load());
		form.addEventListener('submit', (e) => {
			e.preventDefault();
			load();
		});
		form.querySelector('[data-finder-reset]')?.addEventListener('click', (e) => {
			e.preventDefault();
			form.querySelectorAll('input[type="search"], select').forEach((el) => (el.value = ''));
			load();
		});
	});
}
