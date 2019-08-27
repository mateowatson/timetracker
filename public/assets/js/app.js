import { Application } from 'stimulus';

import TimerController from './controllers/timer_controller';

const application = Application.start();
application.register('timer', TimerController);