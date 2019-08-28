'use strict';

import Clipboard from 'clipboard';

document.addEventListener('DOMContentLoaded', () => {
  let clipboard = new Clipboard('.js-clipboard'),
      $done = $('.js-copy-done'),
      doneShowClass = 'is-show',
      clipboardHideClass = 'is-hide';

  if (!Clipboard.isSupported()) {
    let actionMsg = '';

    if (/Mac/i.test(navigator.userAgent)) {
      actionMsg = window.LANGUAGE == 'ru' ? 'Нажмите ⌘-С': 'Press ⌘-С';
    } else {
      actionMsg = window.LANGUAGE == 'ru' ? 'Нажмите Ctrl-С' : 'Press Ctrl-C';
    }

    $done.attr('data-tooltip-text', actionMsg);
  }

  clipboard.on('success', e => {
    showDoneTooltip(e);
  });

  clipboard.on('error', e => {
    showDoneTooltip(e);
  });

  function showDoneTooltip(copyTooltip) {
    copyTooltip.trigger.classList.add(clipboardHideClass);
    $done.addClass(doneShowClass);
    setTimeout(() => {
      copyTooltip.trigger.classList.remove(clipboardHideClass);
      $done.removeClass(doneShowClass);
    }, 5000);
  }
});
