Ext.define('Lougis.controller.Profile', {
    extend: 'Ext.app.Controller',
    requires: [],
    title: "Profiili",
    init: function() {
        this.control({
        });
    },
    createPage: function(href) {
        Ext.create('Lougis.view.UsersAndGroups', {
            user: Lougis.App.getLoggedUser()
        });
    }
});
