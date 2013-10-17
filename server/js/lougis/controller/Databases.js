Ext.define('Mip.controller.Databases', {
    extend: 'Mip.controller.Tree',
    requires: [],
    title: "Tietokannat",
    name: "databases",
    treeStoreId: "tree-store-databases",
    init: function() {
        this.control({
        });
        Ext.create('Ext.data.TreeStore', {
            storeId: this.treeStoreId,
            root: {
                text: this.title,
                expanded: true,
                children: [
                    { text: "Vanhat tietokannat", leaf: true, id: "/tietokannat/vanhat_tietokannat" },
                    { text: "Rakennusinventointi", expanded: true, id: "/tietokannat/rakennusinventointi", children: [
                        { text: "Luo kiinteistö", leaf: true, id: "/tietokannat/rakennusinventointi/luo_kiinteisto" },
                        { text: "Selaa kiinteistöjä", leaf: true, id: "/tietokannat/rakennusinventointi/selaa_kiinteistoja"},
                        { text: "Selaa rakennuksia", leaf: true, id: "/tietokannat/rakennusinventointi/selaa_rakennuksia"}
                    ] },
                    { text: "Uusi tietokanta", leaf: true, id: "/tietokannat/uusi_tietokanta" }
                ]
            }
        });
    }
});
