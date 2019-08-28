class Dropdown {
  constructor(el) {
    this.el = el;

    this.opener = this.el.querySelector('.js-dropdown-opener');
    this.cancelButton = this.el.querySelector('.js-dropdown-cancel');
    this.getNumberButton = this.el.querySelector('.js-dropdown-number');

    this.tx_id = this.el.querySelector('.js-transaction-id').value;
    this.address = this.el.querySelector('.js-transaction-address').value;
    this.amount = this.el.querySelector('.js-transaction-amount').value;
    this.magic = this.el.querySelector('.js-transaction-magic').value;

    this.el.dataset.id = this.tx_id;

    this.modalAddressText = document.querySelector('.js-sent-withdraw-eth-address');
    this.modalAmountText = document.querySelector('.js-sent-withdraw-total');
    this.modalMagicInput = document.querySelector('.js-sent-withdraw-magic');

    this.openClass = 'is-open';
    this.toggle = this.toggle.bind(this);
    this.cancel = this.cancel.bind(this);
    this.getNumber = this.getNumber.bind(this);

    this.opener.addEventListener('click', this.toggle);
    // this.cancelButton.addEventListener('click', this.cancel);
    this.getNumberButton.addEventListener('click', this.getNumber);
  }

  toggle(e) {
    e.preventDefault();

    this.el.classList.toggle(this.openClass);
  }

  close() {
    this.el.classList.remove(this.openClass);
  }

  cancel() {
    this.close();
  }

  getNumber() {
    this.close();

    this.modalAddressText.innerHTML = this.address;
    this.modalAmountText.innerHTML = this.amount;
    this.modalMagicInput.value = this.magic.match(/.{4}/g).join('-');

    window.loyalty.modalManager.openModal({ detail: { modalName: '.js-sent-withdraw-modal' } });
  }
}

function initDropdowns() {
    let dropdowns = [...document.querySelectorAll('.js-dropdown')];
    dropdowns.forEach(item => {
        if (item.dataset.id) {
            return;
        }
        new Dropdown(item)
    });
}

initDropdowns();

document.addEventListener('transaction-dropdown', initDropdowns);
