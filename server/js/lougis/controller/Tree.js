/**
 * Base class for the controllers that use a tree navigation
 */

Ext.define('Mip.controller.Tree', {
    extend: 'Mip.controller.Content',
    requires: [],
    refs: [
        {
            ref: 'tabs',
            selector: '#tabs'
        }
    ],
    treeStoreId: null,
    tree: null,
    init: function() {
    },
    createPage: function(href, title) {
        if(!this.getTabs().hasTab(href)) {
            var store = Ext.data.StoreManager.lookup(this.treeStoreId);
            if(Ext.isEmpty(title)) {
                var node = store.getNodeById(href);
                title = node? node.raw.text: this.title;
            }
            var titleContainer = Ext.create('Ext.container.Container', {
                xtype: 'container',
                cls: 'panel-mip-title',
                html: title
            });
            // the tree is created when a tab is requested from this controller for the first time
            if(!this.tree) {
                this.tree = Ext.create('Ext.tree.Panel', {
                    xtype: 'treepanel',
                    id: this.name+"-tree",
                    store: store,
                    cls: 'browser-tree',
                    listeners: {
                        itemclick: this.processTreeClick,
                        scope: this
                    },
                    border: false,
                    bodyStyle: "border-top: none;",
                    split: true
                });
            }

            this.getTabs().addTab({
                xtype: 'container',
                layout: 'border',
                title: title || this.title,
                href: href,
                items: [
                    {
                        xtype: 'container',
                        cls: 'tree-container',
                        layout: 'fit',
                        width: 200,
                        split: true,
                        autoScroll: true,
                        region: 'west',
                        autoDestroy: false,
                        items: [
                            this.tree
                        ]
                    },
                    {
                        xtype: 'container',
                        region: 'center',
                        cls: 'tree-tab-content',
                        layout: 'anchor',
                        items: [
                            titleContainer,
                            {
                                xtype: 'mippanel',
                                titleContainer: titleContainer
                            }
                        ]
                    }
                ],
                listeners: {
                    // when a panel is activated and if it does not already contain the tree, it is added to it
                    activate: function(component, options) {
                        var treeContainer = component.getComponent(0);
                        if(!treeContainer.getComponent(0)) {
                            treeContainer.add(this.tree);
                        }
                    },
                    // the panel tries to destroy the tree when the panel itself is destroyed. We don't want this to happen so we detach the tree first
                    beforedestroy: function(component, options) {
                        var treeContainer = component.getComponent(0);
                        if(treeContainer.getComponent(0)) {
                            treeContainer.remove(this.tree);
                        }
                    },
                    scope: this
                }
            }, true);
        }
        else this.getTabs().activateTab(href);
    },
    processTreeClick: function(view, record, item, index, event, object) {
        if(record.raw && record.raw.id) this.createPage(record.raw.id, record.raw.text);
    }
});
