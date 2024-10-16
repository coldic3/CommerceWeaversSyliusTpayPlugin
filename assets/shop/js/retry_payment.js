function disableAllPaymentDetails() {
  document.querySelectorAll('[data-payment-details]').forEach((element) => element.style.display = 'none');
  document.querySelectorAll('[data-payment-details] input, [data-payment-details] select, [data-payment-details] textarea, [data-payment-details] button')
    .forEach(function(element) {
      element.disabled = true;
    });
}

function enablePaymentDetails(element) {
  element.style.display = '';
  element.querySelectorAll('input, select, textarea, button')
    .forEach(function(element) {
      element.disabled = false;
    });
}

function resolvePaymentDetailsFromPaymentMethodRadioButton(radioButton) {
  return radioButton.parentElement.parentElement.parentElement.querySelector('[data-payment-details]');
}

document.addEventListener('DOMContentLoaded', () => {
  disableAllPaymentDetails();

  const checkedCheckboxElement = $('input[type=radio]:checked')[0];
  if (checkedCheckboxElement !== undefined) {
    const paymentDetailsForm = resolvePaymentDetailsFromPaymentMethodRadioButton(checkedCheckboxElement);

    if (paymentDetailsForm) {
      enablePaymentDetails(paymentDetailsForm);
    }
  }

  $('input[type=radio]').change(function(event) {
    disableAllPaymentDetails();

    const paymentDetailsForm = resolvePaymentDetailsFromPaymentMethodRadioButton(event.target);

    if (paymentDetailsForm) {
      enablePaymentDetails(paymentDetailsForm);
    }
  });
});
