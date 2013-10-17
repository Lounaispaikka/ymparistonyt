Ext.define('Lougis.store.Users', {
    extend: 'Ext.data.Store',
    model: 'Lougis.model.User',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: '/run/lougis/usersandgroups/jsonListUsers/',
        reader: {
            type: 'json',
            root: 'users'
        }
    }
});