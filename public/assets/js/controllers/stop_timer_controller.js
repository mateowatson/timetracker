import { Controller } from 'stimulus';

export default class extends Controller {
  static targets = ['spinner', 'stop'];

  stopped() {
    this.stopTarget.disabled = true;
    this.spinnerTarget.classList.remove('d-none');
  }
}