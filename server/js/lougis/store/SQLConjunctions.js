Ext.define('Mip.store.SQLConjunctions', {
    extend: 'Ext.data.Store',
    fields: [
        {name: 'conjunction', type: 'string'},
        {name: 'title', type: 'string'}
    ],
    data: [
        {conjunction: 'AND', title: 'ja'},
        {conjunction: 'OR', title: 'tai'}
    ]
});