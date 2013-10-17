Ext.define('Lougis.view.News', {
    extend: 'Lougis.view.Panel',
    alias: 'widget.news',
    id: 'LougisNewsWidget',
	anchor: '100% 100%',
	border: 0,
    items: [],
    treeData: [],
    initComponent: function() {
    
		this.callParent();
		
		this.newsTreePanel = this.createNewsTreePanel();
		this.newsEditorPanel = this.createNewsEditorPanel();
		this.newsPagesPanel = this.createNewsPagesPanel();
		//this.newsStartPanel = this.createNewsStartPanel();
		
		this.newsPanel = Ext.create('Ext.panel.Panel', {
			layout: 'border',
			border: 0,
			anchor: '100% 100%',
			items: [ this.newsTreePanel, this.newsEditorPanel, this.newsPagesPanel ]
		});
		
		this.add(this.newsPanel);
        
    }
    
    ,createNewsTreePanel: function() {
    	
		this.newsTreeStore = Ext.create('Ext.data.TreeStore', {
			fields: [ 'text', 'news_id' ],
			proxy: {
				type: 'ajax',
				url: '/run/lougis/news/newsTreeJson/'
			},
			folderSort: false,
			root: null
		});
    	
		return Ext.create('Ext.tree.Panel', {
			id: 'newsTreePanel',
			title: 'Arkisto',
			region:'west',
			store: this.newsTreeStore,
			width: 300,
			split: true, //resizable
			collapsible: true,   // make collapsible
			layout: 'fit',
    		scroll: 'both',
			anchor: '250 100%',
    		rootVisible: false, 
	        allowContainerDrop: false,
    		viewConfig: {
	            plugins: {
	                ptype: 'treeviewdragdrop'
	            }
	        },
			buttonAlign: 'left',
			buttons: [
				Ext.create('Ext.Button', {
					text: 'Uusi tiedote',
					icon: '/img/icons/16x16/page_edit.png',
					scope: this,
				    handler: function() {
				    	if ( this.newsEditorPanel.disabled ) this.newsEditorPanel.enable();
				    	if ( this.newsPagesPanel.disabled ) this.newsPagesPanel.enable();
				    	this.newsEditorPanel.getForm().reset();
				    	this.cleanPagesTreeFromChecks();
				    	CKEDITOR.instances.newsCKEditorField.setData("");
				    	Ext.getCmp('newsTitle').focus();
				    }
				})
				/*,
				'->',
				Ext.create('Ext.Button', {
					text: 'Tallenna Järjestys',
					icon: '/img/icons/16x16/disk.png',
				    scope: this,
				    handler: function() {
				    	this.saveNewsTreeSort();
				    }
				})
				*/
			],
			listeners: {
				itemclick: {
					scope: this,
					fn: function( view, record, item, index ){
						if ( record.data.news_id != null ) {
							this.loadNewsToEditor( record.data.news_id );
						}
					}
				}
			}
		});
    	
    }
    
    ,createNewsEditorPanel: function() {
    
		
		this.newsEditorPanel = Ext.create('Ext.form.Panel', {
			id: 'newsContentEditorPanel',
			itemId: 'newsContentEditorTab',
    		url: '/run/lougis/news/saveNews/',
			region: 'center',
			disabled: true,
			title: 'Tiedote',
			anchor: '100% 100%',
			autoScroll: true,
			buttonAlign: 'left',
			bodyPadding: '10 10 10 10',
			defaultType: 'textarea',
			items: this.getNewsFormItems(),
			buttons: [
    			Ext.create('Ext.Button', {
					text: 'Tallenna ',
					icon: '/img/icons/16x16/disk.png',
					width: 150,
					scope: this,
				    handler: function() {
				    	
				    	var checkedPages = this.__getCheckedPageIds();
				    	
				    	this.newsEditorPanel.getForm().submit({
							scope: this,
							params: {
								news_content: CKEDITOR.instances.newsCKEditorField.getData(),
								news_pages: Ext.JSON.encode(checkedPages)
							},
							success: function(form, action) {
								var res = Ext.JSON.decode(action.response.responseText);
								if ( res.success ) {
									form.findField('news_id').setValue(res.news_id);
									Ext.Msg.alert('Tallennus onnistui', res.msg);
								} else {
									Ext.Msg.alert('Virhe!', res.msg);
								}
								this.reloadNewsTreeStore();
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
							}
						});
						
				    }
				}),
				'->',
				Ext.create('Ext.Button', {
					text: 'Poista',
					icon: '/img/icons/16x16/delete.png',
					width: 100,
					scope: this,
				    handler: function() {
				    	if ( confirm("Haluatko varmasti poistaa tämän tiedotteen? Toimintoa ei voi peruuttaa.") ) {
				    		
					    	Ext.Ajax.request({
								url: '/run/lougis/news/deleteNews/',
								scope: this,
								params: {
									news_id: this.newsEditorPanel.getForm().getFieldValues().news_id
								},
								success: function( xhr ){
									var res = Ext.JSON.decode(xhr.responseText);
									if ( res.success ) {
										this.reloadNewsTreeStore();
										this.newsEditorPanel.disable();
										Ext.Msg.alert('Tiedote poistettu', res.msg);
									} else {
										Ext.Msg.alert('Virhe!', res.msg);
									}
								}
							});
				    		
				        }
				        
				    }
				})
			]
		});
    	return this.newsEditorPanel;
    
    }
    
    ,createNewsPagesPanel: function() {// {
    	
		this.newsPagesStore = Ext.create('Ext.data.TreeStore', {
			fields: [ 'text', 'page_id' ],
			proxy: {
				type: 'ajax',
				url: '/run/lougis/cms/checkPagesJson/'
			},
			folderSort: false,
			root: null
		});
		
		this.newsPagesPanel = Ext.create("Ext.tree.Panel", {
			id: 'newsPagesPanel',
			title: 'Tiedotteen linkitys',
			region: 'east',
			store: this.newsPagesStore,
			width: 300,
			disabled: true,
			split: true, //resizable
			collapsible: true,   // make collapsible
			collapsed: true,
			layout: 'fit',
			autoScroll: true,
    		scroll: 'both',
    		rootVisible: false, 
			xtype: 'treepanel',
			useArrows: true
		});
		
		return this.newsPagesPanel;
		
		//console.log(this.newsPagesGrid);
			
    
    }
    
    ,createNewsStartPanel: function() {
    
    	return Ext.create('Ext.panel.Panel', {
			id: 'newsStartPanel',
			region: 'center',
			anchor: '100% 100%',
			layout: 'fit',
			defaults: {
				bodyPadding: 50
			},
			html: '<div style="padding: 20% 0 0 0;text-align:center;"><b>Valitse muokattava tiedote arkistosta<br/>tai luo uusi tiedote.</b></div>'
		});
    
    }
    
    ,reloadNewsTreeStore: function() {
    
    	this.newsTreeStore.getRootNode().removeAll();
    	this.newsTreeStore.load();
    
    }
    
    ,loadNewsToEditor: function( newsId ) {
    	
    	if ( this.newsEditorPanel.disabled ) this.newsEditorPanel.enable();
    	if ( this.newsPagesPanel.disabled ) this.newsPagesPanel.enable();
    	
		Ext.Ajax.request({
			url: '/run/lougis/news/getNewsJson/',
			scope: this,
			params: {
				news_id: newsId
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					this.updateNewsForm( res.data );
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
		
    }
    
    ,updateNewsForm: function( newsData ) {
    	
    	this.newsEditorPanel.getForm().setValues( newsData );
    	contentData = "";
    	if ( newsData.content.length > 0 ) contentData = newsData.content;
    	if ( typeof CKEDITOR.instances.newsCKEditorField !== "undefined" ) {
    		CKEDITOR.instances.newsCKEditorField.setData( contentData );
    	} else {
    		Ext.getCmp('newsCKEditorTextArea').setValue( contentData );
    	}
    	this.checkNewsPages( newsData.pages );
    	
    }
    
    ,checkNewsPages: function( newsPages ) {
    	
    	var root = this.newsPagesPanel.getRootNode();
    	this.__iterateCheckedPages( root.childNodes, newsPages );
    
    }
    
    ,cleanPagesTreeFromChecks: function() {
    
    	var root = this.newsPagesPanel.getRootNode();
    	this.__iterateCheckedPages( root.childNodes, [] );
    	
    }
    
    ,__iterateCheckedPages: function( branch, checkedPages ) {
    
    	Ext.each( branch, function( leaf, index ) {
    		if ( Ext.Array.contains(checkedPages, leaf.data.page_id) && leaf.data.checked != true ) {
    			leaf.set('checked', 'checked');
    		} else {
    			if ( leaf.data.checked != false ) leaf.set('checked', false);
    		}
    		if ( leaf.childNodes.length > 0 ) this.__iterateCheckedPages( leaf.childNodes, checkedPages );
    	}, this);
    	
    }
    
    ,__getCheckedPageIds: function() {
    
    	var checkedNodes = this.newsPagesPanel.getChecked();
    	var checkedPages = [];
    	
    	Ext.each( checkedNodes, function( node ){
    		checkedPages.push(node.data.page_id);
    	}, this);
    	
    	return checkedPages;
    
    }
    
    ,getNewsFormItems: function( newsData ) {
    	
    	if ( typeof newsData === 'undefined' ) {
    	
    		newsData = {
    			news_id: null,
    			title: null,
    			published: true
    		};
    		
    	}
    	
    	if ( typeof this.newsCKEditorField == "undefined" ) {
			this.newsCKEditorField = Ext.create('Ext.form.field.TextArea', {
				id: 'newsCKEditorTextArea',
				inputId: 'newsCKEditorField',
				xtype: 'textarea',
			    scope: this,
				anchor: '100% 100%',
				margin: '0 0 0 0',
				width: 700,
				height: 500,
				name: 'news[content]'
			});
			this.newsCKEditorField.on('afterrender', function( container, layout ){
				
				//var editoHeight = Ext.getBody().getHeight()-290;
				CKEDITOR.replace( 'newsCKEditorField', {
					toolbar: 'Lougis'
					,language: 'fi'
					,width: 700
					,height: 400
				});
				
			});
		}
    
		var newsFormItems = [
		        {
		            name: 'news_id',
		            xtype: 'hidden',
		            value: newsData.news_id
		        },
		        {
		            xtype: 'checkbox',
		            fieldLabel: 'Julkaistu',
		            name      : 'news[published]',
	                inputValue: 'true',
	                checked	  : newsData.published
		        },
		        {
		        	xtype: 'textfield',
		        	id: 'newsTitle',
		            fieldLabel: 'Otsikko',
		            name: 'news[title]',
		            width: 680,
		            maxLength: 250,
		            value: newsData.title,
		            enableKeyEvents: true
		        }
		        ,{
		        	xtype: 'textarea',
		        	id: 'newsDescription',
		            fieldLabel: 'Lyhyt kuvaus',
		            name: 'news[description]',
		            width: 680,
		            height: 50,
		            maxLength: 250,
		            value: newsData.description
		        },
		        {
		        	xtype: 'datefield',
		        	id: 'newsDate',
		            fieldLabel: 'Pvm',
		            name: 'news[created_date]',
		            format: "d.m.Y",
		            altFormats: "j.n.y|j.n.Y|j.m.y|j.m.Y|d.n.y|d.n.Y|d.m.y",
		            value: newsData.created_date
		        },
		        {
		        	xtype: 'textfield',
		        	id: 'newsSource',
		            fieldLabel: 'Lähde',
		            name: 'news[source]',
		            width: 680,
		            maxLength: 250,
		            value: newsData.source
		        },
		        {
		        	xtype: 'textfield',
		        	id: 'newsSourceUrl',
		            fieldLabel: 'Lähdelinkki',
		            name: 'news[source_url]',
		            width: 680,
		            maxLength: 250,
		            value: newsData.source_url
		        },
		        {
                                xtype: 'radiogroup',
                                id: 'newsNewsType',
                                fieldLabel: 'Tyyppi',
                                columns: 2,
                                width: 300,
                                defaults: {
                                name: 'news[news_type]'
                       },
                                items: [
                                        { boxLabel: 'Uutinen', inputValue: '0', checked: newsData.news_type },
                                        { boxLabel: 'Tapahtuma', inputValue: '1', checked: newsData.news_type }
                                ]
                                
                               
		        
                        }
                        ,this.newsCKEditorField
		        /*
		        , {
		        	xtype: 'textarea',
		        	id: 'newsPages',
		            fieldLabel: 'Sivut',
		            name: 'news[pages]',
		            width: 600,
		            height: 50,
		            maxLength: 250,
		            value: null
		        }
		        */
		];
		
		return newsFormItems;
    	
    }
    
    ,saveNewsTreeSort: function() {
    
    	this.treeData = [];
		this.treeData = this._recurseNewsTree( this.newsTreeStore.getRootNode() );
		
		Ext.Ajax.request({
			url: '/run/lougis/news/saveTreeSort/',
			scope: this,
			params: {
				tree_data: Ext.JSON.encode(this.treeData)
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Tallennus onnistui', res.msg);
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
    
    }
    
    ,_recurseNewsTree: function( leaf ) {
    	var branch = [];
    	leaf.eachChild(function( kid ){
			branch.push(kid.data.news_id);
    	}, this);
    	return branch;
    	
    }
    
});