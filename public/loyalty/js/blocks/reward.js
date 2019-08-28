var RewardManager = new function(){
  this.displayUsageModal = function(url, callback) {
    $.get(url, function(html){
      var $block = $(html);
      $('.js-modal-overlay').append($block);
      var e = {detail: {target: $block[0]}};

      if (callback) {
        callback();
      }

      window.loyalty.updateEverything(function(){
        window.loyalty.modalManager.openModal(e);
      });
    }, 'html');
  };
};

$(function(){
  var $body = $('body');

  $body.on('click', '.js-get-reward', function(e){
    e.stopPropagation();

    var $this = $(this);

    if ($this.hasClass('is-giftd-blinking')) {
      return;
    }

    $.get(window.URLS.GET_CONFIRMATION_STATUS, function(response) {
      if (response.data.result == window.CONFIRMATION_STATUS.NOT_NEEDED) {

        $this.addClass('is-giftd-blinking');

        var url = $this.data("url");
        $.post(url, {}, function (response) {
          $this.removeClass('is-giftd-blinking');

          RewardManager.displayUsageModal(response.data.url);
        }, 'json');
      } else if (response.data.result == window.CONFIRMATION_STATUS.CONFIRM_PHONE) {
        $this.removeClass('is-giftd-blinking');
        $.get(window.URL.SEND_PHONE_CONFIRMATION);
        window.loyalty.tabManager.openTabByID('phone-not-confirmed');
      } else {
        $this.removeClass('is-giftd-blinking');
        window.loyalty.tabManager.openTabByID('email-not-confirmed');
      }
    });
  });

  $body.on('click', '.js-transaction-show-usage-modal', function(){
    var $this = $(this);

    if ($this.hasClass('is-giftd-blinking')) {
      return;
    }

    $this.addClass('is-giftd-blinking');
    RewardManager.displayUsageModal($(this).data('usage-url'), function(){
      $this.removeClass('is-giftd-blinking');
    });
  });

  $body.on('click', '.js-saved-coupon-show-usage-modal', function(){
      var $this = $(this);

      if ($this.data('redeem-url')) {
          // no need to open any modal, as we already know the redeem URL
          window.open($this.data('redeem-url'), '_blank');
      }
  });

  $body.on('mouseenter', '.js-transaction-show-usage-modal', function(){
    var $title = $(this).find(".js-title");
    var oldTitle = $title.html();
    $(this).data('default-title', oldTitle);
    $title.html($(this).data('hover-title'));
  });

  $body.on('mouseleave', '.js-transaction-show-usage-modal', function(){
    var $title = $(this).find(".js-title");
    var oldTitle = $(this).data('default-title');
    if (oldTitle) {
        $title.html(oldTitle);
    }
  });
});
