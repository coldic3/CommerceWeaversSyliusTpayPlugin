document.addEventListener('DOMContentLoaded', () => {
  const checkedCheckboxElement = $('input[type=radio]:checked')[0];
  if (checkedCheckboxElement !== undefined) {
    const paymentDetailsForm = checkedCheckboxElement.parentElement.parentElement.parentElement.querySelector('[data-payment-details]');

    if (paymentDetailsForm) {
      paymentDetailsForm.style.display = '';
    }
  }

  $('input[type=radio]').change(function(event) {
    document.querySelectorAll('[data-payment-details]').forEach((element) => element.style.display = 'none');

    const paymentDetailsForm = event.target.parentElement.parentElement.parentElement.querySelector('[data-payment-details]');

    if (paymentDetailsForm) {
      paymentDetailsForm.style.display = '';
    }
  });
});
