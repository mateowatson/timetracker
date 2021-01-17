import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ 'elapsed', 'refresh' ]

    connect() {
        // get initial elapsed time if available
        const initialElapsed = this.elapsedTimeToMs(this.data.get('elapsed'));

        // start the automatic timer display
        const timer = requestAnimationFrame(this.loop.bind(this));
        this.data.set('timer', timer);

        // keep a reference to the start time
        const startTime = Date.now() - initialElapsed;
        this.data.set('start-time', startTime);

        // hide the refresh link
        this.refreshTarget.style.display = 'none';

        // update the favicon
        const favicon = document.querySelector('[rel=icon][type="image/svg+xml"]');
        favicon.href = '/assets/images/favicon-on.svg';
    }

    disconnect() {
        // stop the animation loop
        const timer = parseInt(this.data.get('timer'));
        cancelAnimationFrame(timer);

        // restore the favicon
        const favicon = document.querySelector('[rel=icon][type="image/svg+xml"]');
        favicon.href = '/assets/images/favicon.svg';
    }

    displayElapsedTime(ms) {
        // convert the elapsed milliseconds into seconds
        const totalInSeconds = ms / 1000;
        // how many seconds to display
        const s = parseInt(totalInSeconds % 60);
        // how many minutes to display
        const m = Math.floor(totalInSeconds / 60) % 60;
        // how many hours to display
        const h = Math.floor(totalInSeconds / 60 / 60);

        // convert to the string format: HH:MM:SS
        const displayTime = `${this.pad(h)}:${this.pad(m)}:${this.pad(s)}`;
        // display the string
        this.elapsedTarget.innerHTML = displayTime;
    }

    loop(ms) {
        // the loop keeps running itself; replace the reference each time
        const timer = requestAnimationFrame(this.loop.bind(this));
        this.data.set('timer', timer);

        // get the elapsed time in milliseconds
        const now = Date.now();
        const startTime = parseInt(this.data.get('start-time'));
        const elapsed = now - startTime;

        // convert to human friendly format and display it
        this.displayElapsedTime(elapsed);
    }

    elapsedTimeToMs(timeString) {
        if (!timeString) {
            return 0;
        }

        const parts = timeString.split(':').map(part => parseInt(part));
        return (parts[0] * 60 * 60 * 1000) + (parts[1] * 60 * 1000) + parts[2] * 1000;
    }

    // convert number to string and ensure it is at least two characters long
    pad(num) {
        num = num.toString();

        if (num.length < 2) {
            num = `0${num}`;
        }

        return num;
    }
}