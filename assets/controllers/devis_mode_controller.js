import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'panel'];

    switch(event) {
        const mode = event.currentTarget.dataset.mode;

        this.buttonTargets.forEach((button) => {
            button.classList.remove('active');
        });

        event.currentTarget.classList.add('active');

        this.panelTargets.forEach((panel) => {
            panel.classList.remove('active');
        });

        const targetPanel = this.panelTargets.find((panel) => panel.id === `mode-${mode}`);

        if (targetPanel) {
            targetPanel.classList.add('active');
        }
    }
}
