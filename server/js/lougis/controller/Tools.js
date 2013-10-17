Ext.define('Mip.controller.Tools', {
    extend: 'Mip.controller.Content',
    requires: [
        "Mip.view.UsersAndGroups",
        "Mip.view.SQLBuilder"
    ],
    title: "Työkalut",
    init: function() {
        this.control({
        });
    },
    tabs: {
        "kayttajat_ja_ryhmat": {
            title: "Käyttäjien ja ryhmien hallinta",
            xtype: 'usersandgroups'
        },
        "cms": {
            title: "Sisällönhallinta",
            xtype: 'cms'
        },
        "muutoshistoria": {
            title: "Muutoshistoria",
            xtype: 'mippanel'
        },
        "pdft": {
            title: "PDF:t",
            xtype: 'mippanel'
        },
        "tukipyynto": {
            title: "Tukipyyntö",
            xtype: 'mippanel'
        },
        "wms-aineistot": {
            title: "WMS-aineistojen hallinta",
            xtype: 'mippanel'
        },
        "kyselytyokalu": {
            title: "Kyselytyökalu",
            xtype: 'sqlbuilder'
        }
    },
    createPage: function(href) {
        if(!this.getTabs().hasTab(href)) {
            var parts = href.split("/");
            var tabName = parts[2];
            var tab = this.tabs[tabName];
            if(!Ext.isEmpty(tab)) {
                var titleContainer = Ext.create('Ext.container.Container', {
                    xtype: 'container',
                    cls: 'panel-mip-title',
                    html: tab.title
                });
                this.getTabs().addTab({
                    xtype: 'container',
                    layout: 'anchor',
                    title: tab.title,
                    href: href,
                    items: [
                        titleContainer,
                        {
                        xtype: tab.xtype,
                        titleContainer: titleContainer
                    }]
                }, true);
            }
        }
        else this.getTabs().activateTab(href);
    }
});
