Ext.define('Mip.model.Table', {
    extend: 'Ext.data.Model',
    requires: ['Mip.model.Attribute'],
    fields: [
        {name: 'id',     type: 'int'},
        {name: 'schema', type: 'string'},
        {name: 'name',  type: 'string'}
    ],
    hasMany  : {model: 'Mip.model.Attribute', name: 'attributes'},
    getAlias: function() {
        var alias = this.get('name');
        return alias;
    }
});