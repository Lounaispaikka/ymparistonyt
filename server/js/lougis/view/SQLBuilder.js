Ext.define('Mip.view.SQLBuilder', {
    extend: 'Mip.view.Panel',
    alias: 'widget.sqlbuilder',
    layout: 'border',
    anchor: '100% 95%',
    requires: [
        'Mip.store.Tables',
        'Mip.model.SelectedTable',
        'Mip.store.SQLOperations',
        'Mip.store.JoinTypes',
        'Mip.store.SQLOrderBy',
        'Mip.store.SQLConjunctions'
    ],
    tableCount: 0,
    initComponent: function() {
        this.allFieldsStore = Ext.create('Ext.data.Store', {
            model: 'Mip.model.Attribute'
        });
        this.selectedFieldsStore = Ext.create('Ext.data.Store', {
            model: 'Mip.model.Attribute',
            listeners: {
                dataChanged: this.updateQuery,
                scope: this
            }
        });
        this.selectedTablesStore = Ext.create('Ext.data.Store', {
            model: 'Mip.model.SelectedTable',
            listeners: {
                update: this.updateQuery,
                scope: this
            }
        });
        this.tableStore = Ext.create('Mip.store.Tables', {
            groupField: 'schema'
        });

        this.conjunctionStore = Ext.create('Mip.store.SQLConjunctions');
        this.orderByStore = Ext.create('Mip.store.SQLOrderBy');
        this.operationsStore = Ext.create('Mip.store.SQLOperations');

        this.filterStore = Ext.create('Ext.data.Store', {
            fields: [
                {name: 'tableAlias', type: 'string'},
                {name: 'attribute', type: 'string'},
                {name: 'operation', type: 'string', defaultValue: '='},
                {name: 'value', type: 'string'},
                {name: 'conjunction', type: 'string', defaultValue: 'ja'}
            ],
            listeners: {
                dataChanged: this.updateQuery,
                scope: this
            }
        });
        this.sortByStore = Ext.create('Ext.data.Store', {
            fields: [
                {name: 'tableAlias', type: 'string'},
                {name: 'attribute', type: 'string'},
                {name: 'sortBy', type: 'string', defaultValue: 'Nouseva'}
            ],
            listeners: {
                dataChanged: this.updateQuery,
                scope: this
            }
        })

        this.tableGrid = Ext.create('Ext.grid.Panel', {
            split: true,
            region: 'west',
            title: 'Tietokannat ja taulut',
            width: 250,
            store: this.tableStore,
            hideHeaders: true,
            features: [Ext.create('Ext.grid.feature.Grouping',{groupHeaderTpl: '{name}'})],
            columns: [
                {header: 'Taulu', dataIndex: 'name', flex: 1}
            ],
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    ddGroup: 'tables-group',
                    enableDrop: false
                }
            }
        });
        var parent = this;
        this.centerPanel = Ext.create('Ext.Panel', {
            region: 'center',
            title: 'Valitut taulut ja niiden asetukset',
            layout: 'anchor',
            html: '<span class="notice">Lisää tauluja vetämällä ja pudottamalla</span>',
            autoScroll: true,
            listeners: {
                render: function(panel) {
                    Ext.create('Ext.dd.DropTarget', panel.body, {
                        ddGroup: 'tables-group',
                        notifyDrop: function(source, event, data) {
                            parent.addTable(data.records[0]);
                        }
                        ,scope: this
                    });
                },
                scope: this
            }
        });


        this.selectedColumnGrid = Ext.create('Ext.grid.Panel', {
            region: 'north',
            flex: 0.4,
            split: true,
            store: this.selectedFieldsStore,
            sortableColumns: false,
            enableColumnMove: false,
            columns: [
                {xtype: 'templatecolumn', header: 'Kenttä', flex: 1, tpl: new Ext.XTemplate('<tpl if="ownerAlias != \'\'">', '{ownerAlias}.', '</tpl>', '{attribute}'), hideable: false},
                {header: 'Alias', dataIndex: 'alias', flex: 1, editor: {allowBlank: false}, hideable: false}
            ],
            title: 'Valittujen kenttien nimet ja järjestys',
            plugins: [Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1})],
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragText: 'Järjestele kenttiä raahaamalla',
                    ddGroup: 'dragColumnsGroup'
                },
                emptyText: "<span class='notice'>Lisää kenttiä vetämällä ja pudottamalla</span>",
                deferEmptyText: false
            },
            listeners: {
                edit: function(editor, e) {
                    e.record.commit();
                    this.updateQuery();
                },
                scope: this
            },
            bbar: [{
                xtype: 'button',
                icon: '/img/icons/16x16/add.png',
                text: 'Lisää vapaamuotoinen',
                scope: this,
                handler: this.addSpecialFieldToSelectedColumns
            },'->',{
                xtype: 'button',
                icon: '/img/icons/16x16/cross.png',
                text: 'Poista valitut',
                scope: this,
                handler: function() {
                    this.selectedFieldsStore.remove(this.selectedColumnGrid.getSelectionModel().getSelection());
                    this.updateQuery();
                }
            },{
                xtype: 'button',
                icon: '/img/icons/16x16/table_delete.png',
                text: 'Poista kaikki',
                scope: this,
                handler: function() {
                    this.selectedFieldsStore.removeAll();
                    this.updateQuery();
                }
            }]
        });

        this.filterColumnsGrid = Ext.create('Ext.grid.Panel', {
            title: 'Suodata tuloksia',
            region: 'center',
            store: this.filterStore,
            sortableColumns: false,
            enableColumnMove: false,
            flex: 0.3,
            viewConfig: {
                emptyText: "<span class='notice'>Lisää kenttiä vetämällä ja pudottamalla</span>",
                deferEmptyText: false
            },
            columns: [{
                xtype: 'templatecolumn',
                header: 'Kenttä',
                flex: 1,
                tpl: new Ext.XTemplate('{tableAlias}.{attribute}'),
                hideable: false
            },{
                dataIndex: 'operation',
                width: 60,
                hideable: false,
                editor: {
                    allowBlank: false,
                    xtype: 'combobox',
                    store: this.operationsStore,
                    selectOnTab: true,
                    typeAhead: true,
                    displayField: 'title',
                    valueField: 'title',
                    queryMode: 'local',
                    forceSelection: true
                }
            },{
                header: 'Arvo',
                dataIndex: 'value',
                flex: 1,
                editor: {
                    allowBlank: false
                },
                hideable: false
            },{
                dataIndex: 'conjunction',
                width: 40,
                hideable: false,
                editor: {
                    allowBlank: false,
                    xtype: 'combobox',
                    store: this.conjunctionStore,
                    selectOnTab: true,
                    typeAhead: true,
                    displayField: 'title',
                    valueField: 'title',
                    queryMode: 'local',
                    forceSelection: true
                }
            }
            ],
            plugins: [Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1})],
            listeners: {
                render: function(panel) {
                    Ext.create('Ext.dd.DropTarget', panel.body, {
                        ddGroup: 'dragColumnsGroup',
                        notifyDrop: function(source, event, data) {
                            var record = data.records[0];
                            panel.getStore().add({
                                tableAlias: record.get('ownerAlias'),
                                attribute: record.get('attribute')
                            });
                        }
                        ,scope: this
                    });
                },
                edit: function(editor, e) {
                    e.record.commit();
                    this.updateQuery();
                },
                scope: this
            },
            bbar: ['->',{
                xtype: 'button',
                icon: '/img/icons/16x16/cross.png',
                text: 'Poista valitut',
                scope: this,
                handler: function() {
                    this.filterStore.remove(this.filterColumnsGrid.getSelectionModel().getSelection());
                    this.updateQuery();
                }
            },{
                xtype: 'button',
                icon: '/img/icons/16x16/table_delete.png',
                text: 'Poista kaikki',
                scope: this,
                handler: function() {
                    this.filterStore.removeAll();
                    this.updateQuery();
                }
            }]
        });

        this.sortResultsGrid = Ext.create('Ext.grid.Panel', {
            title: 'Järjestele tuloksia',
            region: 'south',
            store: this.sortByStore,
            sortableColumns: false,
            enableColumnMove: false,
            split: true,
            flex: 0.3,
            viewConfig: {
                emptyText: "<span class='notice'>Lisää kenttiä vetämällä ja pudottamalla</span>",
                deferEmptyText: false,
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragText: 'Järjestele kenttiä raahaamalla',
                    ddGroup: 'sortSortByColumns'
                }
            },
            columns: [{
                xtype: 'templatecolumn',
                header: 'Kenttä',
                flex: 1,
                tpl: new Ext.XTemplate('{tableAlias}.{attribute}'),
                hideable: false
            },{
                dataIndex: 'sortBy',
                width: 100,
                header: 'Järjestys',
                hideable: false,
                editor: {
                    allowBlank: false,
                    xtype: 'combobox',
                    store: this.orderByStore,
                    selectOnTab: true,
                    typeAhead: true,
                    displayField: 'title',
                    valueField: 'title',
                    queryMode: 'local',
                    forceSelection: true
                }
            }
            ],
            plugins: [Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1})],
            listeners: {
                render: function(panel) {
                    Ext.create('Ext.dd.DropTarget', panel.body, {
                        ddGroup: 'dragColumnsGroup',
                        notifyDrop: function(source, event, data) {
                            var record = data.records[0];
                            panel.getStore().add({
                                tableAlias: record.get('ownerAlias'),
                                attribute: record.get('attribute')
                            });
                        }
                        ,scope: this
                    });
                },
                scope: this,
                edit: function(editor, e) {
                    e.record.commit();
                    this.updateQuery();
                }
            },
            bbar: ['->',{
                xtype: 'button',
                icon: '/img/icons/16x16/cross.png',
                text: 'Poista valitut',
                scope: this,
                handler: function() {
                    this.selectedFieldsStore.remove(this.selectedColumnGrid.getSelectionModel().getSelection());
                    this.updateQuery();
                }
            },{
                xtype: 'button',
                icon: '/img/icons/16x16/table_delete.png',
                text: 'Poista kaikki',
                scope: this,
                handler: function() {
                    this.selectedFieldsStore.removeAll();
                    this.updateQuery();
                }
            }]
        });

        this.rightPanel = Ext.create('Ext.container.Container', {
            region: 'east',
            width: 360,
            layout: 'border',
            split: true,
            items: [
                this.selectedColumnGrid,
                this.filterColumnsGrid,
                this.sortResultsGrid
            ]
        });

        this.bottomPanel = Ext.create('Ext.Panel', {
            region: 'south',
            height: 80,
            title: 'Kysely',
            split: true,
            autoScroll: true
        });

        this.mainPanel = Ext.create('Ext.container.Container', {
            region: 'center',
            layout: 'border',
            items: [
                this.tableGrid,
                this.centerPanel,
                this.bottomPanel
            ]
        });
        this.items = [
            this.mainPanel,
            this.rightPanel
        ]
        this.callParent();
    },
    addTable: function(record) {
        this.centerPanel.update("");
        var alias = record.getAlias();
        var origAlias = alias;
        var count = 0;
        while(this.selectedTablesStore.find('alias', alias) != -1) {
            count++;
            alias = origAlias+"_"+count;
        }

        var table = Ext.create('Mip.model.SelectedTable', {
            isMainTable: this.selectedTablesStore.getCount() == 0,
            schema: record.get("schema"),
            name: record.get("name"),
            alias: alias
        });
        var store = Ext.create('Ext.data.Store', {
            model: 'Mip.model.Attribute'
        });

        table.setAllFieldsStore(this.allFieldsStore);
        table.setSelectedFieldsStore(this.selectedFieldsStore);
        table.setFilterStore(this.filterStore);
        table.setSortByStore(this.sortByStore);
        this.selectedTablesStore.add(table);


        record.attributes().each(function(record) {
            var field = Ext.create('Mip.model.Attribute', {
                schema: record.get('schema'),
                table: record.get('table'),
                ownerAlias: alias,
                attribute: record.get('attribute'),
                dataType: record.get('dataType'),
                fullName: alias+"."+record.get('attribute')
            });
            this.allFieldsStore.add(field);
            store.add(field);
            field.join(this.allFieldsStore);
        }, this);

        var aliasField = Ext.create('Ext.form.field.Text', {
            fieldLabel: 'Alias',
            listeners: {blur: table.updateFromPanel, scope: table}
        });

        var joinFromField = Ext.create('Ext.form.field.ComboBox', {
            emptyText: 'Tämän taulun kenttä',
            flex: 1,
            store: record.attributes(),
            forceSelection: true,
            displayField: 'attribute',
            valueField: 'attribute',
            queryMode: 'local',
            listeners: {blur: table.updateFromPanel, scope: table}
        });

        var joinCompareField = Ext.create('Ext.form.field.ComboBox', {
            flex: 0.4,
            store: this.operationsStore,
            selectOnTab: true,
            typeAhead: true,
            displayField: 'title',
            valueField: 'operation',
            queryMode: 'local',
            listeners: {blur: table.updateFromPanel, scope: table}
        });

        var joinToTableField = Ext.create('Ext.form.field.ComboBox', {
            emptyText: 'Kyselyn kenttä',
            flex: 1,
            queryMode: 'local',
            store: this.allFieldsStore,
            displayField: 'fullName',
            valueField: 'fullName',
            listeners: {blur: table.updateFromPanel, scope: table}
        });


        var joinTypeField = Ext.create('Ext.form.field.ComboBox', {
            flex: 1,
            store: Ext.create('Mip.store.JoinTypes'),
            displayField: 'description',
            valueField: 'name',
            fieldLabel: 'Yhdistystapa',
            queryMode: 'local',
            listeners: {blur: table.updateFromPanel, scope: table}
        });

        var joinFieldContainer = Ext.create('Ext.form.FieldContainer', {
            fieldLabel: 'Yhdistyskohta',
            layout: 'hbox',
            items: [
                joinFromField,
                {xtype: 'splitter'},
                joinCompareField,
                {xtype: 'splitter'},
                joinToTableField
            ]
        });


        var panel = Ext.create('Ext.panel.Panel', {
            title: record.get("schema")+"/"+record.get("name"),
            anchor: '100%',
            cls: 'x-panel-table',
            layout: 'hbox',
            closable: true,
            frame: true,
            aliasField: aliasField,
            joinFromField: joinFromField,
            joinCompareField: joinCompareField,
            joinToTableField: joinToTableField,
            joinTypeField: joinTypeField,
            joinFieldContainer: joinFieldContainer,
            items: [
                {
                    xtype: 'form',
                    flex: 1,
                    defaults: {
                        anchor: '100%',
                        labelWidth: 110
                    },
                    border: false,
                    bodyStyle: 'background-color: transparent;',
                    items: [
                        aliasField,
                        joinFieldContainer,
                        joinTypeField
                    ],
                    buttons: [{
                        xtype: 'button',
                        icon: '/img/icons/16x16/arrow_switch.png',
                        text: 'Valitse kaikki',
                        scope: this,
                        handler: function() {
                            store.each(function(record) {
                                this.addFieldToResultSet(record);
                            }, this);
                        }

                    }],
                    height: 130
                },
                {xtype: 'splitter'},
                {
                    xtype: 'gridpanel',
                    columns: [{header: 'Kentät', dataIndex: 'attribute', flex: 1}],
                    store: store,
                    width: 160,
                    height: 130,
                    viewConfig: {
                        plugins: {
                            ptype: 'gridviewdragdrop',
                            dragText: 'Raahaa kenttiä',
                            dragGroup: 'dragColumnsGroup',
                            allowDrop: false
                        },
                        copy: true
                    }
                }

            ]
        });
        table.setPanel(panel);
        table.updatePanel();
        this.centerPanel.add(panel);
        this.updateQuery();
    },
    addFieldToResultSet: function(record) {
        var field = Ext.create('Mip.model.Attribute', {
            schema: record.get("schema"),
            table: record.get("table"),
            ownerAlias: record.get("ownerAlias"),
            attribute: record.get("attribute"),
            fullName: record.get("fullName")
        });
        this.selectedFieldsStore.add(field);
    },
    addSpecialFieldToSelectedColumns: function() {
        var contents = Ext.create('Ext.form.field.Text', {
            fieldLabel: "Sisältö"
        });
        var alias = Ext.create('Ext.form.field.Text', {
            fieldLabel: "Alias"
        });

        var window = Ext.create('Ext.window.Window', {
            title: "Lisää erikoiskenttä",
            width: 500,
            height: 120,
            layout: 'anchor',
            bodyStyle: 'padding: 5px;',
            defaults: {
                anchor: '100%'
            },
            buttons: [{
                xtype: 'button',
                text: 'Lisää',
                icon: '/img/icons/16x16/add.png',
                scope: this,
                handler: function() {
                    var field = Ext.create('Mip.model.Attribute', {
                        attribute: contents.getValue(),
                        alias: alias.getValue()
                    });
                    this.selectedFieldsStore.add(field);
                    window.close();
                }
            }],
            items: [
                contents,
                alias
            ]
        }).show();
    },
    updateQuery: function() {
        var fields = [];
        this.selectedFieldsStore.each(function(record) {

            var field = !Ext.isEmpty(record.get('ownerAlias'))? '"'+record.get('ownerAlias')+'"."'+record.get('attribute')+'"': record.get('attribute');
            if(!Ext.isEmpty(record.get('alias'))) field += ' AS "'+record.get("alias")+'"'
            fields.push(field);
        }, this);

        var wheres = [];
        this.filterStore.each(function(record) {
            var where = '"'+record.get('tableAlias')+'"."'+record.get('attribute')+'"';

            var operation= this.operationsStore.findRecord('title', record.get('operation'));
            where += " "+operation.get("operation");
            if(operation.get("includeValue")) {
                where += " "+operation.get("wrapBeforeValue")+record.get("value")+operation.get("wrapAfterValue");
            }
            if(this.filterStore.indexOf(record)+1 != this.filterStore.getCount()) {
                var conjunction = this.conjunctionStore.findRecord('title', record.get('conjunction'));
                where += " "+conjunction.get('conjunction');
            }
            wheres.push(where);
        }, this);

        var orderBys = [];
        this.sortByStore.each(function(record) {
            var orderBy = '"'+record.get('tableAlias')+'"."'+record.get('attribute')+'"';
            var sortBy = this.orderByStore.findRecord('title', record.get('sortBy'));
            orderBy += " "+sortBy.get("operation");
            orderBys.push(orderBy);
        }, this);

        var tables = [];
        this.selectedTablesStore.each(function(record) {
            var table = record.get('isMainTable')? " FROM ": " "+record.get("joinType")+" ";
            table += '"'+record.get("schema")+'"."'+record.get("name")+'" AS "'+record.get("alias")+'"';
            if(!record.get('isMainTable')) {
                table += ' ON("'+record.get("alias")+'"."'+record.get("joinFrom")+'" '+record.get("joinCompare")+' "'+record.get("joinToTable")+'"."'+record.get("joinToField")+'")';
            }
            tables.push(table);
        }, this)

        var html = "SELECT "+fields.join(", ");
        html += tables.join(" ");
        if(!Ext.isEmpty(wheres)) html += " WHERE "+wheres.join(" ");
        if(!Ext.isEmpty(orderBys)) html += " ORDER BY "+orderBys.join(", ");
        this.bottomPanel.update(html);

    }
});
