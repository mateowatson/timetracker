import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ 'elapsed', 'refresh' ]

    connect() {
        // start the automatic timer display
        this.startTimer();

        // hide the refresh link
        this.refreshTarget.style.display = 'none';
    }

    startTimer() {
        // read initial time
        let initialTime = this.data.get('elapsed');
        initialTime = initialTime.split(':');
        initialTime = initialTime.map(number => {
            return parseInt(number);
        });
        
        // create a diy time object
        this.currentTime = {
            h: initialTime[0],
            m: initialTime[1],
            s: initialTime[2]
        };

        // start timer loop
        this.timer = setInterval(() => {
            this.incrementTimer();
            this.updateDisplay();
        }, 1000);
    }

    incrementTimer() {
        this.currentTime.s++;

        if (this.currentTime.s > 59) {
            this.currentTime.s = 0;
            this.currentTime.m++;

            if (this.currentTime.m > 59) {
                this.currentTime.m = 0;
                this.currentTime.h++;
            }
        }
    }

    updateDisplay() {
        const hours = this.displayNumber(this.currentTime.h);
        const minutes = this.displayNumber(this.currentTime.m);
        const seconds = this.displayNumber(this.currentTime.s);
        const elapsed = `${hours}:${minutes}:${seconds}`;
        
        this.elapsedTarget.innerHTML = elapsed;
        this.data.set('elapsed', elapsed);
    }

    displayNumber(number) {
        number = number.toString();

        if (number.length < 2) {
            number = '0' + number;
        }

        return number;
    }
}