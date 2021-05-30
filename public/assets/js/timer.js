import $ from 'cash-dom';
import { ajaxFormSubmit } from './ajax-form';

const FormTypes = {
  START_FORM: 'START_FORM',
  NEW_PROJECT: 'NEW_PROJECT',
  NEW_TASK: 'NEW_TASK',
  STOP_FORM: 'STOP_FORM'
};

let formType = FormTypes.START_FORM;

/**
 * Hijacks the submit event. After submitting the form and performing a body
 * swap, re-init the timer controller.
 * 
 * @param {collection} $timerForm the form selected with $
 */
function handleSubmission($timerForm) {
  // listen for submit
  $timerForm.one('submit', event => {
    // show loading indicator
    $('[data-timer-start-spinner]').removeClass('d-none');
    // submit the form and reconnect using an updated reference
    ajaxFormSubmit(event).then(() => initTimer());
  });
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
 * Responsible for disabling the Start button if the form is incomplete.
 * 
 * @param {FormData} state 
 */
function validate(state) {
  if (formType.STOP_FORM) {
    return;
  }

  const project = state.get('start_time_new_project') || state.get('start_time_project');
  const task = state.get('start_time_new_task') || state.get('start_time_task');

  if (!project || !task) {
    $('[data-timer-start]').attr('disabled', 'disabled');
  } else {
    $('[data-timer-start]').attr('disabled', null);
  }
}

/**
 * Attaches to the Timer form and makes it AJAX-enabled.
 */
export function initTimer() {
  const $timerForm = $('[data-timer-form]');

  // handle form submission
  handleSubmission($timerForm);

  // set form type
  setFormType($timerForm);

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