'use strict';

import Util from 'utils';
import ModalOverlay from 'modal-overlay/modal-overlay';
import AskQuestionModal from 'modal/ask-question-modal';
import PromoCodeModal from 'modal/promocode-modal';
import AddCardModal from 'modal/add-card-modal';
import InviteModal from 'modal/invite-modal';
import EthereumOutputModal from 'modal/ethereum-output-modal';
import WithdrawModal from 'modal/withdraw-modal';
import DepositPersonalModal from 'modal/deposit-personal-modal'
import DepositShopModal from 'modal/deposit-shop-modal'
import ExchangeModal from 'modal/exchange-modal'
import ReferrerWithdrawModal from 'modal/referrer-withdraw-modal';
import HowItWorksModal from 'modal/how-it-works-modal';
import ChangePasswordModal from 'modal/change-password-modal';
import ConfirmPhoneModal from 'modal/confirm-phone-modal';
import ConfirmEmailModal from 'modal/confirm-email-modal';

export default class ModalManager {
  constructor() {
    this.current = null;
    this.overlay = new ModalOverlay();

    this.activeClass = 'is-active';

    this.openModal = this.openModal.bind(this);
    this.closeAll = this.closeAll.bind(this);
    this.initModals = this.initModals.bind(this);
    this.closeNotAuthModal = this.closeNotAuthModal.bind(this);
    this.closeAuthModal = this.closeAuthModal.bind(this);
    this.onOverlayClick = this.onOverlayClick.bind(this);

    let self = this;
    $('body').on('click', '.js-show-modal', function(e) {
      e.preventDefault();
      let modalName = $(this).data('modal');
      self.openModal({detail: {modalName}});
    });

    $('body').on('click', '.js-close-modal', this.closeAll);

    this.overlay.el.addEventListener('click', this.onOverlayClick);

    this.initModals();
    document.body.addEventListener('MENU_SELECT', this.closeNotAuthModal);
  }

  initModals() {
    new AskQuestionModal(document.querySelector('.js-ask-question-modal'));
    new PromoCodeModal(document.querySelector('.js-promocode-modal'));
    new AddCardModal(document.querySelector('.js-add-card-modal'));
    let invite = document.querySelector('.js-invite-modal');
    invite && new InviteModal(invite);
    let ethereum = document.querySelector('.js-ethereum-output-modal');
    ethereum && new EthereumOutputModal(ethereum);
    new WithdrawModal();
    new DepositPersonalModal();
    new DepositShopModal();
    new ExchangeModal();
    new ReferrerWithdrawModal();
    new HowItWorksModal();
    let confirmEmail = document.querySelector('.js-confirm-email-modal');
    confirmEmail && new ConfirmEmailModal(confirmEmail);

    let confirmPhone = document.querySelector('.js-confirm-phone-modal');
    confirmPhone && new ConfirmPhoneModal(confirmPhone);

    let changePassword = document.querySelector('.js-change-password-modal');
    changePassword && new ChangePasswordModal(changePassword);
  }

  openModal(e) {
    let target = e.detail.modalName ? document.querySelector(e.detail.modalName) : e.detail.target;
    if (target === this.current) return;

    if (target) {
      let callback = target.getAttribute('data-before-open-callback');
      if (callback) {
        Util.dispatchEvent(callback, e.detail);
      }

      let handler = () => {
        if (this.current) {
          this.current.classList.remove(this.activeClass);
        }

        this.current = target;
        this.current.classList.add(this.activeClass);
        this.overlay.el.removeEventListener(Util.whichTransitionEvent(), handler);
      };

      if (!this.current) {
        this.overlay.show();
        this.overlay.el.addEventListener(Util.whichTransitionEvent(), handler);
      } else {
        handler()
      }
    }
  }

  closeAll() {
    if (this.current) {
      let callback = this.current.getAttribute('data-close-callback');
      if (callback) {
        Util.dispatchEvent(callback);
      }

      this.current.classList.remove(this.activeClass);
      let handler = () => {
        this.overlay.hide();
        this.current && this.current.removeEventListener(Util.whichTransitionEvent(), handler);
        this.current = null;
      }
      this.current.addEventListener(Util.whichTransitionEvent(), handler);
    }
  }

  closeNotAuthModal() {
    if (this.current && !this.current.classList.contains('js-auth-modal')) {
      this.closeAll();
    }
  }

  closeAuthModal() {
    if (this.current && this.current.classList.contains('js-auth-modal')) {
      this.closeAll();
    }
  }

  onOverlayClick(e) {
    if (!e.target.closest('.modal')) {
      this.closeNotAuthModal();
    }
  }

  openTextModal(title, text, isSuccess = true) {
    let $modal = $('.js-text-modal');
    if (isSuccess) {
      $modal.find('.js-text-modal-success-icon').show()
    }
    $modal.find('.js-text-modal-title').html(title || "");
    $modal.find('.js-text-modal-text').html(text || "");
    this.openModal({detail: {modalName: '.js-text-modal'}});
  }
}
