import { Controller } from 'stimulus';
import axios from 'axios';

export default class extends Controller {
    initialize() {
        console.log('initialize');
        window.addEventListener('popstate', event => {
            const url = event.target.location;
            this.loadFragment(url, false);
        });
    }

    load(event) {
        const id = this.data.get('id');

        if(!id) {
            // if we hit this error, early return an **don't** preventDefault
            throw new Exception('Missing `data-fragment-loader-id` attribute on controller.');
            return;
        }

        event.preventDefault();
        const url = event.target.href;
        this.loadFragment(url);
    }

    async loadFragment(url, pushState = true) {
        const id = this.data.get('id');
        const response = await axios.get(url);
        const selector = `[data-fragment-loader-id="${id}"]`;
        const temp = document.createElement('div');
        temp.innerHTML = response.data;
        const el = temp.querySelector(selector);

        // pre swap event
        const preSwapEvent = new CustomEvent('fragmentpreswap', {
            detail: id
        });
        window.dispatchEvent(preSwapEvent);

        this.element.innerHTML = el.innerHTML;
        if(pushState) window.history.pushState({}, '', url);
        this.updateCSRF(temp);

        // post swap event
        const postSwapEvent = new CustomEvent('fragmentpostswap', {
            detail: id
        });
        window.dispatchEvent(postSwapEvent);
    }

    updateCSRF(responseBody) {
        const csrfInput = responseBody.querySelector('[name="csrf"]');
        if(!csrfInput) return;

        const csrf = csrfInput.value;
        const csrfInputs = Array.from(document.querySelectorAll('[name="csrf"]'));
        if(!csrfInputs) return;
        
        csrfInputs.forEach(input => {
            input.setAttribute('value', csrf);
        });
    }
}