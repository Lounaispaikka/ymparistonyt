Ext.define('Lougis.view.Panel', {
    extend: 'Ext.container.Container',
    alias: 'widget.lougispanel',
    autoScroll: true,
    title: null,
    target: null,
    items: [],
    layout: 'anchor',
    cls: 'panel-lougis',
    initComponent: function() {
        this.callParent();
    }      
});
