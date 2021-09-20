import $ from 'cash-dom';
import { bodySwapWithHtml, ajaxFormSubmit, getUrl } from './ajax';

const FormTypes = {
  START_FORM: 'START_FORM',
  NEW_PROJECT: 'NEW_PROJECT',
  NEW_TASK: 'NEW_TASK',
  STOP_FORM: 'STOP_FORM'
};

// module-scoped variable that holds the form type
let formType = FormTypes.START_FORM;

// module-scoped variable that holds the computed start time (now - initial elapsed time)
let startTime = null;

// module-scoped variable that holds the current animation frame
let timerId = 0;

// module-scoped variable that holds the server check interval id
let serverCheckIntervalId = 0;

/**
 * Connects to the timer form and sets up an event handler to reconnect after
 * a body swap. Should be called once.
 */
export function initTimer() {
  initTimerForm();
  // re-bind to the timer form after body swaps
  $(window).on('postbodyswap', () => initTimerForm());
}

/**
 * Attaches to the Timer form and makes it AJAX-enabled.
 */
 function initTimerForm() {
  const $timerForm = $('[data-timer-form]');

  if (!$timerForm.length) {
    return;
  }

  // set form type
  setFormType($timerForm);

  // handle form submission
  handleSubmission($timerForm);

  // check the server every 10 seconds for timer state change
  serverCheckIntervalId = setInterval(checkServerForTimerStateChange, 1000 * 10);

  // if it's the running timer, start the elapsed timer display and skip the
  // rest of this function.
  if (formType === FormTypes.STOP_FORM) {
    initTimerDisplay();
    return;
  }

  // turn off the timer icon if needed
  const $favicon = $('[rel=icon][type="image/svg+xml"]');
  $favicon.attr('href', '/assets/images/favicon.svg');

  // initialize form state
  const state = new FormData($timerForm[0]);

  // initial validation
  validate(state);

  // sync form state
  state.forEach((value, key) => {
    $(`[name=${key}]`).on('input', (event) => {
      state.set(key, event.target.value);
      validate(state);
    });
  });
}

/**
 * Hijacks the submit event. Submits the form and performs a body swap.
 * 
 * @param {collection} $timerForm the form selected with $
 */
function handleSubmission($timerForm) {
  // listen for submit
  $timerForm.one('submit', (event) => {
    // disable button and show loading indicator
    $('[data-timer-submit]').attr('disabled', 'disabled');
    $('[data-timer-submit-spinner]').removeClass('d-none');

    resetUiState();

    // submit the form
    ajaxFormSubmit(event);
  });
}

/**
 * Any reset logic that needs to be done prior to a body swap.
 */
function resetUiState() {
  // if needed, stop the timer animation loop
  if (formType === FormTypes.STOP_FORM) {
    cancelAnimationFrame(timerId);
  }
  // stop the server check
  clearInterval(serverCheckIntervalId);
}

/**
 * Compares the ID of the current log to that of a newly requested page. If they
 * are different, it performs a body swap.
 */
async function checkServerForTimerStateChange() {
  try {
    const currentLogId = $('[data-log-id]').data('log-id') || null;
    const text = await getUrl(window.location);
    const newLogId = $(text).find('[data-log-id]').data('log-id') || null;

    if (currentLogId !== newLogId) {
      resetUiState();
      bodySwapWithHtml(text);
      return;
    }

    setFormType();

    // weird hack required by firefox in order to keep the "on" icon from
    // disappearing. if timer is running and thus we want the "on" icon, we need
    // to switch the icon off and then back on again. go figure.
    if (formType === FormTypes.STOP_FORM) {
      const $favicon = $('[rel=icon][type="image/svg+xml"]');
      $favicon.attr('href', '/assets/images/favicon.svg');
      $favicon.attr('href', '/assets/images/favicon-on.svg');
    }
  } catch (err) {
    console.error(err)
  }
}

/**
 * Sets the form type, so we know if it's the regular start form, the new
 * project form, the new task form, or the stop timer form.
 */
function setFormType() {
  if ($('[name=start_time_new_project]').length) {
    formType = FormTypes.NEW_PROJECT;
  } else if ($('[name=start_time_new_task]').length) {
    formType = FormTypes.NEW_TASK
  } else if ($('[name=start_time_project]').length) {
    formType = FormTypes.START_FORM;
  } else {
    formType = FormTypes.STOP_FORM;
  }
}

/**
 * Responsible for disabling the submit button if the form is incomplete.
 * 
 * @param {FormData} state 
 */
function validate(state) {
  if (formType === FormTypes.STOP_FORM) {
    return;
  }

  const project = state.get('start_time_new_project') || state.get('start_time_project');
  const task = state.get('start_time_new_task') || state.get('start_time_task');

  if (!project || !task) {
    $('[data-timer-submit]').attr('disabled', 'disabled');
  } else {
    $('[data-timer-submit]').attr('disabled', null);
  }
}

/**
 * Initializes the timer display. It gets the initial elapsed time, starts the
 * animation loop for the timer, shows the timer running icon, and hides the
 * "Refresh" link.
 */
function initTimerDisplay() {
  // get initial elapsed time if available
  const initialElapsed = elapsedTimeToMs($('[data-timer-elapsed]').data('timer-elapsed'));

  // start the automatic timer display
  timerId = requestAnimationFrame(timerLoop);

  // keep a reference to the start time
  startTime = Date.now() - initialElapsed;

  // hide the refresh link
  $('[data-timer-elapsed] a').hide();

  // update the favicon
  const $favicon = $('[rel=icon][type="image/svg+xml"]');
  $favicon.attr('href', '/assets/images/favicon-on.svg');
}

/**
 * The animation loop for the running timer display.
 * 
 * @param {number} ms 
 */
function timerLoop(ms) {
  // the loop keeps running itself; replace the reference each time
  timerId = requestAnimationFrame(timerLoop);

  // get the elapsed time in milliseconds
  const now = Date.now();
  const elapsed = now - startTime;

  // convert to human friendly format and display it
  const formattedTime = formatElapsedTime(elapsed);
  $('[data-timer-elapsed-display]').text(formattedTime);
}

/**
 * Converts an elapsed time string into milliseconds.
 * 
 * @param {string} timeString a time string of the pattern hh:mm:ss
 * @returns the elapsed time in milliseconds
 */
function elapsedTimeToMs(timeString) {
  if (!timeString) {
      return 0;
  }

  const parts = timeString.split(':').map(part => parseInt(part));
  return (parts[0] * 60 * 60 * 1000) + (parts[1] * 60 * 1000) + parts[2] * 1000;
}

/**
 * Formats a number of milliseconds into the pattern, hh:mm:ss.
 * 
 * @param {number} ms the amount of milliseconds to be formatted
 * @returns {string} the formatted time string
 */
function formatElapsedTime(ms) {
  // convert the elapsed milliseconds into seconds
  const totalInSeconds = ms / 1000;
  // how many seconds to display
  const s = Math.floor(totalInSeconds % 60);
  // how many minutes to display
  const m = Math.floor(totalInSeconds / 60) % 60;
  // how many hours to display
  const h = Math.floor(totalInSeconds / 60 / 60);

  // convert to the string format: HH:MM:SS
  return `${pad(h)}:${pad(m)}:${pad(s)}`;
}

/**
 * A generic 2-digit-minimum left-pad.
 * 
 * @param {number} num
 * @returns {string} 2-digit-minimum numerical string
 */
function pad(num) {
  num = num.toString();

  if (num.length < 2) {
    num = `0${num}`;
  }

  return num;
}
