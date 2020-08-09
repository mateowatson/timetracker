import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ 'elapsed', 'refresh' ]

    connect() {
        // start the automatic timer display
        const timer = requestAnimationFrame(this.loop.bind(this));
        this.data.set('timer', timer);

        // keep a reference to the start time
        const startTime = Date.now();
        this.data.set('start-time', startTime);

        // hide the refresh link
        this.refreshTarget.style.display = 'none';
    }

    disconnect() {
        // stop the animation loop
        const timer = parseInt(this.data.get('timer'));
        cancelAnimationFrame(timer);
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

    // convert number to string and ensure it is at least two characters long
    pad(num) {
        num = num.toString();

        if (num.length < 2) {
            num = `0${num}`;
        }

        return num;
    }
}