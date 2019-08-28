import { Controller } from 'stimulus';

export default class extends Controller {
    submit(event) {
        event.preventDefault();

        const data = new URLSearchParams();
        for (const pair of new FormData(this.element)) {
            data.append(pair[0], pair[1]);
        }

        fetch(this.element.getAttribute('action'), {
            method: 'post',
            body: data,
        })
        .then(res => res.text())
        .then(text => {
            if(!text) throw new Exception('Submission failed.');
            // perform a body swap
            document.querySelector('body').innerHTML = text;
        });
    }
}