import flatpickr from "flatpickr";
import { initTimer } from './timer';
import { initAjax } from "./ajax";

initTimer();
initAjax();

flatpickr("#rd", {
  mode: "range",
  dateFormat: "m/d/Y",
  allowInput: true,
});
