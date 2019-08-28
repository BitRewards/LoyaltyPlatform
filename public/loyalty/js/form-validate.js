'use strict';

import Input from 'form-field/input';

export default class FormValidate {
  constructor(el) {
    this.el = el;
    this.state = true;
  }

  validate(checkHiddenInputs = true) {
    let self = this;
    this.state = true;
    $(this.el).find('[data-validate=true]').each(function (i, item) {
        let input = new Input($(item));
        if (!checkHiddenInputs) {
          if (!input.isHidden()) {
            let inputState = input.validate();
            self.state = self.state && inputState;
          }
        } else {
          let inputState = input.validate();
          self.state = self.state && inputState;
        }
    });
    this.state = self.state;
    return this.state;
  }
}
