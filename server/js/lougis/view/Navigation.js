Ext.define('Lougis.view.Navigation', {
    extend: 'Ext.container.Container',
    alias: 'widget.navigation',
    id: 'navigation',
    componentCls: 'navigation',
    initComponent: function () {
        this.toolbar = Ext.create('Ext.toolbar.Toolbar', {
            items: [
                {
                    icon: '/img/icons/16x16/house.png',
                    text: "Etusivu",
                    target: '/etusivu',
                    action: 'openpage'
                },
                '->',/*
                ,{
                    icon: '/img/icons/16x16/database.png',
                    text: "Tietokannat",
                    target: '/tietokannat',
                    action: 'openpage'
                },{
                    icon: '/img/icons/16x16/question.png',
                    text: "Ohjeet",
                    target: '/ohjeet',
                    action: 'openpage'
                },
                '->',{
                    icon: '/img/icons/16x16/star.png',
                    target: '/kirjanmerkit',
                    text: 'Kirjanmerkit',
                    xtype: 'splitbutton',
                    menu: {
                        xtype: 'menu',
                        items: [{
                            text: "Lisää kirjanmerkki",
                            icon: '/img/icons/16x16/add.png',
                            target: '/kirjanmerkit/lisaa_kirjanmerkki'
                        },'-',{
                            text: "<i>Ei kirjanmerkkejä</i>"
                        }]
                    }
                }*/ {
                    icon: '/img/icons/16x16/setting_tools.png',
                    target: '/tools',
                    text: 'Työkalut',
                    xtype: 'splitbutton',
                    menu: {
                        xtype: 'menu',
                        items: [
                            {
                                text: "Käyttäjien ja ryhmien hallinta",
                                icon: '/img/icons/16x16/user_go.png',
                                target: '/tools/users_and_groups'
                            }, {
                                text: "Sisällönhallinta",
                                icon: '/img/icons/16x16/page_edit.png',
                                target: '/tools/cms'
                            }, {
                                text: "Ajankohtaiset",
                                icon: '/img/icons/16x16/newspaper.png',
                                target: '/tools/news'
                            }, {
                                text: "Tilastot",
                                icon: '/img/icons/16x16/chart_pie_alternative.png',
                                target: '/tools/charts'
                            }, {
                                text: "Toimenpiteiden arvioinnit",
                                icon: '/img/icons/16x16/table_chart.png',
                                target: '/ymparisto/toimenpiteet'
                            }
                            /*,{
                                text: "Muutoshistoria",
                                icon: '/img/icons/16x16/hourglass.png',
                                target: '/tyokalut/muutoshistoria'
                            },{
                                text: "PDF:t",
                                icon: '/img/icons/16x16/file_extension_pdf.png',
                                target: '/tyokalut/pdft'
                            },{
                                text: "Tukipyyntö",
                                icon: '/img/icons/16x16/support.png',
                                target: '/tyokalut/tukipyynto'
                            },{
                                text: "WMS-aineistojen hallinta",
                                icon: '/img/icons/16x16/map_edit.png',
                                target: '/tyokalut/wms-aineistot'
                            }*/
                        ]
                    }
                }, {
                    text: "Ladataan...",
                    target: '/profiili',
                    xtype: 'splitbutton',
                    id: 'profileButton',
                    menu: {
                        xtype: 'menu',
                        items: [
                            {
                                text: "Profiili",
                                icon: '/img/icons/16x16/user_silhouette.png',
                                target: '/profiili'
                            },/*,{
                                text: "På svenska",
                                icon: '/img/icons/16x16/se.png',
                                target: '/profiili/vaihda_kieli/pa_svenska'
                            }*/ '-' , {
                                text: "Kirjaudu ulos",
                                icon: '/img/icons/16x16/im-invisible-user.png',
                                href: '/run/lougis/usersandgroups/logoutUser/'
                            }
                        ]
                    }
                }
            ]
        });
        this.items = [this.toolbar];
        this.callParent();
    }
});
