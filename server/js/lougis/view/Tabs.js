/**
 * Handles the Tab bar functionality and tracking of Tabs.
 */
Ext.define('Lougis.view.Tabs', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.lougistabs',
    id: 'tabs',
    tabs: {},
    border: 0,
    title: null,
    autoScroll: true,
    bodyStyle: "border: none;",
    initComponent: function() {
        var items = this.items;
        delete this.items;
        this.callParent();
        Ext.each(items, function(item, index, items) {
            this.addTab(item, false);
        }, this);
        this.setActiveTab(0);
    },

    listeners: {
        remove: function(container, component, opts) {
            this.removeTab(component.href);
        },
        tabChange: function(tabPanel, newCard, oldCard, opts) {
            Lougis.History.push(newCard.href);
        }
    },

    /**
     * Adds a new tab
     *
     * @param {Object} tab
     * @param {String} tab.href URL of the resource
     * @param {String} tab.iconCls CSS class to be used as the icon
     **/
    addTab: function(tab, activate) {
        var component = this.tabs[tab.href];
        if(Ext.isEmpty(component)) {
            component = this.add(tab);
            this.tabs[tab.href] = component;
        }
        if(activate) {this.activateTab(component.href);}
    },

    /**
     * Removes a tab. If the tab to be closed is currently active, activate a neighboring tab.
     *
     * @param {String} href URL of the tab to remove
     */
    removeTab: function(href) {
        delete this.tabs[href];
    },

    /**
     * Activates a tab
     *
     * @param {String} href URL of tab
     * @param {Boolean} updateUrl should the URL of the site be updated (default true)
     * @param {Boolean} updateTitle should the title tag of the site be updated (default true)
     */
    activateTab: function(href, updateUrl, updateTitle) {
        this.setActiveTab(this.tabs[href]);
        if(updateUrl !== false) {Lougis.History.push(href);}
        if(updateTitle !== false) {this.setPageTitle(this.tabs[href].title);}
    },

    closeAllTabs: function() {
    },
    hasTab: function(href) {
        return !Ext.isEmpty(this.tabs[href]);
    },

    /**
     * Sets the contents of <title>`tag.
     * @param {String} text
     */
    setPageTitle: function(text) {
        text = Ext.util.Format.stripTags(text);
        if (!this.origTitle) {this.origTitle = document.title;}
        document.title = text? (this.origTitle+" - "+text): this.origTitle;
    }
});
