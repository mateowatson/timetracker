import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [
        'project',
        'projectSelect',
        'task',
        'taskSelect',
        'submit'
    ];

    initialize() {
        window.addEventListener('fragmentpostswap', event => {
            if(event.detail !== 'start-new') return;
            this.inputChange();
        });
    }

    connect() {
        this.inputChange();
    }

    inputChange() {
        const hasProject = this.hasProjectTarget;
        const hasProjectSelect = this.hasProjectSelectTarget;
        const hasTask = this.hasTaskTarget;
        const hasTaskSelect = this.hasTaskSelectTarget;

        let submitEnabled = true;

        let project = '';
        let task = '';

        if(hasProjectSelect) {
            project = this.projectSelectTarget.value;
        }

        if(hasProject) {
            project = this.projectTarget.value ? this.projectTarget.value : project;
        }

        // if no project, disable start button
        submitEnabled = project ? true : false;

        if(hasTaskSelect) {
            task = this.taskSelectTarget.value;
        }

        if(hasTask) {
            task = this.taskTarget.value ? this.taskTarget.value : task;
        }

        // if no task, disable start button
        submitEnabled = task && submitEnabled ? true : false;

        // set state of start button
        if(submitEnabled) return this.submitTarget.removeAttribute('disabled');
        this.submitTarget.setAttribute('disabled', 'disabled');
    }
}