Share.FB = new function(){
    var self = this;

    this.open = function(url, shareCallback) {
        return FB.ui({
            method: "feed",
            display: "popup",
            link: url,
        }, function (data) {
            if (typeof data === 'undefined') {
                return // window was closed/cancel btn was pressed
            } else if (typeof data === 'object' && (data.hasOwnProperty('error_code') || data.hasOwnProperty('error_message'))) {
                return // FB error occuried
            } else if (typeof data === 'object') {
                // as of 2017-05-18 empty array returns when post was published
                shareCallback(data);
            }
        });
    };
};
