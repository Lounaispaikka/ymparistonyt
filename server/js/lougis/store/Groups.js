Ext.define('Lougis.store.Groups', {
    extend: 'Ext.data.Store',
    model: 'Lougis.model.Group',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: '/run/lougis/usersandgroups/jsonListGroupsWithUsers/',
        reader: {
            type: 'json',
            root: 'groups'
        }
    }
});