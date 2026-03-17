import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'preview'];
    static values = {
        defaultImage: String
    };

    connect() {
        this.objectUrl = null;
    }

    openFileDialog() {
        if (this.hasInputTarget) {
            this.inputTarget.click();
        }
    }

    previewFile(event) {
        const file = event.target.files?.[0];

        if (!file) {
            return;
        }

        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
        }

        this.objectUrl = URL.createObjectURL(file);
        this.previewTarget.src = this.objectUrl;
    }

    removePreview() {
        if (this.hasInputTarget) {
            this.inputTarget.value = '';
        }

        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
            this.objectUrl = null;
        }

        this.previewTarget.src = this.defaultImageValue;
    }

    disconnect() {
        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
        }
    }
}