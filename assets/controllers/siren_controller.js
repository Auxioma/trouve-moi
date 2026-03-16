import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['siren', 'companyName', 'address', 'postalCode', 'city'];
    static values = {
        url: String
    };

    connect() {
        this.timeout = null;
    }

    search() {
        clearTimeout(this.timeout);

        const siren = this.sirenTarget.value.trim();

        if (!/^\d{9}$/.test(siren)) {
            this.clearFields();
            return;
        }

        this.timeout = setTimeout(() => {
            this.fetchCompany(siren);
        }, 400);
    }

    async fetchCompany(siren) {
        try {
            const response = await fetch(`${this.urlValue}?siren=${encodeURIComponent(siren)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                console.warn(result.message || 'Entreprise introuvable');
                this.clearFields();
                return;
            }

            const data = result.data ?? {};

            if (this.hasCompanyNameTarget) {
                this.companyNameTarget.value = data.name ?? '';
            }

            if (this.hasAddressTarget) {
                this.addressTarget.value = data.address ?? '';
            }

            if (this.hasPostalCodeTarget) {
                this.postalCodeTarget.value = data.postal_code ?? '';
            }

            if (this.hasCityTarget) {
                this.cityTarget.value = data.city ?? '';
            }
        } catch (error) {
            console.error('Erreur lors de la recherche SIREN :', error);
            this.clearFields();
        }
    }

    clearFields() {
        if (this.hasCompanyNameTarget) {
            this.companyNameTarget.value = '';
        }

        if (this.hasAddressTarget) {
            this.addressTarget.value = '';
        }

        if (this.hasPostalCodeTarget) {
            this.postalCodeTarget.value = '';
        }

        if (this.hasCityTarget) {
            this.cityTarget.value = '';
        }
    }
}