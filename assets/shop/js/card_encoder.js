import * as JSEncrypt from './jsencrypt.min';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('[name="sylius_checkout_complete"]');
  const submit_button = form.querySelector('[type="submit"]');
  const cards_api = document.getElementById('sylius_checkout_complete_tpay_cards_api').value.replace(/\s/g, '');
  const encrypted_card_field = document.getElementById('sylius_checkout_complete_tpay_card_card');

  const encrypt = new JSEncrypt();
  encrypt.setPublicKey(atob(cards_api));

  submit_button.addEventListener('click', (e) => {
    e.preventDefault();

    const card_number = document.getElementById('sylius_checkout_complete_tpay_card_number').value.replace(/\s/g, '');
    const cvc = document.getElementById('sylius_checkout_complete_tpay_card_cvv').value.replace(/\s/g, '');
    const expiration_date_month = document.getElementById('sylius_checkout_complete_tpay_card_expiration_date_month').value.replace(/\s/g, '');
    const expiration_date_year = document.getElementById('sylius_checkout_complete_tpay_card_expiration_date_year').value.replace(/\s/g, '');
    const expiration_date = [expiration_date_month, expiration_date_year].join('/');

    encrypted_card_field.value = encrypt.encrypt([card_number, expiration_date, cvc, document.location.origin].join('|'));

    form.submit();
  })
});
