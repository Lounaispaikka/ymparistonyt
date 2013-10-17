/**
 * Base class for the basic controllers
 */

Ext.define('Lougis.controller.TabContent', {
    extend: 'Ext.app.Controller',
    requires: [
        'Lougis.Fn'
    ],
    refs: [
        {
            ref: 'tabs',
            selector: '#tabs'
        }
    ],
    treeStore: null,
    init: function() {
    },
    hideTreeRoot: true,
    defaultPageXtype: 'lougispanel',
    createPage: function(href, title, xtype) {
        var hrefParts = href.split("/");
        hrefParts.shift();
        title = hrefParts.join(" - ");
        title = Lougis.Fn.prettyName(title);
        if(!this.getTabs().hasTab(href)) {
            var loadMask = new Ext.LoadMask(this.getTabs().getEl(), {msg:"Odota..."});
            loadMask.show();
            Ext.Ajax.request({
                scope: this,
                url: '/run/lougis/content/jsonContent/',
                params: {
                    url: href
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    /*
                    var titleContainer = Ext.create('Ext.container.Container', {
                        xtype: 'container',
                        cls: 'panel-mip-title',
                        html: result.title
                    });

                    var items = [titleContainer].concat(result.items);
                    */
                    var items = result.items;
                    this.getTabs().addTab({
                        xtype: 'container',
                        layout: 'anchor',
                        title: result.title,
                        href: href,
                        items: items
                    }, true);
                    loadMask.hide();
                }
            });



        }
        else this.getTabs().activateTab(href);
    }
});
