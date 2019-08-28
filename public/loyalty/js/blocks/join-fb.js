$(function(){
  var $blocks = $('.js-join-fb');

  function proceed() {
    FB.init({
      appId: window.FB_APP_ID,
      xfbml: true,
      version: 'v2.8'
    });

    var requestUrl = $blocks.data('event-url');
    $blocks.each(function(){
      var pageUrl = $(this).data('page-url');
      var width = $(this).parent().width();
      var height = 220;
      var $div = $('<div class="fb-page" data-small-header="false" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="true"></div>');
      $div.attr('data-href', pageUrl);
      $div.attr('data-width', width);
      $div.attr('data-height', height);

      $(this).html($div);

      FB.XFBML.parse();

    });

    function onLike() {
        $.post(requestUrl, {}, function(){
            window.loyalty.updateEverything(function(){

            });
        }, 'json');
    }

    FB.Event.subscribe('edge.create', function(e){
      onLike();
    });

    $('body').on('click', '.js-show-modal', function(e) {
      // dirty hack to make iframeTracker work properly
        setTimeout(function(){
            $blocks.find('iframe').iframeTracker({
                // if FB like iframe is reloaded after click in it — the like with "Confirm" window was made
                blurCallback: function(e){
                    $blocks.find('iframe').on('load', function(){
                        onLike();
                    });
                }
            });
        }, 100);
    });
  }

  if (!window.FB) {
    window.fbAsyncInit = function() {
      proceed();
    };
  } else {
    proceed();
  }
});