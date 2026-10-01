// Entry point - bundled with esbuild into assets/dist/main.js
import initNav from './modules/nav';
import initAccordions from './modules/accordion';
import initTabs from './modules/tabs';
import initSliders from './modules/slider';
import initCounters from './modules/counter';
import initProgramFinders from './modules/program-finder';
import { captureUtm } from './modules/utm';
import initInquiryForms from './modules/inquiry-form';
import initSubnav from './modules/subnav';

document.documentElement.classList.add('js');

const ready = (fn) => (document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn));

ready(() => {
	captureUtm();
	initNav();
	initAccordions();
	initTabs();
	initSliders();
	initCounters();
	initProgramFinders();
	initInquiryForms();
	initSubnav();
});
