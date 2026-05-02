import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    connect() {
        this.refresh();
    }

    select() {
        this.refresh();
    }

    refresh() {
        this.cardTargets.forEach((card) => {
            const input = card.querySelector('.tm-signup-role-input');

            card.classList.toggle('is-selected', input.checked);
        });
    }
}