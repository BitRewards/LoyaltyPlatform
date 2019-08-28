'use strict';

let Util = {
  getWindowWidth: function() {
    return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  },

  whichTransitionEvent: function() {
    let t,
        el = document.createElement("fakeelement");

    let transitions = {
      "transition"      : "transitionend",
      "OTransition"     : "oTransitionEnd",
      "MozTransition"   : "transitionend",
      "WebkitTransition": "webkitTransitionEnd"
    }

    for (t in transitions){
      if (el.style[t] !== undefined){
        return transitions[t];
      }
    }
  },

  dispatchEvent: function(eventName, data) {
    let event = new CustomEvent(eventName, {detail: data});
    document.body.dispatchEvent(event);
  },

  getRootDomain: function() {
    var currentDomain = document.location.hostname;
    var parts = currentDomain.split(".");
    return parts[parts.length - 2] + "." + parts[parts.length - 1];
  },

  getCookie: function(name) {
    var cookies = document.cookie.split(/;\s*/);
    var value = '';

    for (var i = 0; i < cookies.length; i++) {
      var cookie = cookies[i].split('=');
      if(cookie[0] == name) {
        value = decodeURIComponent(cookie[1]);
      }
    }

    return value;
  },

  setCookie: function(key, value, expires) {
    expires = expires || {};
    document.cookie = key + "=" + value + ";path=/;domain=." + Util.getRootDomain() + ";expires=" + expires;
  },

  removeCookie: function(key) {
    Util.setCookie(key, '', -1);
  }
}

export default Util
