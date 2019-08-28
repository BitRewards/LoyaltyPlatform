$(function(){
  if (window.IS_DEMO_PAGE) {
      window.loyalty.postMessageToParent = function(){};
      $('.js-close-popup').css('visibility', 'hidden');
  }

  window.loyalty.postMessageToParent('crmSetUserKey', window.CURRENT_USER_KEY);

  $('body').on('keypress', 'input.is-error, textarea.is-error', function(){
    $(this).removeClass('is-error');
  });

  $('body').on('click', '.js-balance', function(){
    window.loyalty.updateEverything();
  });

  window.loyalty.postMessageToParent('crmSetLocation', document.location.href);

  $(window).on('hashchange', function() {
    window.loyalty.postMessageToParent('crmSetLocation', document.location.href);
  });

  if (top == self) {
    $('body').addClass('is-not-in-iframe');
  }


});