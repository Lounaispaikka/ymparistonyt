Ext.define('Lougis.view.UsersAndGroups', {
    extend: 'Lougis.view.Panel',
    alias: 'widget.usersandgroups',
    editEnabled: true,
    deleteEnabled: true,
    createEnabled: true,
    user: null,
    items: [],
    initComponent: function() {

        if(this.user) {
            this.showUserWindow(this.user);
        }

        else {
            this.callParent();

            if(!Ext.getStore('userStore')) Ext.create('Lougis.store.Users', {
                storeId: 'userStore'
            });
            if(!Ext.getStore('groupStore')) Ext.create('Lougis.store.Groups', {
                storeId: 'groupStore'
            });

            this.userGrid = Ext.create('Ext.grid.Panel', {
                columns: [
                    {header: "Etunimi", dataIndex: "firstname", flex: 1},
                    {header: "Sukunimi", dataIndex: "lastname", flex: 1},
                    {header: "Sähköposti", dataIndex: "email", flex: 1},
                    {header: "Organisaatio", dataIndex: "organization", flex: 1},
                    {header: "Muokkaa", width: 50, align: 'center', renderer: function() {return  '<img src="/img/icons/16x16/pencil.png" class="clickable" />';}}
                ]
                ,store: 'userStore'
                ,multiSelect: false
                ,emptyText: "Ei käyttäjiä"
                ,autoScroll: true
                ,anchor: '100%'
                ,title: "Rekisteröityneet käyttäjät"
                ,bbar: [
                    Ext.create('Ext.form.Label', {
                        text: "Suodata käyttäjiä:",
                        style: {marginTop: '2px', marginRight: '2px'}
                    }),
                    Ext.create('Ext.form.TextField', {
                        emptyText: "Suodata käyttäjiä nimen, sähköpostin tai organisaation perusteella",
                        width: 400,
                        enableKeyEvents: true,
                        listeners: {
                            keyup: this.filterUserStore,
                            scope: this
                        }
                    }),
                    '->',
                    Ext.create('Ext.Button', {
                        text: "Luo uusi käyttäjä",
                        icon: '/img/icons/16x16/add.png',
                        scope: this,
                        handler: function() {
                            this.showUserWindow(Ext.create('Lougis.model.User'));
                        }
                    })
                ],
                listeners: {
                    scope: this,
                    itemclick: function(view, record, item, index, event, options) {
                        this.showUserWindow(record);
                    }
                }
                ,height: 250
            });
            this.add(this.userGrid);

            this.groupGrid = Ext.create('Ext.grid.Panel', {
                columns: [
                    {header: "Nimi", dataIndex: "name", flex: 1},
                    {header: "Kuuluu ryhmään", dataIndex: "parent_id", flex: 1, renderer: function(parentId) {
                            var store = Ext.getStore('groupStore');
                            var record = store.getById(parentId);
                            return !Ext.isEmpty(record)? record.get("name"): '';
                        }},
                    {header: "Kuvaus", dataIndex: "description", flex: 1},
                    {header: "Muokkaa", width: 50, align: 'center', renderer: function() {return  '<img src="/img/icons/16x16/pencil.png" class="clickable" />';}}
                ]
                ,store: 'groupStore'
                ,multiSelect: false
                ,emptyText: "Ei ryhmiä"
                ,autoScroll: true
                ,anchor: '100%'
                ,title: "Ryhmät"
                ,bbar: [
                    Ext.create('Ext.form.Label', {
                        text: "Suodata ryhmiä:",
                        style: {marginTop: '2px', marginRight: '2px'}
                    }),
                    Ext.create('Ext.form.TextField', {
                        emptyText: "Suodata ryhmiä nimen tai kuvauksen perusteella",
                        width: 400,
                        enableKeyEvents: true,
                        listeners: {
                            keyup: this.filterGroupStore,
                            scope: this
                        }
                    }),
                    '->',
                    Ext.create('Ext.Button', {
                            text: "Luo uusi ryhmä",
                            icon: '/img/icons/16x16/add.png',
                            scope: this,
                            handler: function() {
                                this.showGroupWindow(Ext.create('Lougis.model.Group'));
                            }
                    })
                ]
                ,height: 250,
                listeners: {
                    scope: this,
                    itemclick: function(view, record, item, index, event, options) {
                        this.showGroupWindow(record);
                    }
                }
            });
            this.add(this.groupGrid);
        }
    }
    ,filterUserStore: function(textfield, event) {
        var store = Ext.getStore('userStore');
        var value = textfield.getValue().toLowerCase();
        if(Ext.isEmpty(value)) {
            store.clearFilter();
        }
        else {
            store.filterBy(function(record, id) {
                var text = (record.get('firstname')+record.get('lastname')+record.get('email')+record.get('organization')).toLowerCase();
                return (text.indexOf(value) != -1);
            }, this);
        }
    }
    ,filterGroupStore: function(textfield, event) {
        var store = Ext.getStore('groupStore');
        var value = textfield.getValue().toLowerCase();
        if(Ext.isEmpty(value)) {
            store.clearFilter();
        }
        else {
            store.filterBy(function(record, id) {
                var text = (record.get('name')+record.get('description')).toLowerCase();
                return (text.indexOf(value) != -1);
            }, this);
        }
    }
    ,showUserWindow: function(user) {
    	//if( typeof console != 'undefined' ) console.log(user);
        user.beginEdit();
        var userForm = user.getForm();

        var formPanel = Ext.create('Ext.form.Panel', {
            items: userForm,
            bodyStyle: 'padding: 5px',
            buttonAlign: 'left',
            buttons: [
                Ext.create('Ext.Button', {
                    text: 'Poista',
                    icon: '/img/icons/16x16/delete.png',
                    hidden: (user.get('id') == 0),
                    handler: function() {
                        Ext.Msg.confirm("Oletko varma?", "Haluatko varmasti poistaa käyttäjän?", function(buttonId) {
                            if(buttonId == 'yes') {
                                Ext.Ajax.request({
                                    url: '/run/lougis/usersandgroups/deleteUser/',
                                    params: {
                                        userId: user.get('id')
                                    },
                                    scope: this,
                                    success: function() {
                                        Ext.getStore('userStore').load();
                                        window.close();
                                    }

                                })
                            }
                        }, this);
                    },
                    scope: this
                }),
                '->',
                Ext.create('Ext.Button', {
                    text: 'Tallenna',
                    icon: '/img/icons/16x16/disk.png',
                    handler: function() {
                        var form = formPanel.getForm();
                        if(form.isValid()) {
                            user.commit();
                            user.endEdit();
                            user.save();
                            Ext.getStore('userStore').load();
                            window.close();
                        }
                        else Ext.Msg.alert("Virhe lomakkeen tiedoissa", "Täytä kaikki lomakkeen kentät kunnollisilla arvoilla.");

                    },
                    scope: this
                })
            ]
        });

        var window = Ext.create('Ext.window.Window', {
            width: 500,
            height: 330,
            title: user.get('id') == 0? "Luo uusi käyttäjä": "Käyttäjän tiedot",
            layout: 'fit',
            items: [
                formPanel
            ]
        });
        window.on('close', function() {
            user.cancelEdit();
        }, this);
        window.show();
    }
    ,showGroupWindow: function(group) {
        group.beginEdit();
        var groupForm = group.getForm();

        var formPanel = Ext.create('Ext.form.Panel', {
            items: groupForm,
            bodyStyle: 'padding: 5px',
            buttonAlign: 'left',
            buttons: [
                Ext.create('Ext.Button', {
                    text: 'Poista ryhmä',
                    icon: '/img/icons/16x16/delete.png',
                    hidden: (group.get('id') == 0),
                    handler: function() {
                        Ext.Msg.confirm("Oletko varma?", "Haluatko varmasti poistaa ryhmän?", function(buttonId) {
                            if(buttonId == 'yes') {
                                Ext.Ajax.request({
                                    url: '/run/lougis/usersandgroups/deleteGroup/',
                                    params: {
                                        groupId: group.get('id')
                                    },
                                    scope: this,
                                    success: function() {
                                        Ext.getStore('groupStore').load();
                                        window.close();
                                    }

                                })
                            }
                        }, this);
                    },
                    scope: this
                }),
                '->',
                Ext.create('Ext.Button', {
                    text: 'Tallenna',
                    icon: '/img/icons/16x16/disk.png',
                    handler: function() {
                        var form = formPanel.getForm();
                        if(form.isValid()) {
                            group.commit();
                            group.endEdit();
                            var users = [];
                            group.users().each(function(user) {
                                users.push({
                                    'user_id': user.get('id'),
                                    'group_admin': user.get('isAdminOfAGroup')
                                });
                            }, this);
                            Ext.Ajax.request({
                                url: '/run/lougis/usersandgroups/editGroup/',
                                params: {
                                    users: Ext.encode(users),
                                    id: group.get('id'),
                                    name: group.get('name'),
                                    public_joining: group.get('public_joining'),
                                    description: group.get('description'),
                                    parent_id: group.get('parent_id')
                                },
                                success: function() {
                                    Ext.getStore('groupStore').load();

                                },
                                scope: this
                            });
                            window.close();
                        }
                        else Ext.Msg.alert("Virhe lomakkeen tiedoissa", "Täytä kaikki lomakkeen kentät kunnollisilla arvoilla.");
                    },
                    scope: this
                })
            ]
        });

        var window = Ext.create('Ext.window.Window', {
            width: 700,
            height: 600,
            title: group.get('id') == 0? "Luo uusi ryhmä": "Muokkaa ryhmää",
            layout: 'fit',
            items: [
                formPanel
            ]
        });
        window.show();
        window.on('close', function() {
            group.cancelEdit();
        }, this);
        window.show();
    }
});
