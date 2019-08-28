'use strict';

import Util from 'utils';

let Global = {};

export default Global = {
  processAjaxResponse: function (response, $form) {
    $('.is-load').removeClass('is-load');
    switch (response.type) {
        case 'error':
          if (response.code) {
            this.displayErrorByCode(response.code, response.data, $form);
          } else {
            this.displayErrors(response.data, $form);
          }
          return true;
        case 'redirect':
          this.simpleRedirect(response.data.url);
          return true;
        case 'execute':
          Util.executeFunctionByName(response.data.callback, response.data);
          return true;
        default:
          return false;
    }
  },

  simpleRedirect: function(url, forceFrame, forceTop) {
    var stripHashtag = function(url) {
      var parts = url.split('#');
      return parts[0];
    };
    var currentUrl = document.location.href;

    var rootUrl = this.getRootUrl();
    if (url.indexOf(rootUrl) === 0) {
        url = url.replace(rootUrl, "");
    }
    if ((url.charAt(0) == '/' || forceFrame) && !forceTop) {
        document.location = url;

        var newUrlAbsolute = url.charAt(0) == '/' ? (document.location.protocol + "//" + document.location.hostname + url) : url;

        if (stripHashtag(currentUrl) == stripHashtag(newUrlAbsolute)) {
          document.location.reload();
        }
    } else {
        try {
            top.location = url;
        } catch (e) {
            try {
                parent.location = url;
            } catch (e) {
                document.location = url;
            }
        }
    }
  },

  getRootUrl: function() {
    return location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
  },

  getParam: function(name, url) {
    if (!url) {
        url = location.search;
    }
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&#;]" + name + "=([^&#]*)"),
        results = regex.exec(url);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
  },

  displayErrors: function(data, $form) {
    let errorsToAlert = [];
    for (var field in data) {
      var isVisible = false;
      if ($form !== undefined) {
        var $element = $form.find(`[name=${field}]`);
        isVisible = $element.length && $element.is(':visible');
      }

      if (!isVisible) {
          errorsToAlert.push(data[field].join ? data[field].join("<br>") : data[field]);
      } else {
          $('body').trigger('inputError', [{name: $element.attr('name'), errorText: data[field]}]);
      }
    }
    if (errorsToAlert.length) {
        var errorsStr = errorsToAlert.join("<br>");
        swal('', errorsStr, 'error');
    }
  },

  displayErrorByCode: function(code, data, $form) {
    switch (code) {
      default:
        this.displayErrors(data, $form);
        break;
    }
  }
}

Global.scrollToOffset = function(offset, speed) {
    if (typeof speed == 'undefined') {
        speed = 0;
    }
    if (typeof offset == 'undefined') {
        offset = 0;
    }

    $('html, body').stop().animate({scrollTop: offset}, {
        duration: speed
    });
};


$.fn.scrollIntoViewSafely = function(options) {
    var $this = $(this);
    if (!$this.length) {
        return;
    }
    var $body = $('body');
    var bottomOffset = Number.NEGATIVE_INFINITY;
    var topOffset = Number.POSITIVE_INFINITY;
    $(this).each(function(){
        var $el = $(this);
        topOffset = Math.min(topOffset, $el.offset().top);
        bottomOffset = Math.max(bottomOffset, $el.offset().top + $el.outerHeight())
    });
    
    var scrollToOffset = false;
    var targetOffset = false;
    var additionalOffset = 0;
    var bodyTopOffset = 0;
    var visibleArea = [0, 800];
    var windowHeight = 0;

    function proceed() {
        if (topOffset < visibleArea[0]) {
            scrollToOffset = bottomOffset - windowHeight + additionalOffset;
        } else if (bottomOffset > visibleArea[1]) {
            scrollToOffset = bottomOffset - bodyTopOffset - additionalOffset
        }

        scrollToOffset -= bodyTopOffset;
        var isBottomVisible = bottomOffset >= visibleArea[0] && bottomOffset <= visibleArea[1];
        var isTopVisible = topOffset >= visibleArea[0] && topOffset <= visibleArea[1];

        if (!isBottomVisible) {
            targetOffset = bottomOffset;
        } else if (!isTopVisible) {
            targetOffset = topOffset;
        }

        var isVisible = isBottomVisible && isTopVisible;
        if (!isVisible) {
            scrollToOffset = bottomOffset - bodyTopOffset - windowHeight + additionalOffset;
            Global.scrollToOffset(scrollToOffset, options.duration);
        }
    }

    if (typeof options == 'number') {
        options = {
            duration: options
        };
    }

    options = options || {};
    visibleArea = [$body.scrollTop() + $('.js-dynamic-header').height(), $body.scrollTop() + $(window).height()];
    windowHeight = $(window).height();
    additionalOffset = 20;
    proceed();
};


window.isMobileBrowser = /Android|webOS|iPhone|iPad|iPod|iOS|BlackBerry|IEMobile|Windows Phone|Opera Mini/i.test(navigator.userAgent);

Global.isMobileBrowser = function() {
    return window.isMobileBrowser;
};

(function () {

    if ( typeof window.CustomEvent === "function" ) return false;

    function CustomEvent ( event, params ) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent( 'CustomEvent' );
        evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();
