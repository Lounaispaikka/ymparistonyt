Ext.define('Mip.controller.Help', {
    extend: 'Mip.controller.Tree',
    requires: [],
    title: "Ohjeet",
    name: "help",
    treeStoreId: "tree-store-help",
    init: function() {
        this.control({
        });
        Ext.create('Ext.data.TreeStore', {
            storeId: this.treeStoreId,
            root: {
                text: this.title,
                expanded: true,
                children: [
                    { text: "Käyttöliittymä", leaf: true, id: "/ohjeet/kayttoliittyma" },
                    { text: "Tietokannat", expanded: true, children: [
                        { text: "Tietokantojen luominen", leaf: true, id: "/ohjeet/tietokannat/tietokantojen_luominen" },
                        { text: "Tietokantojen selailu", leaf: true, id: "/ohjeet/tietokannat/tietokantojen_selailu"}
                    ] },
                    { text: "Suosikkien käyttö", leaf: true, id: "/ohjeet/tietokannat/suosikkien_kaytto" }
                ]
            }
        });
    }
});
