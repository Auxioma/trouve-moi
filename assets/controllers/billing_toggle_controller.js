import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'toggle',
        'monthlyLabel',
        'yearlyLabel',
        'monthlyPrice',
        'yearlyPrice',
        'monthlyPeriod',
        'yearlyPeriod',
        'monthlyMeta',
        'yearlyMeta',
        'monthlyButton',
        'yearlyButton'
    ];

    static values = {
        default: { type: String, default: 'monthly' }
    };

    connect() {
        this.setBilling(this.defaultValue);
    }

    toggle() {
        const currentMode = this.element.dataset.billingMode || 'monthly';
        this.setBilling(currentMode === 'monthly' ? 'yearly' : 'monthly');
    }

    setMonthly() {
        this.setBilling('monthly');
    }

    setYearly() {
        this.setBilling('yearly');
    }

    setBilling(mode) {
        const isYearly = mode === 'yearly';

        this.monthlyPriceTargets.forEach((element) => {
            element.classList.toggle('d-none', isYearly);
        });

        this.yearlyPriceTargets.forEach((element) => {
            element.classList.toggle('d-none', !isYearly);
        });

        this.monthlyPeriodTargets.forEach((element) => {
            element.classList.toggle('d-none', isYearly);
        });

        this.yearlyPeriodTargets.forEach((element) => {
            element.classList.toggle('d-none', !isYearly);
        });

        this.monthlyMetaTargets.forEach((element) => {
            element.classList.toggle('d-none', isYearly);
        });

        this.yearlyMetaTargets.forEach((element) => {
            element.classList.toggle('d-none', !isYearly);
        });

        this.monthlyButtonTargets.forEach((element) => {
            element.classList.toggle('d-none', isYearly);
        });

        this.yearlyButtonTargets.forEach((element) => {
            element.classList.toggle('d-none', !isYearly);
        });

        this.monthlyLabelTargets.forEach((element) => {
            element.classList.toggle('active', !isYearly);
        });

        this.yearlyLabelTargets.forEach((element) => {
            element.classList.toggle('active', isYearly);
        });

        if (this.hasToggleTarget) {
            this.toggleTarget.classList.toggle('is-yearly', isYearly);
        }

        this.element.dataset.billingMode = mode;
        document.body.setAttribute('data-billing-mode', mode);
    }
}
