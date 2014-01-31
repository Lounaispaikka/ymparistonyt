/**
 * The main viewport
 */
Ext.define('Lougis.view.TabViewport', {
    extend: 'Ext.container.Viewport',
    requires: [
        'Lougis.view.Navigation',
        'Lougis.view.Tabs'
    ],
    id: 'viewport',
    layout: 'border',
    defaults: {
        xtype: 'container'
    },

    initComponent: function() {
        this.items = [
            {
                region: 'north',
                id: 'north-region',
                height: 34,
                xtype: 'container',
                layout: 'hbox',
                items: [
                    {
                        xtype: 'container',
                        width: 260,
                        contentEl: 'header-content'
                    },
                    {
                        xtype: 'navigation',
                        flex: 1
                    }
                ]
            },{
                region: 'center',
                layout: 'fit',
                minWidth: 800,
                items: [
                    {
                        xtype: 'lougistabs',
                        defaults: {
                            closable: true
                        }
                    }
                ]
            }
        ];

        this.callParent(arguments);
    }
});
