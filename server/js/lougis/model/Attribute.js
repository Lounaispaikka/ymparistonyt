Ext.define('Mip.model.Attribute', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'schema',     type: 'string'},
        {name: 'table',      type: 'string'},
        {name: 'ownerAlias', type: 'string'},
        {name: 'attribute',  type: 'string'},
        {name: 'value',      type: 'string'},
        {name: 'dataType',   type: 'string'},
        {name: 'alias',      type: 'string'},
        {name: 'fullName',   type: 'string'}
    ],
    updateAlias: function() {
        var alias = this.get('attribute');
        if(alias != this.get('alias')) {
            if(this.store.find('alias', alias) != -1) alias = this.get('ownerAlias')+"."+alias;
            this.set('alias', alias);
        }
    },
    updateFullName: function() {
        this.set('fullName', this.get("ownerAlias")+"."+this.get("attribute"));
    }
});