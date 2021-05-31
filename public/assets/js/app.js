import flatpickr from "flatpickr";
import $ from "cash-dom";
import { initTimer } from './timer';
import { initAjax } from "./ajax";

initTimer();
initAjax();

function initFlatpickr() {
  flatpickr("#rd", {
    mode: "range",
    dateFormat: "m/d/Y",
    allowInput: true,
  });
}

initFlatpickr();
$(window).on('postbodyswap', initFlatpickr)
