export default function faqManager(config) {
    return {
        isModalOpen: false,
        isEditMode: false,
        activeLang: 'en',
        formAction: config.routes.store,
        reorderStatus: '',
        formData: {
            question: { en: '', ar: '' },
            answer: { en: '', ar: '' },
            is_active: true
        },

        init() {
            window.addEventListener('open-faq-modal', () => this.openCreateModal());
        },

        openCreateModal() {
            this.isEditMode = false;
            this.formAction = config.routes.store;
            this.formData = { question: { en: '', ar: '' }, answer: { en: '', ar: '' }, is_active: true };
            this.isModalOpen = true;
        },

        editFaq(faq) {
            this.isEditMode = true;
            this.formAction = `${config.routes.update}/${faq.id}`;
            
            this.formData = {
                question: { en: faq.question.en || '', ar: faq.question.ar || '' },
                answer: { en: faq.answer.en || '', ar: faq.answer.ar || '' },
                is_active: Boolean(faq.is_active)
            };
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        draggedItem: null,

        dragStart(event) {
            this.draggedItem = event.target;
            event.dataTransfer.effectAllowed = 'move';
            event.target.classList.add('opacity-50');
        },

        dragOver(event) {
            event.preventDefault();
            return false;
        },

        drop(event) {
            event.stopPropagation();
            this.draggedItem.classList.remove('opacity-50');
            
            let targetItem = event.target.closest('li');
            
            if (this.draggedItem !== targetItem && targetItem) {
                let list = this.draggedItem.parentNode;
                const bounding = targetItem.getBoundingClientRect();
                const offset = bounding.y + (bounding.height / 2);
                
                if (event.clientY - offset > 0) {
                    targetItem.after(this.draggedItem);
                } else {
                    targetItem.before(this.draggedItem);
                }

                this.saveOrder();
            }
            return false;
        },

        saveOrder() {
            let items = document.querySelectorAll('#faq-list li');
            let orderData = [];
            
            items.forEach((item, index) => {
                orderData.push({
                    id: item.getAttribute('data-id'),
                    order: index + 1
                });
            });

            fetch(config.routes.reorder, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
                },
                body: JSON.stringify({ order: orderData })
            })
            .then(response => response.json())
            .then(data => {
                this.reorderStatus = data.message || 'Order saved';
                setTimeout(() => this.reorderStatus = '', 3000);
            })
            .catch(error => console.error(error));
        }
    }
}