 /**
 * Browser history management
 */
Ext.define("Lougis.History", {
    singleton: true,
    init: function(  ) {
        Ext.util.History.init(function() {
            var token = Ext.util.History.getToken();
            if(Ext.isEmpty(token)) token = Lougis.App.getDefaultUrl();
            this.navigate(token);
        }, this);
        Ext.util.History.on("change", this.navigate, this);
    },

    // Parses current URL and navigates to the page
    navigate: function(token) {
        if(token.substr(0,1) == "!") token = token.substr(1);
        Lougis.App.getConrollerFromUrl(token).createPage(token);
    },

    /**
     * Adds URL to history
     * @param {String} token  the part of URL after #
     */
    push: function(token) {
        token = this.cleanUrl(token);
        if (!/^#!?/.test(token)) {
            token = "#!"+token;
        }
        Ext.util.History.add(token);

    },

    /**
     * Given a URL, removes anything before a #
     */
    cleanUrl: function(url) {
        return url.replace(/^[^#]+#/, '#');
    }
});
