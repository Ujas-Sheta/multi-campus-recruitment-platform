// Save UTM params on the first visit (30 day cookie) so if someone fills in
// a form later on another page we still know which campaign they came from.
const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
const COOKIE = 'mcrp_utm';

const readCookie = () => {
	const match = document.cookie.match(new RegExp(`(?:^|; )${COOKIE}=([^;]*)`));
	if (!match) return {};
	try {
		return JSON.parse(decodeURIComponent(match[1]));
	} catch (e) {
		return {};
	}
};

export function captureUtm() {
	const params = new URLSearchParams(window.location.search);
	const incoming = {};
	KEYS.forEach((k) => params.get(k) && (incoming[k] = params.get(k).slice(0, 120)));

	if (Object.keys(incoming).length && !readCookie().utm_source) {
		incoming.landing_page = window.location.href.split('#')[0];
		incoming.referrer = document.referrer;
		const value = encodeURIComponent(JSON.stringify(incoming));
		document.cookie = `${COOKIE}=${value}; path=/; max-age=${60 * 60 * 24 * 30}; SameSite=Lax${location.protocol === 'https:' ? '; Secure' : ''}`;
	}
}

export function getAttribution() {
	return readCookie();
}
