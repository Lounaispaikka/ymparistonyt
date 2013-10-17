Ext.define('Lougis.model.User', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'firstname', type: 'string'},
        {name: 'lastname', type: 'string'},
        {name: 'email', type: 'string'},
        {name: 'organization', type: 'string'},
        {name: 'phone', type: 'string'},
        {name: 'password', type: 'string'},
        {name: 'password_again', type: 'string'},
        // used by groups
        {name: 'isAdminOfAGroup', type: 'boolean', defaultValue: false}
    ],
    proxy: {
        type: 'rest',
        url : '/run/lougis/usersandgroups/editUser/',
        appendId: false
    },
    getForm: function() {
        var basicItems = [{
            xtype: 'hiddenfield',
            name: 'id',
            value: this.get('id')
        },{
            xtype: 'textfield',
            name: 'firstname',
            fieldLabel: 'Etunimi',
            value: this.get('firstname'),
            allowBlank: false,
            listeners: {
                change: function(field, newValue) {
                    this.set('firstname', newValue)
                },
                scope: this
            }
        },{
            xtype: 'textfield',
            name: 'lastname',
            fieldLabel: 'Sukunimi',
            value: this.get('lastname'),
            allowBlank: false,
            listeners: {
                change: function(field, newValue) {
                    this.set('lastname', newValue)
                },
                scope: this
            }
        },{
            xtype: 'textfield',
            name: 'email',
            vtype: 'email',
            fieldLabel: 'Sähköposti',
            value: this.get('email'),
            allowBlank: false,
            listeners: {
                change: function(field, newValue) {
                    this.set('email', newValue)
                },
                scope: this
            }
        },{
            xtype: 'textfield',
            name: 'organization',
            fieldLabel: 'Organisaatio',
            value: this.get('organization'),
            listeners: {
                change: function(field, newValue) {
                    this.set('organization', newValue)
                },
                scope: this
            }
        },{
            xtype: 'textfield',
            name: 'phone',
            fieldLabel: 'Puhelin',
            value: this.get('phone'),
            listeners: {
                change: function(field, newValue) {
                    this.set('phone', newValue)
                },
                scope: this
            }
        }
        ];

        var passwordField = Ext.create('Ext.form.field.Text', {
            name: 'password',
            fieldLabel: 'Salasana',
            allowBlank: this.get("id") != 0,
            inputType: 'password',
            listeners: {
                change: function(field, newValue) {
                    this.set('password', newValue)
                },
                scope: this
            }
        });
        var passwordAgainField = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'password_again',
            fieldLabel: 'Salasana uudestaan',
            allowBlank: this.get("id") != 0,
            inputType: 'password',
            listeners: {
                change: function(field, newValue) {
                    this.set('password_again', newValue)
                },
                scope: this
            },
            validator: function() {
                if(passwordAgainField.getValue() != passwordField.getValue()) return "Salasanat eivät täsmää";
                return true;
            }
        });


        var items = [{
            xtype: 'fieldset',
            title: 'Perustiedot',
            items: basicItems,
            defaults: {
                anchor: '100%'
            }
        },{
            xtype: 'fieldset',
            title: this.get('id') == 0? "Aseta salasana": "Vaihda salasana",
            items: [passwordField, passwordAgainField],
            defaults: {
                anchor: '100%'
            }
        }];
        return items;
    },
    getFullnameWithEmail: function() {
        return this.get("firstname")+" "+this.get("lastname")+" &lt;"+this.get("email")+"&gt;";
    }
});