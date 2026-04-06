import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.scrollToBottom();

        requestAnimationFrame(() => {
            this.scrollToBottom();
        });

        setTimeout(() => {
            this.scrollToBottom();
        }, 50);

        setTimeout(() => {
            this.scrollToBottom();
        }, 150);
    }

    scrollToBottom() {
        this.element.scrollTop = this.element.scrollHeight;
    }
}
