import { Controller } from 'stimulus';
import qs from 'qs';
import axios from 'axios';
import flatpickr from "flatpickr";

export default class extends Controller {
    async submit(event) {
        event.preventDefault();

        let data = {};
        for (const pair of new FormData(this.element)) {
            data[pair[0]] = pair[1];
        }

        data = qs.stringify(data);

        const url = this.element.getAttribute('action');
        const config = { headers: 'content-type' }
        const response = await axios.post(this.element.getAttribute('action'), data);

        // perform a body swap
        document.querySelector('body').innerHTML = response.data;

        flatpickr("#rd", {
            mode: "range",
            dateFormat: "m/d/Y",
            allowInput: true,
        });
    }
}