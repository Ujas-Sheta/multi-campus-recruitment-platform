// Request info form - validation + ajax submit.
import { getAttribution } from './utm';

export default function initInquiryForms(root = document) {
	const data = window.mcrpData || {};

	root.querySelectorAll('[data-inquiry-form]').forEach((form) => {
		const wrap = form.closest('.inquiry');
		const errorBox = form.querySelector('[data-inquiry-error]');
		const success = wrap.querySelector('[data-inquiry-success]');
		const submit = form.querySelector('[data-inquiry-submit]');
		const attribution = getAttribution();
		const params = new URLSearchParams(window.location.search);

		// url params win over the cookie
		form.querySelectorAll('[data-utm]').forEach((input) => {
			input.value = params.get(input.dataset.utm) || attribution[input.dataset.utm] || '';
		});
		form.querySelector('[data-landing]').value = attribution.landing_page || window.location.href.split('#')[0];
		form.querySelector('[data-referrer]').value = attribution.referrer || document.referrer;

		// time trap starts on first focus
		const started = form.querySelector('[data-started]');
		form.addEventListener('focusin', () => !started.value && (started.value = String(Date.now() - 1)), { once: true });

		const showError = (msg) => {
			errorBox.textContent = msg;
			errorBox.hidden = !msg;
		};

		const validate = () => {
			let firstInvalid = null;
			form.querySelectorAll('[required]').forEach((field) => {
				const ok = field.type === 'email' ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim()) : field.value.trim() !== '';
				field.setAttribute('aria-invalid', String(!ok));
				if (!ok && !firstInvalid) firstInvalid = field;
			});
			if (firstInvalid) {
				firstInvalid.focus();
				const label = form.querySelector(`label[for="${firstInvalid.id}"]`);
				showError(`${(label?.textContent || '').replace('*', '').trim()}: ${firstInvalid.validationMessage || 'required'}`);
				return false;
			}
			return true;
		};

		form.addEventListener('submit', async (e) => {
			if (!data.inquiryEndpoint || !window.fetch) return; // Fallback to normal POST.
			e.preventDefault();
			showError('');
			if (!validate()) return;

			const label = submit.innerHTML;
			submit.classList.add('is-loading');
			submit.textContent = (data.i18n && data.i18n.sending) || 'Sending...';

			try {
				const payload = Object.fromEntries(new FormData(form).entries());
				const res = await fetch(data.inquiryEndpoint, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
					body: JSON.stringify(payload),
				});
				const json = await res.json();
				if (!res.ok) throw new Error(json.message || (data.i18n && data.i18n.error));

				// for GTM
				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({ event: 'generate_lead', form_campaign: payload.campaign || '', program_id: payload.program_id || '' });

				if (form.dataset.redirect) {
					window.location.assign(form.dataset.redirect);
					return;
				}
				form.hidden = true;
				if (json.message) success.querySelector('p').textContent = json.message;
				success.hidden = false;
				success.setAttribute('tabindex', '-1');
				success.focus();
			} catch (err) {
				showError(err.message || 'Error');
			} finally {
				submit.classList.remove('is-loading');
				submit.innerHTML = label;
			}
		});

		form.addEventListener('input', (e) => e.target.getAttribute('aria-invalid') === 'true' && e.target.setAttribute('aria-invalid', 'false'));
	});
}
