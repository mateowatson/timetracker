import flatpickr from "flatpickr";
import $ from "cash-dom";
import { initTimer } from './timer';
import { initAjax } from "./ajax";

// if fetch is supported, turn on ajax features
if (fetch) {
  initTimer();
  initAjax();
}

// initializes the date picker widget
function initFlatpickr() {
  flatpickr("#rd", {
    mode: "range",
    dateFormat: "m/d/Y",
    allowInput: true,
  });
}

// initialize the date picker on page load
initFlatpickr();
// initialize the date picker after body swaps
$(window).on('postbodyswap', initFlatpickr);
