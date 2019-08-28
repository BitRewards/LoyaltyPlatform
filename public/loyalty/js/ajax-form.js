'use strict';

import Global from 'global';
import FormValidate from 'form-validate';

export default class AjaxForm {
  constructor(el) {
    this.el = el;
    this.requestData = this.getFormData();
    this.beforeCallback = this.el.data('ajax-before');
    this.commonSuccessCallbacks = [];
  }

  getFormData() {
    let requestData = {};

    $.each(
      this.el.serializeArray(),
      function () {
        requestData[this.name] = this.value;
      }
    );
    return requestData;
  }

  send(form) {
    let result;
    if (this.beforeCallback) {
      let event = new CustomEvent(this.beforeCallback);
      document.body.dispatchEvent(event);
    }

    if (result === false) {
      return false;
    }

    let timeout = result || 0,
        self = this;

    setTimeout(function () {
      let $submitButtons =  self.el.find('input[type=submit], button[type=submit]'),
          $loader = self.el.find('.js-loader');

      if (self.el.hasClass('is-submitting')) {
        return false;
      }

      $loader.addClass('is-load');

      self.el.addClass("is-submitting");

      $submitButtons.attr('disabled', 'disabled');

      $.ajax({
        method: self.el.attr('method'),
        url: self.el.attr('action'),
        data: self.requestData,
        success: function (result) {
          let callback;
          self.finishLoadingState($submitButtons);

          if (result.type != 'error') {
            self.executeCommonSuccessCallbacks(result.data);
          }
          switch (result.type) {
            case 'data':
              callback = self.el.data('ajax-callback');
              self.el.addClass('is-success');
              if (callback) {
                if (form) {
                  form[callback](result.data);
                } else {
                  let event = new CustomEvent(callback, {detail: {data: result.data} });
                  document.body.dispatchEvent(event);
                }
              }
              break;
            case 'error':
              callback = self.el.data('ajax-error-callback');
              if (callback) {
                if (form) {
                  form[callback](result.data);
                } else {
                  let event = new CustomEvent(callback, {detail: {data: result.data} });
                  document.body.dispatchEvent(event);
                }
              }
              Global.processAjaxResponse(result, self.el, self.requestData);
              break;
            case 'redirect':
              Global.processAjaxResponse(result, self.el);
              $loader.addClass('is-load');
              break;
            default:
              Global.processAjaxResponse(result, self.el, self.requestData);
            }

          },
          error: function(response) {
            if (
              response.responseJSON !== undefined
              && response.responseJSON.hasOwnProperty('data') 
              && (typeof response.responseJSON.data == 'object')
            ) {
              let result = response.responseJSON,
              callback = self.el.data('ajax-error-callback');
              if (callback) {
                if (form) {
                  form[callback](result.data);
                } else {
                  let event = new CustomEvent(callback, {detail: {data: result.data} });
                  document.body.dispatchEvent(event);
                }
              }
              Global.processAjaxResponse(result, self.el, self.requestData);
            } else {
              if (response.status == 429) {
                swal('', window.i18n.TOO_MANY_ATTEMPTS_ERROR_OCCURRED, 'error');
              } else {
                swal('', window.i18n.UNKNOWN_ERROR_OCCURRED, 'error');
              }
            }
            self.finishLoadingState($submitButtons);
          },
          dataType: 'json'
        });
    }, timeout);
  }

  setCookie() {
    return new Promise(resolve => {
      if (window.MUST_GET_COOKIES) {
        const newWindow = window.open(window.URLS.SET_COOKIE, 'cookie_window', 'width=50,height=50')
        const self = this;
        var timer = setTimeout(function tick () {
          if (newWindow && newWindow.closed) {
            window.MUST_GET_COOKIES = false
            self.onBeforeUnloadFired = true
            clearTimeout(timer)
            resolve()
          } else {
            timer = setTimeout(tick, 10)
          }
        }, 10)
      } else {
        resolve()
      }
    })
  }

  validateSend() {
    let form = new FormValidate(this.el.get(0));
    if (form.validate()) {
      this.setCookie().then(() => this.send())
    } else {
      if(this.el.data('validate-error-callback')) {
        let event = new CustomEvent(this.el.data('validate-error-callback'));
        document.body.dispatchEvent(event);
      }
    }
  }

  finishLoadingState($submitButtons) {
    this.el.find('.js-loader').removeClass('is-load');
    this.el.removeClass("is-submitting");
    $submitButtons.removeAttr('disabled');
  }

  addCommonSuccessCallback(cb) {
    this.commonSuccessCallbacks.push(cb);
  }

  executeCommonSuccessCallbacks(responseData) {
    this.commonSuccessCallbacks.forEach(function (cb) {
      cb(responseData, this.el, this.requestData);
    });
  }
}
