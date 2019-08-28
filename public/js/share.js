Share = {
    METHOD_VK: 'vk',
    METHOD_OK: 'ok',
    METHOD_FB: 'fb',
    METHOD_TWITTER: 't',
    METHOD_MAILRU: 'mailru',
    METHOD_GPLUS: 'gplus',

    open: function(method, url, title, image, text, strict, closeCallback) {
        Share[method](url, title, image, text, strict, closeCallback);
        //console.log(text, image, title, url);
    },

    vk: function(purl, ptitle, pimg, text, strict, closeCallback) {
        if (strict && (typeof window.VK != 'undefined') && !isMobileBrowser) {
            $('.onp-vk-share-button-wrap .onp-pseudo-share-button').click();
        } else {
            purl = Util.addParam(purl, 'm', Share.METHOD_VK);
            var url  = 'http://vk.com/share.php?_fm=1&';
            url += 'url='          + encodeURIComponent(purl);
            /*url += '&title='       + encodeURIComponent(ptitle);
             url += '&description=' + encodeURIComponent(text);
             url += '&image='       + encodeURIComponent(pimg);
             url += '&noparse=true';*/
            Share.popup(url, closeCallback);
        }
    },

    ok: function(purl, ptitle, pimg, text, strict, closeCallback) {
        purl = Util.addParam(purl, 'm', Share.METHOD_OK);
        url  = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
        url += '&st.comments=' + encodeURIComponent(text);
        url += '&st._surl='    + encodeURIComponent(purl);
        Share.popup(url, closeCallback);
    },
    fb: function(purl, ptitle, pimg, text, strict, closeCallback) {
        if (strict && (typeof window.FB != 'undefined')) {
            $('.onp-facebook-share-button-overlay').click();
        } else {
            purl = Util.addParam(purl, 'm', Share.METHOD_FB);
            url = 'http://www.facebook.com/sharer.php?';
            url += '&u=' + encodeURIComponent(purl);
            Share.popup(url, closeCallback);
        }
    },
    t: function(purl, ptitle, pimg, text, strict, closeCallback) {
        purl = Util.addParam(purl, 'm', Share.METHOD_TWITTER);
        url  = 'http://twitter.com/share?';
        url += 'text='      + encodeURIComponent(text);
        url += '&url='      + encodeURIComponent(purl);
        url += '&counturl=' + encodeURIComponent(purl);
        Share.popup(url, closeCallback);
    },
    mailru: function(purl, ptitle, pimg, text, strict, closeCallback) {
        purl = Util.addParam(purl, 'm', Share.METHOD_MAILRU);
        url  = 'http://connect.mail.ru/share?';
        url += 'url='          + encodeURIComponent(purl);
        url += '&title='       + encodeURIComponent(ptitle);
        url += '&description=' + encodeURIComponent(text);
        url += '&imageurl='    + encodeURIComponent(pimg);
        Share.popup(url, closeCallback)
    },

    gplus: function(purl, ptitle, pimg, text, strict, closeCallback) {
        purl = Util.addParam(purl, 'm', Share.METHOD_GPLUS);
        url  = 'https://plus.google.com/share?';
        url += 'url='          + encodeURIComponent(purl);
        Share.popup(url, closeCallback)
    },

    popup: function(url, closeCallback) {
        var popup = window.open(url,'','toolbar=0,status=0,width=626,height=436');
        this.watchWindowClosing(popup, closeCallback);
    },

    watchWindowClosing: function(window, closeCallback){
        var intervalCheckClosed = setInterval(function(){
            if (!window || window.closed) {
                clearInterval(intervalCheckClosed);
                // TODO раскомментировать
                if (closeCallback) {
                    closeCallback();
                }
            }
        }, 200);
    }
};

$(function(){
    $('body').on('click', '.js-share', function(){
        var url = $(this).data("url") || document.location.href;
        var method = $(this).data('method');

        Share.open(method, url);

        return false;
    });
});