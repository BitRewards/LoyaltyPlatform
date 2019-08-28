$(function(){
  var $blocks = $('.js-join-vk');
  if (!$blocks.length) {
    return;
  }

  function proceed() {
    var requestUrl = $blocks.data('event-url');
    $blocks.each(function(){
      var blockId = $(this).attr('id');
      if (!blockId) {
        blockId = 'vk-join-' + new Date().getTime();
        $(this).attr('id', blockId);
      }
      var groupId = $(this).data('group-id');
      var width = $(this).parent().width();
      VK.Widgets.Group(blockId, {mode: 0, width: width, height: 200}, groupId);
    });

    VK.Observer.subscribe("widgets.groups.joined", function(){
      $.post(requestUrl, {}, function(){
        window.loyalty.updateEverything(function(){

        });
      }, 'json');
    });
  }

  if (window.VK && window.VK.Widgets && window.VK.Widgets.Group) {
    proceed();
  } else {
    $.getScript('https://vk.com/js/api/openapi.js?136', proceed);
  }

});