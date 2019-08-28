'use strict';

import Global from 'global';
import tracker from 'tracker';
import MenuOpener from 'loyalty-popup/__menu-opener/menu-opener';
import MenuClose from 'loyalty-popup/__menu-close/menu-close';
import Sidemenu from 'sidemenu/sidemenu';
import LoyaltyPopupHeader from 'loyalty-popup/__header/loyalty-popup-header';
import TabManager from 'tab-content/tab-manager';
import ModalManager from 'modal/modal-manager';
import Menu from 'menu/menu';
import Faq from 'faq/faq';
import Offers from 'offers-list/offers';
import UploadImage from 'upload-image/upload-image';
import Overlay from 'overlay/overlay';
import Util from 'utils';

export default class LoyaltyPopup {
  constructor(el) {
    window.loyalty = this;
    window.Global = Global;

    this.el = el;
    this.menuOpenerClass = '.js-menu-opener';
    this.menuCloseClass = '.js-menu-close';
    this.sidemenuClass = '.js-sidemenu';
    this.headerClass = '.js-loyalty-popup-header';
    this.menuClass = '.js-menu';
    this.socialAuthButton = '.js-social-auth-button';
    this.goButton = '.js-go-button';
    this.hideEmailOrPhoneInput = '.js-hide-email-or-phone';
    this.forgotPasswordButton = document.querySelector('.js-forgot-password-button');
    this.tabManager = null;
    this.modalManager = null;
    this.menu = null;
    this.overlay = new Overlay();
    this.closeButton = el.querySelector('.js-close-popup');

    this.activeClass = 'is-active';
    this.hideMainMenuClass = 'is-menu-hide';

    this.sidemenu = null;

    this.addEventListeners = this.addEventListeners.bind(this);
    this.open = this.open.bind(this);
    this.close = this.close.bind(this);
    this.openSidemenu = this.openSidemenu.bind(this);
    this.closeSidemenu = this.closeSidemenu.bind(this);
    this.showAuthModal = this.showAuthModal.bind(this);
    this.closeAllModals = this.closeAllModals.bind(this);
    this.updateEverything = this.updateEverything.bind(this);
    this.hideMainMenu = this.hideMainMenu.bind(this);
    this.showMainMenu = this.showMainMenu.bind(this);

    this.setupAjax();

    this.initCloseLogic();
    this.addEventListeners();
  }

  initCloseLogic() {
    if (parent == self) {
      $(this.closeButton).hide();
    } else {
      $(this.closeButton).show();
      this.closeButton.addEventListener('click', this.close);
      $('.js-overlay').click((e) => {
        if ($(e.target).hasClass('js-overlay') || $(e.target).hasClass('overlay__content') ) {
          this.close();
          tracker.trackGaEvent('Using', 'dashboardFreeSpaceButtonClick')
        }
      });
    }
  }

  addEventListeners() {
    let self = this;
    this.modalManager = new ModalManager();

    this.closeButton.addEventListener('click', this.close);

    document.body.addEventListener('SIDEMENUOPENER_CLICK', this.openSidemenu);
    document.body.addEventListener('SIDEMENUCLOSE_CLICK', this.closeSidemenu);
    document.body.addEventListener('MENU_SELECT', this.closeSidemenu);
    document.body.addEventListener('TAB_OPENED', this.showAuthModal);
    document.body.addEventListener('HEADER_HIDE_MAIN_MENU', this.hideMainMenu);
    document.body.addEventListener('HEADER_SHOW_MAIN_MENU', this.showMainMenu);

    new MenuOpener(document.querySelector(this.menuOpenerClass));
    new MenuClose(document.querySelector(this.menuCloseClass));
    new LoyaltyPopupHeader(document.querySelector(this.headerClass));
    this.sidemenu = new Sidemenu(document.querySelector(this.sidemenuClass));
    this.menu = new Menu(document.querySelector(this.menuClass));
    this.tabManager = new TabManager();
    new Faq(document.querySelector('.js-faq-content'));

    $('body').on('click','.js-go-button', function(e) {
      e.preventDefault();
      let id = $(this).data('id');

      self.modalManager.closeAll();
      self.tabManager.openTabByID(id);
      self.menu.selectMenuItem(id);

      if ($(this).data('close-sidemenu')) {
        self.sidemenu.close();
      }
    });

    this.forgotPasswordButton.addEventListener('click', (e) => {
      e.preventDefault();
      let emailOrPhone = $('.js-email-or-phone').val();
      this.forgotPassword(emailOrPhone);
    });

    [].forEach.call(document.querySelectorAll(this.socialAuthButton), item => {
      item.addEventListener('click', this.openSocialAuth);
    });

    new Offers(document.querySelector('.js-offers'));
    new UploadImage(document.querySelector('.js-image-upload'));

    this.createTipped();
  }

  createTipped() {
    Tipped.create('.js-tooltip', function(el) {
      return {
        content: el.getAttribute('data-tooltip-text') || el.getAttribute('data-tooltip')
      };
    }, {
      size: 'large',
      position: 'top',
      fadeIn: 200,
      fadeOut: 200,
      showDelay: 0,
      hideAfter: 30,
      hideDelay: 0,
      maxWidth: 300
    });
  }

  open() {
    let handler = () => {
      this.el.classList.add(this.activeClass);
      this.overlay.el.removeEventListener(Util.whichTransitionEvent(), handler);
    };
    this.overlay.el.addEventListener(Util.whichTransitionEvent(), handler);
    this.overlay.show();

    tracker.trackGaEvent('Using', 'dashboardShow')
  }

  close(e) {
    this.el.classList.remove(this.activeClass);
    let handler = () => {
      this.overlay.hide();
      this.el.removeEventListener(Util.whichTransitionEvent(), handler);
    }
    this.el.addEventListener(Util.whichTransitionEvent(), handler);
    this.postMessageToParent('crmClose');

    let target = e && e.target.closest('.js-close-popup')

    if (target) {
      tracker.trackGaEvent('Using', 'dashboardCloseButtonClick')
      return
    }
  }

  openSidemenu() {
    this.sidemenu &&
      this.sidemenu.open();
  }

  closeSidemenu() {
    this.sidemenu &&
      this.sidemenu.close();
  }

  showAuthModal(e) {
    let { showAuthModal } = e.detail;
    if (showAuthModal && !window.IS_USER_AUTH) {
      this.modalManager.openModal({detail: {modalName: '.js-auth-modal'}});
    } else {
      this.modalManager.closeAuthModal();
    }
  }

  hideMainMenu() {
    if (!this.el.classList.contains(this.hideMainMenuClass)) {
      this.el.classList.add(this.hideMainMenuClass);
    }
  }

  showMainMenu() {
    if (this.el.classList.contains(this.hideMainMenuClass)) {
      this.el.classList.remove(this.hideMainMenuClass);
    }
  }

  fillAllEmailOrPhoneInputs() {
    let emailOrPhone = document.querySelector('.js-email-or-phone').value;
    [].forEach.call(document.querySelectorAll(this.hideEmailOrPhoneInput), item => {
      item.value = emailOrPhone;
    });
    [].forEach.call(document.querySelectorAll('.js-confirm-email'), item => item.innerHTML = emailOrPhone);
  }

  openPasswordRequest(json) {
    this.modalManager.closeAll();
    switch (json.action) {
      case 'login':
        this.tabManager.openTabByID('enter-password');
        break;

      case 'confirmEmail':
        this.tabManager.openTabByID('confirmation-code-email');
        break;

      case 'confirmPhone':
        this.tabManager.openTabByID('confirmation-code-phone');
        break;

      case 'signupDisabled':
        this.showDisabledSignupModal();
        break;
    }
  }

  showDisabledSignupModal(emailOrPhone) {
  	emailOrPhone = emailOrPhone || null;
		if (emailOrPhone) {
			document.querySelector('.js-email-or-phone').value = emailOrPhone;
			this.fillAllEmailOrPhoneInputs()
		}
    this.modalManager.openModal({detail: {modalName: '.js-signup-disabled-modal'}});
  }

  openEmailOrPhoneRequest() {
    if (window.AUTH_METHOD == 'phone') {
      this.tabManager.openTabByID('phone-not-provided');
    } else {
      this.tabManager.openTabByID('email-not-provided');
    }
  }

  forgotPassword(emailOrPhone) {
    var button = $('.js-forgot-password-button');
    var blinkingClass = 'is-giftd-blinking';
    if (!button.hasClass(blinkingClass)) {
      let data = window.AUTH_METHOD == 'phone' ? { phone: emailOrPhone } : { email: emailOrPhone }
      $.post(window.URLS.FORGOT_PASSWORD, data, (response) => {
        if (window.AUTH_METHOD == 'phone') {
          window.loyalty.tabManager.openTabByID('confirmation-code-phone');
        } else {
          this.tabManager.openTabByID('confirmation-code-email');
        }

        button.removeClass(blinkingClass);
      });

      button.addClass(blinkingClass);
    }
  }

  mergeProfiles(emailOrPhone) {
    if (window.AUTH_METHOD == 'phone') {
      $('.js-merge-phone').val(emailOrPhone);
      $.post(window.URLS.SEND_MERGE_BY_PHONE_CONFIRMATION, {'phone': emailOrPhone});
      window.loyalty.tabManager.openTabByID('confirm-merge-by-phone');
    } else {
      $('.js-merge-email').val(emailOrPhone);
      $.post(window.URLS.SEND_MERGE_BY_EMAIL_CONFIRMATION, {'email': emailOrPhone});
      window.loyalty.tabManager.openTabByID('confirm-merge-by-email');
    }
  }

  closeAllModals() {
    this.modalManager.closeAll();
  }

  setupAjax() {
    $.ajaxSetup({
      beforeSend: function(xhr) {
        let token = Util.getCookie('XSRF-TOKEN');
        let header = 'X-XSRF-TOKEN';
        if (!token) {
          token = $('meta[name="csrf-token"]').attr('content');
          header = 'X-CSRF-TOKEN';
        }

        xhr.setRequestHeader(header, token);
      },

      error: function(response) {
        let json = null;
        if (response.responseJSON !== undefined) {
          json = response.responseJSON;
        } else {
          try{
            json = JSON.parse(response.responseText);
          } catch(e){}
        }
        if (!json || !Global.processAjaxResponse(json)) {
          if (response.status == 429) {
            swal('', window.i18n.TOO_MANY_ATTEMPTS_ERROR_OCCURRED, 'error');
          } else {
            swal('', window.i18n.UNKNOWN_ERROR_OCCURRED, 'error');
          }
        }
      }
    });
  }

  openSocialAuth() {
    var url = $(this).data('url');
    var type = $(this).data('type');
    if (!url) {
      return;
    }

    if (type) {
      switch (type) {
        case 'fb':
          tracker.trackGaEvent('Auth', 'fbAuthClick')
          break

        case 'vk':
          tracker.trackGaEvent('Auth', 'vkAuthClick')
          break

        case 'tw':
          tracker.trackGaEvent('Auth', 'twAuthClick')
          break

        case 'gp':
          tracker.trackGaEvent('Auth', 'gpAuthClick')
          break
      }
    }

    var width = 500;
    var height = 500;
    var left = (screen.width / 2) - (width / 2);
    var top = (screen.height / 2) - (height / 2);
    window.open(url, 'OAuth', 'width='+width+', height='+height+', left='+left+', top='+top);
  }

  updateEverything(callback) {
    let self = this;
    $.get(window.URLS.UPDATE_EVERYTHING, function(html) {
      self.closeAllModals();
      let $html = $(html);
      $html.find('.js-updatable').each(function(){
        let $this = $(this);
        let id = $this.data('block-id');
        let $replacedBlock = $(`.js-updatable[data-block-id=${id}]`);
        if ($replacedBlock.length) {
          let oldHtml = $replacedBlock.html();
          let newHtml = $this.html();
          if ($this.data('hidden') !== undefined) {
            if ($this.data('hidden')) {
              $replacedBlock.slideUp();
            } else {
              $replacedBlock.slideDown();
            }
            $replacedBlock.data('hidden', $this.data('hidden'));
          } else {
            if (oldHtml != newHtml) {
                $replacedBlock.addClass('is-flashed-on-update');
            } else {
                $replacedBlock.removeClass('is-flashed-on-update');
            }
          }

          $replacedBlock.html(newHtml);
        }

      });
      if (callback) {
        callback();
      }
      self.createTipped();
    }, 'html');
    //this.addEventListeners();
  }

  postMessageToParent(method, params) {
    params = params || null;
    params = ((typeof params == 'object') ? JSON.stringify(params) : params);
    var message = 'giftd/' + method + '~' + params;
    window.parent.postMessage(message, '*');
  };
}
