'use strict';

export default class Input {
  constructor($el) {
    this.el = $el;
    this.name = this.el.attr('name');
    this.errorField = this.el.parent().parent().find('.js-input-error');

    this.regexp = this.el.data('validate-mode');
    this.required = this.el.data('required');
    this.state = true;
    this.errorClass = 'is-error';

    this.toggleErrorState = this.toggleErrorState.bind(this);

    this.el.on('keypress', () => this.disableErrorState());
    this.el.on('paste', () => this.disableErrorState());


    $('body').on('inputError', this.errorFromServer.bind(this));
  }

  validate() {
    if (this.required) {
      if(this.el.val() == '' || this.el.val() == undefined) {
        this.state = false;
      } else {
        this.state = true;
      }
    }

    if (this.el.hasClass(this.errorClass)) {
      this.state = false;
    }

    let regexp;
    if (this.regexp) {
      switch (this.regexp) {
        case 'email':
          regexp = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
          this.state = this.state && regexp.test(this.el.val());
          break;
        case 'url':
          regexp = /.+\..+/;
          this.state = this.state && regexp.test(this.el.val());
          break;
        case 'email-or-phone':
          regexp = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
          this.state = this.state && regexp.test(this.el.val());

          regexp = /^(7|8)[0-9]{10}$/;
          this.state = this.state || regexp.test(this.el.val());

          regexp = /^[\+\(\)\-0-9]{6,20}$/;
          this.state = this.state || regexp.test(this.el.val());
        case 'eth':
          regexp = /^0x[a-fA-F0-9]{40}$/g;
          this.state = this.state && regexp.test(this.el.val());
      }
    }
    this.toggleErrorState(this.state);
    return this.state;
  }

  toggleErrorState(state) {
    if (state) { 
      this.el.removeClass(this.errorClass);
      this.el.parent().removeClass(this.errorClass);
      this.errorField && this.errorField.text('');
    } else {
      this.el.addClass(this.errorClass);
      this.el.parent().addClass(this.errorClass);
    }
  }

  isHidden() {
    return this.el.data('visibility') === 'hidden';
  }

  disableErrorState() {
    this.toggleErrorState(true);
  }

  errorFromServer(e, data) {
    if (data.name == this.name) {
      this.toggleErrorState();
      this.errorField &&
        this.errorField.text(data.errorText);
    }
  }
}
