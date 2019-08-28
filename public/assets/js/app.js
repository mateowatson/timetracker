import { Application } from 'stimulus';

import TimerController from './controllers/timer_controller';
import StartNewController from './controllers/start_new_controller';
import FragmentLoaderController from './controllers/fragment_loader_controller';
import AjaxFormController from './controllers/ajax_form_controller';

const application = Application.start();
application.register('timer', TimerController);
application.register('start-new', StartNewController);
application.register('fragment-loader', FragmentLoaderController);
application.register('ajax-form', AjaxFormController);