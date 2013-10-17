Ext.define('Mip.model.SelectedTable', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'isMainTable', type: 'boolean', defaultValue: false},
        {name: 'schema',      type: 'string'},
        {name: 'name',        type: 'string'},
        {name: 'alias',       type: 'string'},
        {name: 'joinFrom',    type: 'string'},
        {name: 'joinCompare', type: 'string', defaultValue: '='},
        {name: 'joinToTable', type: 'string'},
        {name: 'joinToField', type: 'string'},
        {name: 'joinType',    type: 'string', defaultValue: 'LEFT JOIN'},
    ],
    hasMany  : {model: 'Mip.model.Attribute', name: 'attributes'},
    panel: null,
    allFieldsStore: null,
    selectedFieldsStore: null,
    filterStore: null,
    sortByStore: null,
    updatePanel: function() {
        this.panel.joinFieldContainer.setVisible(!this.get('isMainTable'));
        this.panel.joinTypeField.setVisible(!this.get('isMainTable'));
        this.panel.setTitle(this.get('schema')+"/"+this.get('name'));
        this.panel.aliasField.setValue(this.get("alias"));
        this.panel.joinFromField.setValue(this.get("joinFrom"));
        this.panel.joinCompareField.setValue(this.get("joinCompare"));
        if(!Ext.isEmpty(this.get("joinToTable"))) this.panel.joinToTableField.setValue(this.get("joinToTable")+"."+this.get("joinToField"));
        this.panel.joinTypeField.setValue(this.get("joinType"));
    },
    updateFromPanel: function() {
        var newAlias = this.panel.aliasField.getValue();
        if(this.get('alias') != newAlias) {
            this.allFieldsStore.each(function(record) {
                if(record.get('ownerAlias') == this.get('alias')) {
                    record.set('ownerAlias', newAlias);
                    record.updateFullName();
                }
            }, this);
            this.selectedFieldsStore.each(function(record) {
                if(record.get('ownerAlias') == this.get('alias')) {
                    record.set('ownerAlias', newAlias);
                    record.updateFullName();
                }
            }, this);
            this.filterStore.each(function(record) {
                if(record.get('tableAlias') == this.get('alias')) {
                    record.set('tableAlias', newAlias);
                }
            }, this);
            this.sortByStore.each(function(record) {
                if(record.get('tableAlias') == this.get('alias')) {
                    record.set('tableAlias', newAlias);
                }
            }, this);
        }
        this.set('alias', this.panel.aliasField.getValue());
        this.set('joinFrom', this.panel.joinFromField.getValue());
        this.set('joinCompare', this.panel.joinCompareField.getValue());
        this.set('joinType', this.panel.joinTypeField.getValue());
        if(!Ext.isEmpty(this.panel.joinToTableField.getValue())) {
            var joinTargets = this.panel.joinToTableField.getValue().split(".");
            this.set('joinToTable', joinTargets[0]);
            this.set('joinToField', joinTargets[1]);
        }

    },
    setPanel: function(panel) {
        this.panel = panel;
        panel.on('close', function() {
            this.allFieldsStore.each(function(record) {
                if(record.get('ownerAlias') == this.get('alias')) this.allFieldsStore.remove(record);
            }, this);
            this.selectedFieldsStore.each(function(record) {
                if(record.get('ownerAlias') == this.get('alias')) this.selectedFieldsStore.remove(record);
            }, this);
            this.filterStore.each(function(record) {
                if(record.get('tableAlias') == this.get('alias')) this.filterStore.remove(record);
            }, this);
            this.sortByStore.each(function(record) {
                if(record.get('tableAlias') == this.get('alias')) this.sortByStore.remove(record);
            }, this);
            if(this.get('isMainTable')) {
                var newMainTable = this.store.getAt(1);
                if(!Ext.isEmpty(newMainTable)) {
                    newMainTable.set('isMainTable', true);
                    newMainTable.updatePanel();
                }
            }
            this.store.remove(this);
        }, this);
    },
    setAllFieldsStore: function(store) {
        this.allFieldsStore = store;
    },
    setSelectedFieldsStore: function(store) {
        this.selectedFieldsStore = store;
    },
    setFilterStore: function(store) {
        this.filterStore = store;
    },
    setSortByStore: function(store) {
        this.sortByStore = store;
    }
});