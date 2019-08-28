'use strict';

import Util from 'utils';
import tracker from 'tracker'

export default class TabManager {
  constructor() {
    this.currentTab = null;
    this.currentTabId = null;
    this.previousTab = null;

    this.tabClass = '.js-tab-content';
    this.tabHideClass = 'is-hide';
    this.tabTitle = document.querySelector('.js-tab-title');

    this.items = {
      'login': {
        isShadowHeader: false,
        isAuthTab: true,
        showAuthModal: false,
      },
      'email-not-provided': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'phone-not-provided': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'create-password': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'enter-password': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'email-not-confirmed': {
        isShadowHeader: false,
        isAuthTab: false,
        showAuthModal: true,
      },
      'phone-not-confirmed': {
        isShadowHeader: false,
        isAuthTab: false,
        showAuthModal: true,
      },
      'reset-password': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'confirmation-code-email': {
        isShadowHeader: false,
        isAuthTab: false,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'confirmation-code-phone': {
        isShadowHeader: false,
        isAuthTab: false,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'reset-password-confirm-phone': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'enter-phone': {
        isShadowHeader: false,
        isAuthTab: false,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'enter-email': {
        isShadowHeader: false,
        isAuthTab: false,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'confirm-merge-by-phone': {
        isShadowHeader: false,
        isAuthTab: false,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'confirm-merge-by-email': {
        isShadowHeader: false,
        isAuthTab: false,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'reset-password-sent': {
        isShadowHeader: false,
        isAuthTab: true,
        hideMainMenu: true,
        showAuthModal: false,
      },
      'earn': {
        title: true,
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: false,
        isDisabled: window.IS_EARN_BIT_HIDDEN
      },
      'withdraw': {
        title: true,
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: false,
      },
      'deposit': {
        title: true,
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: false,
      },
      'spend': {
        title: true,
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: false,
        isDisabled: window.IS_SPEND_BIT_HIDDEN
      },
      'invite': {
        isShadowHeader: false,
        isAuthTab: false,
        showAuthModal: true,
        isDisabled: window.IS_INVITE_FRIENDS_HIDDEN
      },
      'history': {
        title: true,
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true,
      },
      'help': {
        title: true,
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true,
      },
      'cabinet': {
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true,
      },
      'settings': {
        isShadowHeader: false,
        isAuthTab: false,
        showAuthModal: true,
      },
      'profile': {
        isShadowHeader: false,
        isAuthTab: false,
        showAuthModal: true,
      },
      'balance': {
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true,
        noGuestsHere: true
      },
      'bitrewards': {
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true,
      },
      'referrer-balance': {
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true,
        noGuestsHere: true
      },
      'dashboard': {
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: true
      },
      'questionary': {
        isShadowHeader: true,
        isAuthTab: false,
        showAuthModal: false
      }
    };

    this.init = this.init.bind(this);
    this.openTabByID = this.openTabByID.bind(this);
    this.onMenuSelect = this.onMenuSelect.bind(this);
    this.changeTitle = this.changeTitle.bind(this);
    this.changeHeader = this.changeHeader.bind(this);
    this.afterHideCallback = this.afterHideCallback.bind(this);
    this.isAuthTab = this.isAuthTab.bind(this);
    this.openTabByID = this.openTabByID.bind(this);

    document.body.addEventListener('MENU_SELECT', this.onMenuSelect);
    document.body.addEventListener('SIDEMENU_CLOSE', () => {
      this.changeHeader(this.currentTabId);
    });

    this.init();
  }

  init() {
    let id = window.location.hash.replace('#', '');
    this.openTabByID(id);
  }

  openTabByID(id) {
    let isEnterEmailOrPhoneTab = (id == 'enter-phone') || (id == 'enter-email');
    let isConfirmationCodeTab = (id == 'confirmation-code-email') || (id == 'confirmation-code-phone')
    if (window.IS_USER_AUTH && !window.USER_HAS_CONFIRMED_PARTNER_CREDENTIALS && !isEnterEmailOrPhoneTab && !isConfirmationCodeTab) {
      if (window.AUTH_METHOD == 'phone') {
        if (window.USER_PHONE) {
          $('.js-hide-email-or-phone').val(window.USER_PHONE);
          this.openTabByID('confirmation-code-phone');
          $.post(window.URLS.SEND_PHONE_VALIDATION_TOKEN, { 'phone': window.USER_PHONE });
        } else {
          this.openTabByID('enter-phone');
        }
      } else {
        if (window.USER_EMAIL) {
          $('.js-hide-email-or-phone').val(window.USER_EMAIL);
          [].forEach.call(document.querySelectorAll('.js-confirm-email'), item => item.innerHTML = window.USER_EMAIL);
          this.openTabByID('confirmation-code-email');
          $.post(window.URLS.SEND_EMAIL_VALIDATION_TOKEN, { 'email': window.USER_EMAIL });
        } else {
          this.openTabByID('enter-email');
        }
      }
      return;
    }

    if (id === undefined || id == '') {
      if (window.IS_USER_AUTH) {

        if(window.ON_AUTH_OPENED_TAB_ID.length > 0){
          id = window.ON_AUTH_OPENED_TAB_ID;
        }
        else{
          if (window.IS_REFERRAL_PROGRAM) {
              id = "invite";
          } else {
              id = 'earn';
          }
        }

      } else {
        id = 'login';

        if (window.IS_REFERRAL_PROGRAM) {
          tracker.trackGaEvent('Interest', 'dashboardUnauthorizedShow');
        }
      }
    }

    if (!window.IS_USER_AUTH) {
      if (this.isNoGuestsHereTag(id)) {
        id = 'login';
      }
    }

    if (this.isDisabledTab(id)) {
      id = 'history';
    }

    let isNeedNoAuth = window.IS_USER_AUTH && this.isAuthTab(id);
    if (isNeedNoAuth && !window.IS_USER_WITHOUT_EMAIL_OR_PHONE) {
      this.openTabByID('earn');
      return;
    }

    if (id == 'enter-password' && !$('.js-email-or-phone').val()) {
      this.openTabByID('login');
      return;
    }

    if (this.currentTab) {
      this.previousTab = this.currentTab;
      this.previousTab.classList.add(this.tabHideClass);
      let callback = this.previousTab.getAttribute('data-tab-close-callback');
      if (callback) {
        Util.dispatchEvent(callback);
      }
      this.previousTab.addEventListener(Util.whichTransitionEvent(), this.afterHideCallback);
    }

    let target = document.querySelector(`${this.tabClass}[data-id="${id}"]`);
    if (target) {
      this.currentTabId = target.getAttribute('data-id');

      Util.dispatchEvent('TAB_BEFORE_OPENED', {id: id, isAuth: this.isAuthTab(this.currentTabId)});

      target.classList.remove(this.tabHideClass);
      this.currentTab = target;

      let handler = () => {
        let callback = target.getAttribute('data-after-show-callback');
        if (callback) {
          Util.dispatchEvent(callback);
        }
        Util.dispatchEvent('TAB_OPENED', {
          id,
          isAuth: this.isAuthTab(this.currentTabId),
          showAuthModal: this.items[id].showAuthModal,
        });
        target.removeEventListener(Util.whichTransitionEvent(), handler);
      }

      target.addEventListener(Util.whichTransitionEvent(), handler);

      this.changeTitle(id);
      this.changeHeader(id);

      window.location.hash = id;
    }
  }

  onMenuSelect(e) {
    this.openTabByID(e.detail.id);
  }

  changeTitle(id) {
    let item = this.items[id];
    this.tabTitle.innerHTML = item.title ? $.trim($('.js-menu .is-active').html()) : '';
  }

  changeHeader(id) {
    let item = this.items[id],
        eventName;
    if (item) {
      Util.dispatchEvent('HEADER_SHADOW', {isShadow : item.isShadowHeader});

      eventName = item.hideMainMenu ? 'HEADER_HIDE_MAIN_MENU' : 'HEADER_SHOW_MAIN_MENU';
      Util.dispatchEvent(eventName);
    }
  }

  afterHideCallback() {
    let callback = this.previousTab.getAttribute('data-after-hide-callback');
    if (callback) {
      Util.dispatchEvent(callback)
    }
    this.previousTab.removeEventListener(Util.whichTransitionEvent(), this.afterHideCallback);
  }

  isAuthTab(id) {
    return this.items[id] && this.items[id].isAuthTab;
  }

  isDisabledTab(id) {
    return this.items[id] && this.items[id].isDisabled;
  }

  isNoGuestsHereTag(id) {
      return this.items[id] && this.items[id].noGuestsHere;
  }
}
