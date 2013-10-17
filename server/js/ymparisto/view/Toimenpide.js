/**
 * Returns Ext.data.store records as an array.
 * @method
 * @member Ext
 * @author Pyry Liukas
 */
Ext.getStoreDataAsArray = function( store ) {
	var data = [];
	Ext.each(store.getRange(), function(record) {
		data.push(record.data);
	});
	return data;
};

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*',
    'Ext.form.*',
    'Ext.ux.CheckColumn',
    'Ext.selection.CheckboxModel'
]);
Ext.define('Ymparisto.view.Toimenpide', {
    extend: 'Lougis.view.Panel',
    alias: 'widget.toimenpide',
    id: 'YmparistoToimenpide',
	anchor: '100% 100%',
	border: 0,
    items: [],
    treeData: [],
    autoupdateToimenpideEditor: true,
    initComponent: function() {
    
		this.callParent();
		
		this.arviointiTreePanel = this.createArviointiTreePanel();
		//this.toimenpideEditorPanel = this.createToimenpideEditorPanel();
		this.arviointiStartPanel = this.createArviointiStartPanel();
		
		this.centerPanel = Ext.create('Ext.panel.Panel', {
			region: 'center',
			anchor: '100% 100%',
			layout: 'fit',
			border: 0,
			items: [ this.arviointiStartPanel ]
		});
		
		this.arviointiView = Ext.create('Ext.panel.Panel', {
			layout: 'border',
			anchor: '100% 100%',
			border: 0,
			items: [ this.arviointiTreePanel, this.centerPanel ]
		});
		
		this.add(this.arviointiView);
        
    }
    
    
    ,createArviointiStartPanel: function() {
    
    	return Ext.create('Ext.panel.Panel', {
			region: 'center',
			anchor: '100% 100%',
			layout: 'fit',
			border: 0,
			defaults: {
				bodyPadding: 50
			},
			html: '<div style="padding: 20% 0 0 0;text-align:center;"><b>Valitse arviointi kierros tai toimenpide</b></div>'
		});
    
    }
    
    ,createArviointiTreePanel: function() {
    	
		this.arviointiTreeStore = Ext.create('Ext.data.TreeStore', {
			id: 'arviointiTreeStore',
			fields: [ 'kierros_id', 'kysely_id', 'type', 'text' ],
			proxy: {
				type: 'ajax',
				url: '/run/ymparisto/toimenpide/arviointikierrosTree/'
			},
			folderSort: false,
			root: null,
			listeners: {
				load: {
					scope: this,
					fn: function( store, node, records, success, eOpts ) {
						
						/*
				    	if ( this.currentToimenpideRecord != null ) {
				    		this.currentToimenpideRecord = store.getRootNode().findChild('page_id',  this.currentToimenpideRecord.page_id).data;
					    	if ( this.autoupdateToimenpideEditor == true ) this.loadToimenpideToEditor( this.currentToimenpideRecord );
				    	}
				    	*/
					}
				}
			}
		});
    	
		return Ext.create('Ext.tree.Panel', {
			id: 'toimenpideTreePanel',
			title: 'Arviointikierrokset ja toimenpiteet',
			region:'west',
			store: this.arviointiTreeStore,
			width: 400,
			split: true, //resizable
			collapsible: true,   // make collapsible
			layout: 'fit',
    		scroll: 'both',
			anchor: '250 100%',
    		rootVisible: false, 
    		autoScroll: true,
	        allowContainerDrop: false,
			buttonAlign: 'left',
			listeners: {
				itemclick: {
					scope: this,
					fn: function( view, record, item, index ){
						switch(record.data.type) {
							case 'kierros':
								this.loadKierrosToEditor( record.data.kierros_id );
							break;
							case 'kysely':
								this.loadKyselyToEditor( record.data.kysely_id );
							break;
						}
					}
				}
			},
			fbar: [
				{
					type: 'button',
					text: 'Uusi kierros',
					icon: '/img/icons/16x16/disk.png',
					scope: this,
					handler: this.newKierros
				}
			]
		});
    	
    }
    
    ,reloadArviointiTreeStore: function() {
    
    	this.arviointiTreeStore.getRootNode().removeAll();
    	this.arviointiTreeStore.load();
    	
    }
    
    ,newKierros: function() {
    
		this.centerPanel.setLoading(false);
		this.centerPanel.removeAll(true);
		this.centerPanel.add( this.getKierrosForm() );
		this.centerPanel.setLoading(false);
        
    }
    
    ,getKierrosForm: function( kierrosData ) {
	    
	    var newKierros = true;
	    var reminder1Date = new Date();
	    var reminder2Date = new Date();
	    var closingDate = new Date();
	    var startedDate = new Date();
	    
	    if ( typeof kierrosData == 'undefined' ) {
	    
		    reminder1Date.setDate(reminder1Date.getDate()+7);
		    reminder2Date.setDate(reminder2Date.getDate()+14);
		    closingDate.setDate(closingDate.getDate()+21);
	    
		    kierrosData = {
			    id: null,
			    title1: "Arviointikierros - ",
			    title2: "Toimenpiteen etenemisen arviointi",
			    title3: "Arvio toimenpiteen toteutumisesta vuoden 2013 loppuun mennessä",
			    content1: "Arvioi toimenpidettä oman työsi/toimintasi vinkkelistä. Anna arvio, miten toimenpide tulee toteutumaan Lounais-Suomessa vuoden 2013 loppuun mennessä ja kirjoita perustelu arviollesi. Perusteluissa voit antaa myös esimerkkejä ja numerotietoa.",
			    email_tpl: '<p>Hei,</p><p>Olet yst&auml;v&auml;llisesti lupautunut asiantuntijaksi ymp&auml;rist&ouml;ohjelman seurantaan.</p><p>Vuoden 2012 seurantakierros Ymp&auml;rist&ouml; Nyt &ndash;palvelussa on k&auml;ynnistynyt. Ole hyv&auml; ja arvioi toimenpiteen &quot;<strong>[TOIMENPITEEN_NIMI]</strong>&quot; etenemist&auml; [ARVIOINTI_SULKEUTUU_PVM] menness&auml;.</p><p>Sait aiemmin s&auml;hk&ouml;postiisi viestin otsikolla: &rdquo;Ohje ymp&auml;rist&ouml;ohjelman arviointiin&rdquo;, josta l&ouml;yd&auml;t tarkemmat tiedot liittyen arviointiteht&auml;v&auml;&auml;n.</p><p>Siirry arviointilomakkeeseen t&auml;st&auml; linkist&auml;: [LINKKI_ARVIOINTIKYSELYYN]</p><p>Jos t&auml;m&auml; viesti p&auml;&auml;tyi suoraan roskapostiin tai sinulla on muita teknisi&auml; ongelmia, ota yhteytt&auml;: <a href="mailto:ymparisto@lounaispaikka.fi">ymparisto@lounaispaikka.fi</a></p><p><em><strong>Yhdess&auml; parempaan ymp&auml;rist&ouml;&ouml;n!</strong></em></p><p>Koordinaatioryhm&auml;</p>',
			    kysely_idt: [],
			    in_process: false,
			    notes: null
		    }
	    } else {
		    var newKierros = false;
		    reminder1Date.setTime(Date.parse(kierrosData.reminder1_date.substr(0, 10)));
		    reminder2Date.setTime(Date.parse(kierrosData.reminder2_date.substr(0, 10)));
		    closingDate.setTime(Date.parse(kierrosData.closing_date.substr(0, 10)));
		    startedDate.setTime(Date.parse(kierrosData.started_date.substr(0, 10)));
		    
	    }
	    
	    
		if (  kierrosData.in_process ) {
			return { 
				xtype: 'panel',
				html: '<div style="padding: 20% 0 0 0;text-align:center;">'+kierrosData.notes+'</div>'
			}
		}
	    
	    this.currentKierros = kierrosData;
	    
		var toimenpiteetGrid = this.createToimenpiteetGrid( kierrosData.kysely_idt );
			
	    var kierrosForm = Ext.create('Ext.form.Panel', {
    		url: '/run/ymparisto/toimenpide/saveKierros/',
			disabled: false,
			title: 'Arviointikierros',
			//anchor: '100% 100%',
			autoScroll: true,
			buttonAlign: 'left',
			bodyPadding: '10 10 10 10',
			defaultType: 'textfield',
			defaults: {
				labelWidth: 125
			},
			items: [

					{
			            xtype: 'hidden',
			            name: 'kierros[id]',
			            value: kierrosData.id
			        },
			        {
			            fieldLabel: 'Kierroksen nimi',
			            name: 'kierros[title1]',
			            value: kierrosData.title1,
			            width: 600
			        },
			        {
			        	xtype: 'datefield',
			            fieldLabel: '1. muistutus',
			            name: 'kierros[reminder1_date]',
			            format: "d.m.Y",
			            altFormats: "j.n.y|j.n.Y|j.m.y|j.m.Y|d.n.y|d.n.Y|d.m.y",
			            value: reminder1Date
			        },
			        {
			        	xtype: 'datefield',
			            fieldLabel: '2. muistutus',
			            name: 'kierros[reminder2_date]',
			            format: "d.m.Y",
			            altFormats: "j.n.y|j.n.Y|j.m.y|j.m.Y|d.n.y|d.n.Y|d.m.y",
			            value: reminder2Date
			        },
			        {
			        	xtype: 'datefield',
			            fieldLabel: 'Kierros päättyy',
			            name: 'kierros[closing_date]',
			            format: "d.m.Y",
			            altFormats: "j.n.y|j.n.Y|j.m.y|j.m.Y|d.n.y|d.n.Y|d.m.y",
			            value: closingDate
			        },
			        {
			            fieldLabel: 'Lomakkeen otsikko',
			            name: 'kierros[title2]',
			            value: kierrosData.title2,
			            width: 600
			        },
			        {
			        	xtype: 'textarea',
			            fieldLabel: 'Lomakkeen teksti',
			            inputId: 'kierros_content1',
			            name: 'kierros[content1]',
			            value: kierrosData.content1,
						listeners: {
							afterrender: function() {
								 if ( typeof CKEDITOR.instances.kierros_content1 != 'undefined' ) CKEDITOR.instances.kierros_content1.destroy( true );
								CKEDITOR.replace( 'kierros_content1', { toolbar: 'Lougis',language: 'fi',width: 530,height: 250 });
							}
						}
			        },
			        {
			            fieldLabel: 'Arvioinnin otsikko',
			            name: 'kierros[title3]',
			            value: kierrosData.title3,
			            width: 600
			        },
			        {
			        	xtype: 'textarea',
			            fieldLabel: 'Sähköpostin pohja',
			            inputId: 'kierros_email_tpl',
			            name: 'kierros[email_tpl]',
			            value: kierrosData.email_tpl,
						listeners: {
							afterrender: function() {
								if ( typeof CKEDITOR.instances.kierros_email_tpl != 'undefined' ) CKEDITOR.instances.kierros_email_tpl.destroy( true );
								CKEDITOR.replace( 'kierros_email_tpl', { toolbar: 'Lougis',language: 'fi',width: 530,height: 250 });
							}
						}
			        },
			        {
				        xtype: 'displayfield',
				        margin: '10 0 10 125',
				        value: "Koodit: [TOIMENPITEEN_NIMI], [ARVIOINTI_SULKEUTUU_PVM], [LINKKI_ARVIOINTIKYSELYYN] (pakollinen)"
			        },
			        {
						xtype: 'displayfield',
						fieldLabel: '',
						value: 'Kierroksessa arvioitavat toimenpiteet'
					},
					toimenpiteetGrid

			],
			buttons: [
    			{
					text: 'Tallenna',
					icon: '/img/icons/16x16/disk.png',
					scope: this,
				    handler: function(button) {
				    	var form = button.up('form').getForm();
				    	var toimenpiteet = this.getValitutToimenpideIdt();
				    	
				    	form.setValues({
					    	"kierros[content1]": CKEDITOR.instances.kierros_content1.getData(),
					    	"kierros[email_tpl]": CKEDITOR.instances.kierros_email_tpl.getData()
				    	});
				    	form.submit({
							scope: this,
							params: {
								toimenpiteet: toimenpiteet.join(',')
							},
							success: function(form, action) {
								var res = Ext.JSON.decode(action.response.responseText);
								if ( res.success ) {
									Ext.Msg.alert('Tallennus onnistui', res.msg);
									this.reloadArviointiTreeStore();
								} else {
									Ext.Msg.alert('Virhe!', res.msg);
								}
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
							}
						});
					}
				}
			]
			
		});
		
		if ( newKierros ) {
			
			
			var peruutaBtn = {
				xtype: 'button',
				dock: 'bottom',
				text: 'Peruuta',
				icon: '/img/icons/16x16/delete.png',
				scope: this,
				handler: function() {
					var msg = 'Haluatko varmasti peruuttaa uuden kierroksen luomisen? Kaikki tekemäsi muutokset häviävät?';
					Ext.Msg.confirm('Peruuta kierroksen luonti', msg, function(button){
						if ( button === 'yes' ) {
								this.centerPanel.removeAll(true);
								this.centerPanel.add( this.createArviointiStartPanel() );
						}
					}, this);
				}
			}
			var fbar = kierrosForm.getDockedComponent(0);
			fbar.add(['->', peruutaBtn]);
			
		} else {
			
			var sentInfo = {
				xtype: 'displayfield',
				fieldLabel: 'Kierros aloitettu',
				value: 'Kierrosta ei ole vielä aloitettu.<br/>Aloita kyselykierros klikkaamalla "Aloita kierros"-nappia".'
			}
			if ( kierrosData.notes.length > 1 ) sentInfo.value = kierrosData.notes;
			
			kierrosForm.insert(2, sentInfo);
			
			if (  !kierrosData.closed  ) {
				if ( kierrosData.published ) {
					
					var kierrosBtn = {
						xtype: 'button',
						dock: 'bottom',
						text: 'Sulje kierros',
						icon: '/img/icons/16x16/application_form_delete.png',
						scope: this,
						handler: function() {
							var msg = 'Haluatko varmasti sulkea arviointikierroksen?';
							Ext.Msg.confirm('Sulje kierros', msg, function(button){
								if ( button === 'yes' ) this.suljeKierros();
							}, this);
						}
					}
					
				} else {
					
					var kierrosBtn = {
						xtype: 'button',
						dock: 'bottom',
						text: 'Aloita kierros',
						icon: '/img/icons/16x16/email_go.png',
						scope: this,
						handler: function() {
							var msg = 'Haluatko varmasti aloittaa arviointikierroksen ja lähettää arviointiviestit kaikille toimenpiteiden asiantuntijoille?';
							Ext.Msg.confirm('Aloita kierros', msg, function(button){
								if ( button === 'yes' ) this.aloitaKierros();
							}, this);
						}
					}
					
				}
				
				var fbar = kierrosForm.getDockedComponent(0);
				fbar.add(['->', kierrosBtn]);
			}
			
			var poistaBtn = {
				xtype: 'button',
				text: 'Poista kierros',
				icon: '/img/icons/16x16/delete.png',
				scope: this,
				handler: function() {
					var msg = 'Haluatko varmasti poistaa koko arviointikierroksen?';
					Ext.Msg.confirm('Poista kierros', msg, function(button){
						if ( button === 'yes' ) this.poistaKierros();
					}, this);
				}
			}
			kierrosForm.add(poistaBtn);
			
		}
		
		return kierrosForm;
		
    }
    
    ,suljeKierros: function() {
	    
		Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/suljeKierros/',
			scope: this,
			params: {
				kierros_id: this.currentKierros.id
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Kierros suljettu', res.msg);
					this.loadKierrosToEditor( this.currentKierros.id );
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
						
	    
    }
    
    ,aloitaKierros: function() {
	    
		Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/aloitaKierros/',
			scope: this,
			params: {
				kierros_id: this.currentKierros.id
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Kierros aloitettu', res.msg);
					this.loadKierrosToEditor( this.currentKierros.id );
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
						
	    
    }
    
    ,poistaKierros: function() {
	    
	    this.centerPanel.setLoading(true);

		Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/poistaKierros/',
			scope: this,
			params: {
				kierros_id: this.currentKierros.id
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Kierros poistattu', res.msg);
					this.reloadArviointiTreeStore();
					this.centerPanel.removeAll(true);
					this.centerPanel.add( this.createArviointiStartPanel() );
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
				this.centerPanel.setLoading(false);
			}
		});
						
	    
    }
    
    ,createToimenpiteetStore: function() {
	                    
	    return Ext.create('Ext.data.Store', {
	    	id: 'toimenpiteetStore',
	        autoDestroy: true,
	        autoLoad: true,
	        fields: [
	        	'title',
	            { name: 'page_id', type: 'int' }
	        ],
	        proxy: {
	            type: 'ajax',
	            url: '/run/ymparisto/toimenpide/getToimenpiteetStoreData/',
	            reader: {
		            type: 'json',
		            root: null
	            }
	        },
	        sorters: [{
	            property: 'title',
	            direction: 'ASC'
	        }],
	        listeners: {
		        load: {
			        scope: this,
			        fn: function(){
					    if ( this.currentKierros.kysely_idt.length > 0 && typeof this.selectedToimenpiteet != 'undefined' ) {
					    	var selectedRecords = [];
						   	Ext.Array.each(this.currentKierros.kysely_idt, function(valittu_id, idx) {
							   	selectedRecords.push( this.toimenpiteetStore.findRecord('page_id', valittu_id) );
						   	}, this);
						   	this.selectedToimenpiteet.select( selectedRecords );
					   	}
			        }
			        
		        }
	        }
	    });       
	    
    }
    
    ,createToimenpiteetGrid: function() {
	    /*
	    if ( typeof this.toimenpiteetStore != 'undefined' ) Ext.destroy(this.toimenpiteetStore);
	    if ( typeof this.selectedToimenpiteet != 'undefined' ) Ext.destroy(this.selectedToimenpiteet);
	    */
	    this.toimenpiteetStore = this.createToimenpiteetStore();
	   	
	   	this.selectedToimenpiteet = Ext.create('Ext.selection.CheckboxModel',{
	   		/*
		   	listeners: {
			   	selectionchange: {
			   		scope: this,
			   		fn: function(sm, selected) {
			   			console.log( selected, this.currentKierros.kysely_idt );
			   			
			   		}
			   	}
		   	}
		   	*/
	   	});
	   	
		var grid = Ext.create('Ext.grid.Panel', {
			store: this.toimenpiteetStore,
			margin: '10 0 10 130',
			autoScroll: true,
			selModel: this.selectedToimenpiteet,
			columns: [
				{text: "Toimenpiteet", dataIndex: 'title', flex: 1}
			],
			columnLines: true,
			width: 500,
			height: 300,
			iconCls: 'icon-grid'
		});
	   	
	    return grid;
	    
    }
    
    ,getValitutToimenpideIdt: function() {
	    
	    if ( typeof this.selectedToimenpiteet == 'undefined' ) return [];
	    
	    var idt = [];
	    Ext.Array.each(this.selectedToimenpiteet.selected.items, function(itm, index) {
		    idt.push(itm.data.page_id);
	    });
	    return idt;
	    
    }
    
    ,loadKierrosToEditor: function( kierros_id ) {
    	
    	this.centerPanel.setLoading(true);
    	
		Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/getKierrosDetails/',
			scope: this,
			params: {
				kierros_id: kierros_id,
				html: true	
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				this.centerPanel.removeAll(true);
				this.centerPanel.add( this.getKierrosForm( res.kierros ) );
				this.centerPanel.setLoading(false);
			}
		});
		
    }
    
    ,loadKyselyToEditor: function( kysely_id ) {
    	
		Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/getKyselyDetails/',
			scope: this,
			params: {
				kysely_id: kysely_id
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				this.loadKyselyForm( res.kysely, res.asiantuntijat, res.arvioinnit );
			}
		});
		
    }
    
    ,loadKyselyForm: function( kyselyData, asiantuntijatArray, arvioinnitArray ) {
    	
		this.centerPanel.removeAll( true );
		
		this.currentKysely = kyselyData;
		this.asiantuntijaGrid = this.createAsiantuntijaGrid( asiantuntijatArray );
		this.arvioinnitGrid = this.createArvioinnitGrid( arvioinnitArray );
		
    	this.kyselyForm = Ext.create('Ext.form.Panel', {
			id: 'kyselyForm',
			itemId: 'kyselyFormPanel',
    		url: '/run/ymparisto/toimenpide/saveKysely/',
			region: 'center',
			disabled: false,
			title: 'Toimenpiteen arviontikysely',
			//anchor: '100% 100%',
			autoScroll: true,
			buttonAlign: 'left',
			bodyPadding: '10 10 10 10',
			defaultType: 'textfield',
			defaults: {
				labelWidth: 125
			},
			fbar: [
    			{
					text: 'Tallenna',
					icon: '/img/icons/16x16/disk.png',
					width: 150,
					scope: this,
				    handler: function() {
				    
				    	var asiantuntijat = Ext.getStoreDataAsArray(this.asiantuntijaStore);
				    	
				    	this.kyselyForm.submit({
							scope: this,
							params: {
								asiantuntijat: Ext.JSON.encode(asiantuntijat)
							},
							success: function(form, action) {
								var res = Ext.JSON.decode(action.response.responseText);
								if ( res.success ) {
									Ext.Msg.alert('Tallennus onnistui', res.msg);
									this.reloadArviointiTreeStore();
								} else {
									Ext.Msg.alert('Virhe!', res.msg);
								}
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
							}
						});
						
					}
				},
				'->',
				{
					text: 'Poista',
					icon: '/img/icons/16x16/delete.png',
					width: 150,
					scope: this,
				    handler: function() {
				    	var msg = 'Haluatko varmasti aloittaa poistaa kyselyn tältä kierrokselta?';
						Ext.Msg.confirm('Poista kysely', msg, function(button){
							if ( button === 'yes' ) this.poistaKysely();
						}, this);
				    }
				}
			],
			items: [
				{
		            name: 'kysely[id]',
		            xtype: 'hidden',
		            value: kyselyData.id
		        },
				{
		            name: 'kysely[kierros_id]',
		            xtype: 'hidden',
		            value: kyselyData.kierros_id
		        },
				{
		            name: 'kysely[page_id]',
		            xtype: 'hidden',
		            value: kyselyData.page_id
		        },
		        {
		        	
		        	xtype: 'fieldcontainer',
		        	fieldLabel: 'Asiantuntijat',
		        	items: [
		        		this.asiantuntijaGrid
		        	]
		        },
		        {
		        	xtype: 'fieldcontainer',
		        	fieldLabel: 'Arvioinnit',
		        	items: [
		        		this.arvioinnitGrid
		        	]
		        }

			]
		});
		
        this.sendBtn = Ext.create('Ext.Button', {
				text: 'Lähetä kyselyviestit',
				icon: '/img/icons/16x16/email.png',
				scope: this,
				dock: 'right',
			    handler: function() {
			    	this.sendKyselyMessages( this.currentKysely );
			    }
			});
        this.kyselyForm.add(this.sendBtn);
		
		this.centerPanel.add( this.kyselyForm );
    
    }
    
    ,poistaKysely: function() {
	    
		this.centerPanel.setLoading(true);
	    Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/poistaKysely/',
			scope: this,
			params: {
				kysely_id: this.currentKysely.id
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Kysely poistettu', res.msg);
					this.reloadArviointiTreeStore();
					this.centerPanel.removeAll(true);
					this.centerPanel.add( this.createArviointiStartPanel() );
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
				this.centerPanel.setLoading(false);
			}
		});
		
	    
    }
    
    ,sendKyselyMessages: function( kyselyData ) {
    	
    	Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/sendKyselyMessages/',
			scope: this,
			params: {
				kysely_id: kyselyData.id
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Viestit asiantuntijoille lähetetty', res.msg);
					this.loadKyselyToEditor(this.currentToimenpideRecord);
				} else {
					Ext.Msg.alert('Viestien lähetys epäonnistui', res.msg);
				}
			}
		});
		
    }
    
    ,updateEmailTemplates: function() {
    
    	vals = this.kyselyForm.getValues();
    	sda = vals['kysely[sending_date]'].split('.');
    	reminderDays = parseInt(vals['kysely[reminder_interval_days]']);
    	sendingDate = new Date(sda[2], sda[1], sda[0]);
    	reminderDate = new Date( sendingDate.getTime() + (1000*60*60*24*reminderDays) );
    
    	this.reminderDateString = reminderDate.getDate()+"."+reminderDate.getMonth()+"."+reminderDate.getFullYear();
    	this.kyselyForm.getForm().findField('kyselyMsg').setValue( this.msgTpl.applyTemplate([this.toimenpideTitle, this.reminderDateString]) );
    
    }
    
    ,createAsiantuntijaGrid: function( asiantuntijatArray ) {
    
    	    Ext.define('Asiantuntija', {
		        extend: 'Ext.data.Model',
		        fields: [
		            'email',
		            'firstname',
		            'lastname',
		            'organization',
		            { name: 'admin', type: 'bool' }
		        ]
		    });
		                
		    this.asiantuntijaStore = Ext.create('Ext.data.Store', {
		    	id: 'asiantuntijaStore',
		        autoDestroy: true,
		        model: 'Asiantuntija',
		        proxy: {
		            type: 'memory'
		        },
		        data: asiantuntijatArray,
		        sorters: [{
		            property: 'admin',
		            direction: 'DESC'
		        },{
		            property: 'firstname',
		            direction: 'ASC'
		        }]
		    });        
		     
		    this.userComboStore = Ext.create('Ext.data.Store', {
		    	id: 'userStore',
		        autoDestroy: true,
		        model: 'Asiantuntija',
		        proxy: {
		            type: 'ajax',
		            url: '/run/ymparisto/toimenpide/listUsers/'
		        },
		        sorters: [{
		            property: 'email',
		            direction: 'ASC'
		        }]
		    });
		    this.userComboStore.load();
		    
		    this.organizationComboStore = Ext.create('Ext.data.Store', {
		    	id: 'organizationStore',
		    	fields: ['organization'],
		        autoDestroy: true,
		        proxy: {
		            type: 'ajax',
		            url: '/run/ymparisto/toimenpide/listOrganizations/'
		        }
		    });
		    this.organizationComboStore.load();
		    
		    Ext.grid.RowEditor.prototype.saveBtnText = 'Päivitä';
		    Ext.grid.RowEditor.prototype.cancelBtnText = 'Peruuta';
		    
		   	var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
		        clicksToMoveEditor: 1,
		        autoCancel: false
		    });
		
		    this.asiantuntijaGrid = Ext.create('Ext.grid.Panel', {
		    	id: 'asiantuntijaGridPanel',
		        store: this.asiantuntijaStore,
		        width: 'auto',
		        height: 200,
		        frame: false,
		        tbar: [{
		            text: 'Lisää asiantuntija',
		            icon: '/img/icons/16x16/add.png',
		            scope: this,
		            handler : function() {
		                rowEditing.cancelEdit();
						
		                var r = Ext.create('Asiantuntija', {
		                    firstname: '',
		                    lastname: '',
		                    email: '',
		                    organization: '',
		                    admin: null
		                });
		                this.asiantuntijaStore.insert(0, r);
		                rowEditing.startEdit(0, 0);
		            }
		        }, {
		            itemId: 'removeAsiantuntija',
		            text: 'Poista asiantuntija',
		            icon: '/img/icons/16x16/delete.png',
		            scope: this,
		            handler: function() {
		                var sm = this.asiantuntijaGrid.getSelectionModel();
		                rowEditing.cancelEdit();
		                this.asiantuntijaStore.remove(sm.getSelection());
		                if (this.asiantuntijaStore.getCount() > 0) {
		                    sm.select(0);
		                }
		            },
		            disabled: true
		        }],
		        plugins: [rowEditing],
		        listeners: {
		            'selectionchange': function(view, records) {
		                this.down('#removeAsiantuntija').setDisabled(!records.length);
		            }
		        },
		        
		        columns: [
		        	{
			            header: 'Sähköposti',
			            dataIndex: 'email',
			            width: 100,
			            flex: 1,
			            editor: {
			                allowBlank: true,
			                xtype: 'combobox',
							typeAhead: true,
							triggerAction: 'all',
							selectOnTab: true,
							store: this.userComboStore,
							queryMode: 'local',
							displayField: 'email',
							valueField: 'email',
							//listClass: 'x-combo-list-small',
							lazyRender: true,
							listeners: {
								scope: this,
								select: function(combo, records){
									this.asiantuntijaStore.removeAt(0);
		                			this.asiantuntijaStore.insert(0, records[0]);
		                			rowEditing.cancelEdit();
									
								}
							}
			            }
			        },
			        {
			            header: 'Etunimi',
			            dataIndex: 'firstname',
			            flex: 1,
			            width: 100,
			            editor: {
			                allowBlank: true
			            }
			        },{
			            header: 'Sukunimi',
			            dataIndex: 'lastname',
			            flex: 1,
			            width: 100,
			            editor: {
			                allowBlank: true
			            }
			        },{
			            header: 'Organisaatio',
			            dataIndex: 'organization',
			            width: 100,
			            flex: 1,
			            editor: {
			                allowBlank: true,
			                xtype: 'combobox',
							typeAhead: true,
							triggerAction: 'all',
							selectOnTab: true,
							store: this.organizationComboStore,
							queryMode: 'local',
							displayField: 'organization',
							valueField: 'organization',
							listClass: 'x-combo-list-small',
							lazyRender: true			            }
			        },{
			            xtype: 'checkcolumn',
			            header: 'Vastuuhenkilö',
			            dataIndex: 'admin',
			            width: 100,
			            editor: {
			                xtype: 'checkbox',
			                cls: 'x-grid-checkheader-editor'
			            }
			        }
		        ]
		        
		    });
    		
    		return this.asiantuntijaGrid;
    		
    }
    
    ,createArvioinnitGrid: function( arvioinnitArray ) {
    
    	    Ext.define('Arviointi', {
		        extend: 'Ext.data.Model',
		        fields: [
		        	{
			        	name: 'id',
			        	type: 'int'
		        	},
		            { 
		            	name: 'sent_date', 
		            	type: 'date',
		            	dateFormat: 'U'
		            },
		            'reminders',
		            'user_name',
		            { 
		            	name: 'arvio_date', 
		            	type: 'date',
		            	dateFormat: 'U'
		            },
		            'arvio_arvo'
		        ]
		    });
		                
		    this.arviointiStore = Ext.create('Ext.data.Store', {
		    	id: 'asiantuntijaStore',
		        autoDestroy: true,
		        model: 'Arviointi',
		        proxy: {
		            type: 'memory'
		        },
		        data: arvioinnitArray,
		        sorters: [{
		            property: 'sent_date',
		            direction: 'ASC'
		        }]
		    });
			
		    this.arviointiGrid = Ext.create('Ext.grid.Panel', {
		    	id: 'arviointiGridPanel',
		        store: this.arviointiStore,
		        width: 'auto',
		        height: 200,
		        frame: false,
                multiSelect: false,
                scope: this,
		        listeners: {
		        	itemclick: {
		        		scope: this,
		        		fn: function(grid, record) {
			        		this.showVastausWin( record.data.id );
			        	}
		        	}
		        },
		        
		        columns: [
			        {
			            header: 'Lähetetty',
			            dataIndex: 'sent_date',
			            flex: 1,
			            xtype: 'datecolumn',
			            format: 'd.m.Y H:i:s'
			        },{
			            header: 'Muistutuksia',
			            dataIndex: 'reminders',
			            width: 70
			        },{
			            header: 'Asiantuntija',
			            dataIndex: 'user_name',
			            flex: 1
			        },{
			            header: 'Arvioitu',
			            dataIndex: 'arvio_date',
			            flex: 1,
			            xtype: 'datecolumn',
			            format: 'd.m.Y H:i:s'
			        },{
			            header: 'Arvosana',
			            dataIndex: 'arvio_arvo',
			            flex: 1
			        },{
			        	header: "Tiedot", 
			        	width: 50, 
			        	align: 'center', 
			        	renderer: function(var1, var2) {
			        		return  '<img src="/img/icons/16x16/information.png" class="rowinfo" />';
			        	}
			        }
		        ]
		        
		    });
    		
    		return this.arviointiGrid;
    		
    }
    
    ,showVastausWin: function( vastausId ) {
    	
	    var vastausWin = this.createVastausWin();
	    vastausWin.show();
	    vastausWin.setLoading(true);
	    Ext.Ajax.request({
			url: '/run/ymparisto/toimenpide/getVastausData/',
			scope: this,
			params: {
				vastaus_id: vastausId
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				var form = this.buildVastausForm( res.vastaus );
				vastausWin.add(form);
				vastausWin.setLoading(false);
			}
		});

	    
    }
    
    ,buildVastausForm: function( vData ) {
    
	    return {
		    xtype: 'form',
		    defaults: {
			    xtype: 'displayfield',
			    labelWidth: 140
		    },
		    bodyPadding: 10,
		    items: [
		    	{ fieldLabel: 'Arviointi id', value: vData.id },
		    	{ fieldLabel: 'Arvioija', value: vData.user.firstname+' '+vData.user.lastname+' - '+vData.user.organization },
		    	{ fieldLabel: 'Lähetetty pvm', value: vData.sent_date },
		    	{ fieldLabel: 'Muistutus 1. pvm', value: vData.reminder1_date },
		    	{ fieldLabel: 'Muistutus 2. pvm', value: vData.reminder2_date },
		    	{ fieldLabel: 'Arviointi tallennettu', value: vData.arvio_date },
		    	{ fieldLabel: 'Arvio arvo', value: vData.arvio_arvo },
		    	{ fieldLabel: 'Arvio perustelu', value: vData.arvio_perustelu },
		    	{ fieldLabel: 'Linkki lomakkeeseen', value: '<a href="'+vData.url_private+'" target="_blank">'+vData.url_private+'</a>' }
		    ]
	    }
	    
    }
    
    ,createVastausWin: function() {
	    
	    return Ext.create('widget.window', {
            title: 'Toimenpiteen arviointi',
            closable: true,
            width: 650,
            minWidth: 350,
            height: 600,
            modal: true,
            autoScroll: 'auto',
            //bodyStyle: 'padding: 5px;',
			items: []
        });
	    
    }
            
});