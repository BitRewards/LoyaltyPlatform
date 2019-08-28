$(function(){
  $('body').on('click', '.js-action-share-vk', function(){
    var $this = $(this);
    var requestUrl = $this.data('event-url');
    var url = $this.data('share-url');
    Share.VK.open(url, function(){
      $.post(requestUrl, {}, function(){
        window.loyalty.updateEverything(function(){

        });
      }, 'json');
    });
  });

});