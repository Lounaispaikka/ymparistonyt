Ext.ns('Lougis');
Ext.application({
    name: "Lougis",
    appFolder: '/js/lougis',
    requires: [
        'Lougis.History',
        'Lougis.view.Panel'
    ],
    users: [],
    controllers: [
        'Lougis.controller.Navigation',
        'Lougis.controller.TabContent',
        'Lougis.controller.Profile'
    ],
    defaultController: "Lougis.controller.TabContent",
    defaultUrl: '/etusivu',
    loggedUser: null,
    launch: function() {
        Lougis.App = this; 
       this.loadLoggedUser();
        Ext.create('Lougis.view.TabViewport');
        Lougis.History.init();
    },
    getConrollerFromUrl: function(href) {
        var parts = href.split("/");
        if(parts[1] == 'profiili') return this.getController("Lougis.controller.Profile");
        return this.getController(this.defaultController);
    },
    getDefaultUrl: function() {
        return this.defaultUrl;
    },
    loadLoggedUser: function() {
        this.loggedUser = Ext.create('Lougis.model.User');
        Ext.Ajax.request({
            url: '/run/lougis/usersandgroups/jsonLoggedUserInfo/',
            scope: this,
            success: function(response) {
                var userInfo = Ext.decode(response.responseText);
                this.loggedUser.set('id', userInfo.id);
                this.loggedUser.set('firstname', userInfo.firstname);
                this.loggedUser.set('lastname', userInfo.lastname);
                this.loggedUser.set('email', userInfo.email);
                this.loggedUser.set('organization', userInfo.organization);
                this.loggedUser.set('phone', userInfo.phone);
                Ext.getCmp('profileButton').setText(this.loggedUser.get('email'));
            }
        }); //lisätty ; vg
    },
    getLoggedUser: function() {
        return this.loggedUser;
    }
});