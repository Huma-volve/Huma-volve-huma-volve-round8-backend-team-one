import './bootstrap';
import Alpine from 'alpinejs';
import faqManager from './admin/faq-manager';
import { initDoctorChat } from './doctor-chat';

window.initDoctorChat = initDoctorChat;

Alpine.data('faqManager', faqManager);

window.Alpine = Alpine;

Alpine.start();