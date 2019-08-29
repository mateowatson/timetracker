import { Controller } from 'stimulus';
import axios from 'axios';

export default class extends Controller {
    async load(event) {
        const id = this.data.get('id');

        if(!id) {
            // if we hit this error, early return an **don't** preventDefault
            throw new Exception('Missing `data-fragment-loader-id` attribute on controller.');
            return;
        }

        event.preventDefault();

        const url = event.target.href;
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

        // post swap event
        const postSwapEvent = new CustomEvent('fragmentpostswap', {
            detail: id
        });
        window.dispatchEvent(postSwapEvent);
    }
}