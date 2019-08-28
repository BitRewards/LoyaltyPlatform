Share.VK = new function(){
    var self = this;
    this.open = function(url, shareCallback) {
        var popupUrl = 'http://vk.com/share.php?_fm=1&';
        popupUrl += 'url='          + encodeURIComponent(url);

        self.reset();
        self.replaceCountCallback();

        self.requestCountUpdated(url);

        var checkInterval = setInterval(function(){
            self.requestCountUpdated(url);
        }, 1000);
        setTimeout(function(){
            clearInterval(checkInterval);
        }, 60 * 60 * 1000);

        Share.popup(popupUrl);

        $(document).one('vk-strict-share', function(){
            shareCallback();
        });
    };

    this.reset = function() {
        self.currentVkShareCount = null;
    };

    this.currentVkShareCount = null;

    this.countUpdated = function(count) {
        if (self.currentVkShareCount !== null) {
            if (count > self.currentVkShareCount) {
                $(document).trigger('vk-strict-share');
            } else {
                self.currentVkShareCount = count;
            }
        } else {
            self.currentVkShareCount = count;
        }
    };

    this.replaceCountCallback = function() {
        if (!window.VK) {
            window.VK = {};
        }
        if (!window.VK.Share) {
            window.VK.Share = {};
        }
        window.VK.Share.count = function(idx, totalCount){
            if (idx == 99) {
                self.countUpdated(totalCount);
            }
        };
    };

    this.requestCountUpdated = function(url) {
        $.getScript('https://vk.com/share.php?act=count&index=99&url=' + encodeURIComponent(url));
    };
};