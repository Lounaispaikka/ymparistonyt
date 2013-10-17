Ext.define('Mip.store.SQLOrderBy', {
    extend: 'Ext.data.Store',
    fields: [
        {name: 'operation', type: 'string'},
        {name: 'title', type: 'string'}
    ],
    data: [
        {operation: 'ASC', title: 'Nouseva'},
        {operation: 'DESC', title: 'Laskeva'}
    ]
});