Ext.define('Lougis.controller.Navigation', {
    extend: 'Ext.app.Controller',
    requires: [],
    refs: [
        {
            ref: 'tabs',
            selector: '#tabs'
        }
    ],

    init: function() {
        this.control({
            'navigation button[action=openpage]': {
                click: function(button) {
                    Lougis.App.getConrollerFromUrl(button.target).createPage(button.target);
                },
                scope: this
            },
            'navigation splitbutton': {
                mouseover: function(button) {
                    button.showMenu();
                },
                click: function(button) {
                    button.showMenu();
                },
                scope: this
            },
            'navigation button[action=logout]': {
                click: function(button) {
                    Ext.Msg.confirm("Vahvista uloskirjaus", "Haluatko varmasti kirjautua ulos?");
                },
                scope: this
            },
            'navigation menuitem': {
                click: function(button) {
                    Lougis.App.getConrollerFromUrl(button.target).createPage(button.target);
                },
                scope: this
            }
        });
    }
});
