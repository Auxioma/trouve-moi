import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    };

    static targets = [
        'siren',
        'companyName',
        'firstName',
        'lastName',
        'address',
        'postalCode',
        'city'
    ];

    async search() {
        const siren = this.sirenTarget.value.trim();

        if (siren.length !== 9) {
            return;
        }

        try {
            const response = await fetch(`${this.urlValue}?siren=${encodeURIComponent(siren)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Réponse serveur invalide :', response.status, text);
                return;
            }

            const data = await response.json();

            if (!data.success) {
                console.error(data.message || 'Erreur inconnue');
                return;
            }

            const company = data.data ?? {};

            if (this.hasCompanyNameTarget) {
                this.companyNameTarget.value = company.companyName ?? '';
            }

            if (this.hasFirstNameTarget) {
                this.firstNameTarget.value = company.firstName ?? '';
            }

            if (this.hasLastNameTarget) {
                this.lastNameTarget.value = company.lastName ?? '';
            }

            if (this.hasAddressTarget) {
                this.addressTarget.value = company.address ?? '';
            }

            if (this.hasPostalCodeTarget) {
                this.postalCodeTarget.value = company.postalCode ?? '';
            }

            if (this.hasCityTarget) {
                this.cityTarget.value = company.city ?? '';
            }

        } catch (error) {
            console.error('Erreur fetch SIREN :', error);
        }
    }
}