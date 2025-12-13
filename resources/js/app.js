import './bootstrap';

import Alpine from 'alpinejs';

import faqManager from './admin/faq-manager';

window.Alpine = Alpine;

Alpine.start();

window.faqManager = faqManager;