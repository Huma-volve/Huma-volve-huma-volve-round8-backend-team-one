import './bootstrap';

import Alpine from 'alpinejs';
import faqManager from './admin/faq-manager';

Alpine.data('faqManager', faqManager);

window.Alpine = Alpine;

Alpine.start();