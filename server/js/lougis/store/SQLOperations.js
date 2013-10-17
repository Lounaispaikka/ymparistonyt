Ext.define('Mip.store.SQLOperations', {
    extend: 'Ext.data.Store',
    fields: [
        {name: 'operation', type: 'string'},
        {name: 'title', type: 'string'},
        {name: 'wrapBeforeValue', type: 'string'},
        {name: 'wrapAfterValue', type: 'string'},
        {name: 'includeValue', type: 'boolean'}
    ],
    data: [
        {operation: '=', title: '=', wrapBeforeValue: "'", wrapAfterValue: "'", includeValue: true},
        {operation: '<>', title: '≠', wrapBeforeValue: "'", wrapAfterValue: "'", includeValue: true},
        {operation: '>', title: '>', wrapBeforeValue: "'", wrapAfterValue: "'", includeValue: true},
        {operation: '<', title: '<', wrapBeforeValue: "'", wrapAfterValue: "'", includeValue: true},
        {operation: 'ILIKE', title: 'sisältää', wrapBeforeValue: "'%", wrapAfterValue: "%'", includeValue: true},
        {operation: 'NOT ILIKE', title: 'ei sisällä', wrapBeforeValue: "'%", wrapAfterValue: "%'", includeValue: true},
        {operation: 'IS NULL', title: 'on tyhjä', wrapBeforeValue: "", wrapAfterValue: "", includeValue: false},
        {operation: 'IS NOT NULL', title: 'ei ole tyhjä', wrapBeforeValue: "", wrapAfterValue: "", includeValue: false}
    ]
});