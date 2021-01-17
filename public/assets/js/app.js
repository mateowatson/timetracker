import { Application } from 'stimulus';
import axios from 'axios';
import flatpickr from "flatpickr";

axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';

import TimerController from './controllers/timer_controller';
import StartNewController from './controllers/start_new_controller';
import StopTimerController from './controllers/stop_timer_controller';
import FragmentLoaderController from './controllers/fragment_loader_controller';
import AjaxFormController from './controllers/ajax_form_controller';

const application = Application.start();
application.register('timer', TimerController);
application.register('start-new', StartNewController);
application.register('stop-timer', StopTimerController);
application.register('fragment-loader', FragmentLoaderController);
application.register('ajax-form', AjaxFormController);

flatpickr("#rd", {
    mode: "range",
    dateFormat: "m/d/Y",
    allowInput: true,
});