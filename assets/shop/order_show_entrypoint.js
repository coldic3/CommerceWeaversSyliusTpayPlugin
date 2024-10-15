import './js/retry_payment';
import {CardForm} from "./js/card_form";

document.addEventListener('DOMContentLoaded', () => {
  new CardForm('[name="sylius_checkout_select_payment"]');
});
