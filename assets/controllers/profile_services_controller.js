import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['servicesContainer', 'activityWrapper'];
    static values = {
        url: String
    };

    connect() {
        this.activityField = this.activityWrapperTarget.querySelector('[name$="[activity]"]');

        if (!this.activityField) {
            return;
        }

        if (this.activityField.value) {
            this.loadServices(this.activityField.value);
        }
    }

    async changeActivity(event) {
        const activityId = event.target.value;

        if (!activityId) {
            this.servicesContainerTarget.innerHTML = '';
            return;
        }

        await this.loadServices(activityId);
    }

    getSelectedValues() {
        return new Set(
            Array.from(
                this.element.querySelectorAll('input[name="profile[services][]"]:checked')
            ).map((element) => element.value)
        );
    }

    async loadServices(activityId) {
        const selectedValues = this.getSelectedValues();

        this.servicesContainerTarget.innerHTML = '';

        try {
            const response = await fetch(
                this.urlValue.replace('__ID__', activityId),
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Erreur AJAX');
            }

            const services = await response.json();

            services.forEach((service) => {
                const col = document.createElement('div');
                col.classList.add('col-6');

                const wrapper = document.createElement('div');
                wrapper.classList.add('form-check');

                const input = document.createElement('input');
                input.type = 'checkbox';
                input.classList.add('form-check-input');
                input.name = 'profile[services][]';
                input.value = service.id;
                input.id = `profile_services_${service.id}`;

                if (selectedValues.has(String(service.id))) {
                    input.checked = true;
                }

                const label = document.createElement('label');
                label.classList.add('form-check-label');
                label.setAttribute('for', input.id);
                label.textContent = service.name;

                wrapper.appendChild(input);
                wrapper.appendChild(label);
                col.appendChild(wrapper);
                this.servicesContainerTarget.appendChild(col);
            });
        } catch (error) {
            console.error('Erreur chargement services:', error);

            this.servicesContainerTarget.innerHTML = `
                <div class="col-12 text-danger">
                    Impossible de charger les services
                </div>
            `;
        }
    }
}
