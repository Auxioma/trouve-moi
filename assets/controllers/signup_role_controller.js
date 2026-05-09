// assets/controllers/signup_role_controller.js

import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['card']

    selectedUrl = null

    connect() {
        const checkedInput = this.element.querySelector('input[name="role_type"]:checked')

        if (checkedInput) {
            this.selectedUrl = checkedInput.dataset.signupRoleUrlParam
        }
    }

    select(event) {
        this.cardTargets.forEach((card) => {
            card.classList.remove('is-selected')
        })

        const card = event.currentTarget.closest('[data-signup-role-target="card"]')

        if (card) {
            card.classList.add('is-selected')
        }

        this.selectedUrl = event.currentTarget.dataset.signupRoleUrlParam
    }

    continue() {
        if (!this.selectedUrl) {
            return
        }

        window.location.href = this.selectedUrl
    }
}