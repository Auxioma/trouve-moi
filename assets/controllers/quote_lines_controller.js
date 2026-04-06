import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['template', 'container'];

    connect() {
        this.lineIndex = 1;
    }

    addLine() {
        const newLine = this.templateTarget.cloneNode(true);

        newLine.removeAttribute('data-quote-lines-target');

        this.lineIndex++;

        const lineNumber = newLine.querySelector('.line-number');
        if (lineNumber) {
            lineNumber.textContent = this.lineIndex;
        }

        newLine.querySelectorAll('input').forEach((input) => {
            const name = input.getAttribute('name');

            if (name) {
                input.setAttribute(
                    'name',
                    name.replace(/\[\d+\]/, `[${this.lineIndex - 1}]`)
                );
            }

            input.value = '';
        });

        this.containerTarget.appendChild(newLine);
    }
}
