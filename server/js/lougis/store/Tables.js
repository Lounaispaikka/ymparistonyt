Ext.define('Mip.store.Tables', {
    extend: 'Ext.data.Store',
    model: 'Mip.model.Table',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: '/data/databasesAndTables.php',
        reader: {
            type: 'json',
            root: 'tables'
        }
    }
});
