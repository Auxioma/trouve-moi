import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = [
        'radio',
        'autreWrapper',
        'autreButton'
    ];

    connect() {
        this.updateActive();
    }

    toggleAutre() {
        this.autreWrapperTarget.classList.add('d-none');

        const autreRadio = this.autreButtonTarget.querySelector('input');

        if (autreRadio) {
            autreRadio.checked = false;
        }

        this.updateActive();
    }

    toggleAutreSelect(event) {

        this.radioTargets.forEach((radio) => {
            radio.checked = false;
        });

        if (event.currentTarget.checked) {
            this.autreWrapperTarget.classList.remove('d-none');
        } else {
            this.autreWrapperTarget.classList.add('d-none');
        }

        this.updateActive();
    }

    updateActive() {

        this.element.querySelectorAll('.tm-choice-grid label').forEach((label) => {
            label.classList.remove('active');
        });

        this.radioTargets.forEach((radio) => {
            if (radio.checked) {
                radio.closest('label')?.classList.add('active');
            }
        });

        const autreRadio = this.autreButtonTarget.querySelector('input');

        if (autreRadio?.checked) {
            this.autreButtonTarget.classList.add('active');
        }
    }
}
