import flatpickr from "flatpickr";
import { initTimer } from './timer';

initTimer();

flatpickr("#rd", {
  mode: "range",
  dateFormat: "m/d/Y",
  allowInput: true,
});
