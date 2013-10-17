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
    'Ext.ux.CheckColumn'
]);
Ext.define('Lougis.view.Charts', {
    extend: 'Lougis.view.Panel',
    alias: 'widget.charts',
    id: 'LougisCharts',
	anchor: '100% 100%',
	border: 0,
    items: [],
    treeData: [],
    fieldTypes: {
	    'string': 'Teksti',
		'int': 'Kokonaisluku',
		'float': 'Desimaaliluku',
		'bool': 'Totuusarvo'
    },
    initComponent: function() {
    
		this.callParent();
		
		this.chartTreePanel = this.createChartTreePanel();
		this.chartStartPanel = this.createChartStartPanel();
		
		this.centerPanel = Ext.create('Ext.panel.Panel', {
			region: 'center',
			anchor: '100% 100%',
			layout: 'fit',
			border: 0,
			items: [ this.chartStartPanel ]
		});
		
		this.chartPanel = Ext.create('Ext.panel.Panel', {
			layout: 'border',
			anchor: '100% 100%',
			border: 0,
			items: [ this.chartTreePanel, this.centerPanel ]
		});
		
		this.add(this.chartPanel);
        
    }
    
    ,createChartTreePanel: function() {
    	
                this.chartTreeStore = Ext.create('Ext.data.TreeStore', {
			fields: [ 'chart_id', 'text' ],
			proxy: {
				type: 'ajax',
				url: '/run/lougis/charts/getChartsJson/'
			},
			folderSort: false,
			root: null
		});
               
               this.treePanel = Ext.create('Ext.tree.Panel', {
			id: 'ChartTreePanel',
			title: 'Tilastot',
			region:'west',
			autoScroll: true,
			store: this.chartTreeStore,
			width: 250,
			split: true, //resizable
			collapsible: true,   // make collapsible
			layout: 'fit',
                        scroll: 'both',
			anchor: '250 100%',
                        rootVisible: false, 
                        allowContainerDrop: false,
			buttonAlign: 'left',
			listeners: {
				itemclick: {
					scope: this,
					fn: function( view, record, item, index ){
						this.loadChartToEditor( record.data.chart_id );
					}
				}
			},
			tbar: [
				{
					text: 'Uusi tilasto',
					icon: '/img/icons/16x16/chart_bar_add.png',
					scope: this,
					handler: this.startNewChart
				}
			]
		});
               // console.log(this.treePanel);
                return this.treePanel;
    	
    }
    
    ,reloadChartTreeStore: function() {
    
    	this.chartTreeStore.getRootNode().removeAll();
    	this.chartTreeStore.load();
    
    }
    
    ,createChartStartPanel: function() {
    
    	return Ext.create('Ext.panel.Panel', {
			id: 'ChartStartPanel',
			region: 'center',
			anchor: '100% 100%',
			layout: 'fit',
			border: 0,
			defaults: {
				bodyPadding: 50
			},
			html: '<div style="padding: 20% 0 0 0;text-align:center;"><b>Valitse tilasto</b></div>'
		});
    
    }
    
    ,loadChartToEditor: function( chartId ) {
    	
    	this.centerPanel.setLoading(true);
		Ext.Ajax.request({
			url: '/run/lougis/charts/getChartInfo/',
			scope: this,
			params: {
				chart_id: chartId
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				this.setChartToEditor( res.chart );
                                console.log(res.chart);
				//this.loadChartEditor( Chart, res.kysely_id, res.asiantuntijat, res.arvioinnit, res.titleTpl, res.msgTpl );
			}
		});
		
    }
    
    ,setChartToEditor: function( chartObj ) {
	    
	    this.centerPanel.removeAll(true);
	    var chartEditor = this.createChartEditor( chartObj );
	    this.centerPanel.add( chartEditor );
    	this.centerPanel.setLoading(false);
	    
    }
    
    ,createChartEditor: function( chartObj ) {
	    
	    var infoForm = this.createInfoForm( chartObj );
	    var dataGrid = this.createDataGrid( chartObj );
	    var chartForm = this.createChartBuilder( chartObj );
	    var iframeForm = this.createIframeForm( chartObj );
	    
	   	if ( chartObj.request != null ) {
			 
		   	Ext.each(chartObj.config.series, function(serie, idx) {
			   	this.addCurrentChartSeries( chartObj );
		   	}, this);
		   	chartForm.getForm().setValues( chartObj.request );
		   	
	   	}
	    
	    var chartEditor = {
		    xtype: 'tabpanel',
	    	title: 'Tilasto',
		    items: [ infoForm, dataGrid, chartForm, iframeForm ]
	    }
	    return chartEditor;
	    
    }
    
    ,createIframeForm: function( chartObj ) {
	    
	    
		this.iframePreview = Ext.create('Ext.form.FieldSet', {
	   		title: 'Esikatselu',
	   		items: [
	   			{
		   			xtype: 'panel',
					width: 500,
					height: 300,
					margin: '0 0 10 0',
					html: '<div style="padding: 150px 0 0 0;text-align: center"><b>Esikatselu</b></div>' 
	   			}
	   		]
		});
	    this.iframeCode = Ext.create('Ext.form.field.TextArea', {
			width: 500,
			height: 200,
			value: ''
	    });
	   	this.iframeForm = Ext.create('Ext.form.Panel', {
		   	xtype: 'form',
		   	id: 'iframeForm',
		   	title: 'Upotus',
    		//url: '/run/lougis/charts/saveChartConfig/',
			bodyPadding: 10,
		   	autoScroll: true,
		   	buttonAlign: 'left',
		   	items: [
		   		{
			   		xtype: 'fieldset',
			   		title: 'Ikkunan asetukset',
			   		items: [
				   		{
					   		xtype: 'numberfield',
					   		name: 'width',
					   		width: 180,
					   		fieldLabel: 'Leveys',
					   		allowDecimals: false,
					   		value: 450
				   		},
				   		{
					   		xtype: 'numberfield',
					   		name: 'height',
					   		width: 180,
					   		fieldLabel: 'Korkeus',
					   		allowDecimals: false,
					   		value: 270
				   		},
				   		{
				   			xtype: 'button',
				        	text: 'Luo koodi',
				        	margin: '10 0',
				        	scope: this,
							icon: '/img/icons/16x16/table_chart.png',
				        	handler: function( button ) {
				        		this.updateIframe( chartObj );
				        	}
			   			}
			   		]
		   		},
		   		this.iframePreview,
		   		{
			   		xtype: 'fieldset',
			   		title: 'Upotuskoodi',
			   		items: [ this.iframeCode ]
		   		}
		   	]
	    });
	    return this.iframeForm;
	    
    }
    
    ,updateIframe: function( chartObj ) {
    
    	this.iframePreview.setLoading(true);
    	var vals = this.iframeForm.getForm().getValues();
	    
	    Ext.Ajax.request({
			url: '/run/lougis/charts/buildIframeCode/',
			scope: this,
			params: {
				chart_id: chartObj.id,
				width: vals.width,
				height: vals.height
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					this.iframePreview.removeAll();
					var panel = {
						xtype: 'panel',
						width: parseInt(vals.width),
						height: parseInt(vals.height),
						padding: 0,
						margin: '0 0 10 0',
						html: res.code 
				    };
				    
				    this.iframePreview.add(panel);
					
					this.iframeCode.setValue(res.code);
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
				this.iframePreview.setLoading(false);
			}
		});	
	    
    }
    
    ,createChartBuilder: function( chartObj ) {
	   	
	   	this.currentChartPreview = Ext.create('Ext.panel.Panel', {
		   	id: 'currentChartPreview',
		   	title: 'Kaavion esikatselu',
		   	width: 600,
		   	height: 400,
		   	items: []
	   	});
	   	
	   	if ( chartObj.config != null ) this.updateChartPreview( chartObj, chartObj.config );
	   	
	   	var axisFields = [];
	   	var xChecked = true;
	   	var yChecked = false;
		Ext.each(chartObj.data.fields, function(field, idx) {
		
			var aField = {
				xtype: 'fieldcontainer',
				layout: 'hbox',
				items: [
					{
						xtype: 'fieldcontainer',
						fieldLabel: field.name,
						defaultType: 'checkboxfield',
						layout: 'hbox',
			   			width: 300,
						defaults: {
							margin: '0 10 0 0',
							flex: 1
						},
						items: [
							{ boxLabel: 'X-akseli', name: 'chart[axes][x][fields]['+idx+']', inputValue: field.dataindex },
							{ boxLabel: 'Y-akseli', name: 'chart[axes][y][fields]['+idx+']', inputValue: field.dataindex }
						]
					},
					{
						xtype: 'displayfield',
						value: '('+this.fieldTypes[field.type]+')'
					}
				]
			}
			axisFields.push(aField);
		}, this);
	   	
	   	this.currentChartSeries = Ext.create('Ext.form.FieldContainer');
	   	
	   	this.currentChartBuilder = Ext.create('Ext.form.Panel', {
		   	xtype: 'form',
		   	id: 'chartBuilderForm',
		   	title: 'Kaavio',
    		url: '/run/lougis/charts/saveChartConfig/',
			bodyPadding: 10,
		   	autoScroll: true,
		   	buttonAlign: 'left',
		   	items: [
		   		this.currentChartPreview,
				{
					xtype: 'hidden',
					value: chartObj.id,
					name: 'chart[id]'
				},
		   		{
		   			xtype: 'fieldset',
		   			title: 'Kaavion akselit',
		   			items: [
		   				axisFields,
		   				{
			   				xtype: 'textfield',
			   				fieldLabel: 'Y-akselin otsikko',
			   				name: 'chart[axes][y][title]',
			   				width: 500,
			   				value: null
		   				},
		   				{
			   				xtype: 'radiogroup',
			   				fieldLabel: 'Y-akselin tyyppi',
			   				columns: 2,
			   				width: 250,
			   				defaults: {
			   					name: 'chart[axes][y][type]'
			   				},
			   				items: [
			   					{ boxLabel: 'Numero', inputValue: 'Numeric', checked: true },
			   					{ boxLabel: 'Kategoria', inputValue: 'Category' }
			   				]
		   				},
		   				{
			   				xtype: 'textfield',
			   				fieldLabel: 'X-akselin otsikko',
			   				name: 'chart[axes][x][title]',
			   				width: 500,
			   				value: null
		   				},
		   				{
			   				xtype: 'radiogroup',
			   				fieldLabel: 'X-akselin tyyppi',
			   				columns: 2,
			   				width: 250,
			   				defaults: {
			   					name: 'chart[axes][x][type]'
			   				},
			   				items: [
			   					{ boxLabel: 'Numero', inputValue: 'Numeric' },
			   					{ boxLabel: 'Kategoria', inputValue: 'Category', checked: true }
			   				]
		   				}
		   			]
		   		},
		   		{
		   			xtype: 'fieldset',
		   			title: 'Kaavion legenda',
		   			items: [
				    	{
						   	xtype: 'fieldcontainer',
							layout: 'hbox',
							defaults: {
								margin: '0 10 0 0'	
							},
						   	items: [
				   				{
					   				xtype: 'radiogroup',
					   				fieldLabel: 'Näkyvissä',
					   				columns: 2,
					   				width: 220,
					   				defaults: {
					   					name: 'chart[legend][visible]'
					   				},
					   				items: [
					   					{ boxLabel: 'Kyllä', inputValue: 1, checked: true },
					   					{ boxLabel: 'Ei', inputValue: 0 }
					   				]
					   			},
					   			{
						   			xtype: 'displayfield',
						   			margin: '0 0 0 15',
						   			value: 'Kelluvan selitteen sijainti:'
					   			}
					   		]
		   				},
				    	{
						   	xtype: 'fieldcontainer',
							layout: 'hbox',
							defaults: {
								margin: '0 10 0 0'	
							},
						   	items: [
				   				{
									xtype: 'combo',
					   				fieldLabel: 'Sijainti',
									name: 'chart[legend][position]',
									store: Ext.create('Ext.data.Store', {
										fields: ['position', 'title'],
										data: [
											{ position: 'bottom', title: 'Alhaalla' },
											{ position: 'top', title: 'Ylhäällä' },
											{ position: 'right', title: 'Oikealla' },
											{ position: 'left', title: 'Vasemmalla' },
											{ position: 'float', title: 'Kelluva' }
										]
									}),
									queryMode: 'local',
									displayField: 'title',
									valueField: 'position',
									value: 'bottom'
								},
								{
									xtype: 'numberfield',
									fieldLabel: 'X',
									labelWidth: 20,
									width: 80,
									name: 'chart[legend][x]'
								},
								{
									xtype: 'numberfield',
									fieldLabel: 'Y',
									labelWidth: 20,
									width: 80,
									name: 'chart[legend][y]'
								}
							]
						}
		   			]
		   		},
		   		{
			   		xtype: 'fieldset',
			   		title: 'Kaavion kuvaajat',
			   		items: [
			   			{
				   			xtype: 'button',
				        	text: 'Lisää kuvaaja',
				        	margin: '10 0',
				        	scope: this,
							icon: '/img/icons/16x16/chart_line.png',
				        	handler: function( button ) {
				        		this.addCurrentChartSeries( chartObj );
				        	}
			   			},
			   			this.currentChartSeries
			   		]
		   		}
		   	],
		   	fbar: [
		   		
			   	{
					text: 'Esikatselu',
					scope: this,
					icon: '/img/icons/16x16/table_chart.png',
				    handler: function(button) {
				    	var form = button.up('form').getForm();
				    	form.submit({
							scope: this,
							params: {
								save: false	
							},
							success: function(form, action) {
								this.updateChartPreview( chartObj, action.result.conf );
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
							}
						});
				    }
				},
				'->',
			   	{
					text: 'Tallenna',
					scope: this,
					icon: '/img/icons/16x16/disk.png',
				    handler: function(button) {
				    	var form = button.up('form').getForm();
				    	form.submit({
							scope: this,
							params: {
								save: 'true'	
							},
							success: function(form, action) {
								if ( action.result.msg != null ) Ext.Msg.alert('Tallennus onnistui', action.result.msg);
								this.updateChartPreview( chartObj, action.result.conf );
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
							}
						});
				    }
				}
		   	]
	   	});
	   	return this.currentChartBuilder;
	    
    }
    
    ,updateChartPreview: function( chartObj, chartConf ) {
	    
	    this.currentChartPreview.setLoading(true);
	    this.currentChartPreview.removeAll(true);
	    
	    var chartPreviewStore = this.createChartStore( chartObj );
	    chartConf.store = chartPreviewStore;
	    chartConf.width = 550;
	    chartConf.height = 350;
	    
	    this.currentChartPreview.add( chartConf );
	    this.currentChartPreview.setLoading(false);
	    
    }
    
    ,addCurrentChartSeries: function( chartObj ) {
	    
	    serieId = this.currentChartSeries.items.length;
	    
	    if ( typeof picker == 'undefined' ) {
			var picker = Ext.create('Ext.picker.Color', {
				floating: true,
				autoShow: false,
				frame: true,
				style: {
					backgroundColor: '#fff'
				}
			});
		}
	    
	    
	   	var xFields = Ext.create('Ext.form.CheckboxGroup', {
	   		width: 120,
			columns: 1,   	
			margin: '0 10 5 0',	
			defaults: {
				margin: '0 10 0 0'
			},
			items: []
		});
	   	var yFields = Ext.create('Ext.form.CheckboxGroup', {
	   		width: 120,
			columns: 1,   	
			margin: '0 10 5 0',	
			defaults: {
				margin: '0 10 0 0'
			},
			items: []
		});
		Ext.each(chartObj.data.fields, function(field, idx) {
			var xField = { 
				boxLabel: field.name, 
				name: 'chart[series]['+serieId+'][xField]['+idx+']', 
				inputValue: field.dataindex
			}
			xFields.add(xField);
			var yField = { 
				boxLabel: field.name, 
				name: 'chart[series]['+serieId+'][yField]['+idx+']', 
				inputValue: field.dataindex
			}
			yFields.add(yField);
		}, this);
	    
	    var serieRow = {
		    xtype: 'fieldset',
		    //layout: 'hbox',
		    width: 'auto',
		    labelWidth: 60,
		    title: 'Kuvaaja '+(serieId+1),
		    defaults: {
				margin: '0 10 0 0'
		    },
		    items: [
		    
		    	{
				   	xtype: 'fieldcontainer',
				   	items: [
				   		{
					   		xtype: 'fieldcontainer',
						    layout: 'hbox',
						    width: 750,
					   		defaultType: 'displayfield',
					   		items: [
					   			{ value: 'Kuvaajan tyyppi', width: 200 },
					   			{ value: 'X-sarakkeet', width: 140 },
					   			{ value: 'Y-sarakkeet', width: 140 },
					   			{ value: 'Legendan otsikko', width: 100 }
					   		]
				   		}
				   	]
			   	},
			   	
		    	{
				   	xtype: 'fieldcontainer',
					layout: 'hbox',
				   	items: [
				   		{
					   		xtype: 'fieldcontainer',
					   		layout: 'vbox',
					   		width: 200,
					   		height: 140,
					   		items: [
								{
									xtype: 'combo',
									name: 'chart[series]['+serieId+'][type]',
									store: Ext.create('Ext.data.Store', {
										fields: ['type', 'title'],
										data: [
											{ type: 'line', title: 'Käyrä' },
											{ type: 'bar', title: 'Pylväs (vaaka)' },
											{ type: 'column', title: 'Pylväs (pysty)' },
										//	{ type: 'stack', title: 'Pino' },
											{ type: 'area', title: 'Alue' }
										]
									}),
									queryMode: 'local',
									displayField: 'title',
									valueField: 'type',
									value: 'line'
								},
								{ xtype: 'displayfield', value: 'Kuvaajan akseli' },
								{
									xtype: 'combo',
									name: 'chart[series]['+serieId+'][axis]',
									store: Ext.create('Ext.data.Store', {
										fields: ['type', 'title'],
										data: [
											{ type: 'left', title: 'Y-akseli' },
											{ type: 'bottom', title: 'X-akseli' }
										]
									}),
									queryMode: 'local',
									displayField: 'title',
									valueField: 'type',
									value: 'left'
								},
								{
									xtype: 'checkboxfield',
									name: 'chart[series]['+serieId+'][highlight]',
									value: true,
									boxLabel: 'Korosta kuvaaja'
								},
								{
									xtype: 'checkboxfield',
									name: 'chart[series]['+serieId+'][stacked]',
									value: true,
									boxLabel: 'Pinoa pylväät'
								}
					   		]	
				   		},
						xFields,
						yFields,
						{
							xtype: 'textfield',
							value: null,
							width: 200,
							name: 'chart[series]['+serieId+'][title]'
						},
						/*
						{
							xtype: 'textfield',
							value: '#0000FF',
							width: 60,
							margin: 0,
							name: 'chart[series]['+serieId+'][color]'
						},
						{
							xtype: 'button',
							icon: '/img/icons/16x16/color_swatch.png',
							handler: function(button){
								var colorfield = button.previousSibling('textfield');
								var btnPos = button.getPosition();
								picker.addListener('select', function(picker, selColor){
									colorfield.setValue('#'+selColor);
									//picker.clearListener('select');
									picker.hide();
								});
								picker.setPosition( btnPos[0], btnPos[1]-90 );
								picker.show();
							}
						},
						*/
						{
							xtype: 'button',
							text: 'Poista kuvaaja',
							margin: '0 0 0 10',
							handler: function( button ){
								var fc = button.up("fieldset");
								fc.destroy();
							}
						}
				   	
				   	]
				}
		    	
		    ]
	    }
	    
	    this.currentChartSeries.add(serieRow);
	    
    }
    
    ,createChartStore: function( chartObj ){
	    
	    var storeFields = [];
		Ext.each(chartObj.data.fields, function(field, idx) {
			var storeField = {
				name: field.dataindex,
				type: field.type
			}
			storeFields.push(storeField);
		}, this);
		
	    return Ext.create('Ext.data.ArrayStore', {
	        autoDestroy: true,
	        fields: storeFields,
	        data: chartObj.data.data
	    });
	    
    }
    
    ,createDataGrid: function( chartObj ) {
	    
	    this.currentChartStore = this.createChartStore( chartObj );
	    
		var gridColumns = [];
		Ext.each(chartObj.data.fields, function(field, idx) {
			var column = {
				dataIndex: field.dataindex,
				header: field.name,
				editor: {
					allowBlank: false
				}
			}
			gridColumns.push(column);
		}, this);
		
		var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
	        clicksToMoveEditor: 1
	    });
	    this.currentChartGrid = Ext.create('Ext.grid.Panel', {
	    	id: 'currentChartGrid',
	    	title: 'Taulukko',
	        store: this.currentChartStore,
	        width: 'auto',
	        frame: false,
            scope: this,
	        columns: gridColumns,
	        selModel: {
	            selType: 'cellmodel'
	        },
	        plugins: [rowEditing],
	        tbar: [
	        	{
		        	text: 'Lisää rivi',
		        	scope: this,
					icon: '/img/icons/16x16/table_row_insert.png',
		        	handler: function( button ) {
			        	rowEditing.cancelEdit();
			    		var row = {};
			    		Ext.each( chartObj.data.fields, function( field ){ 
			    			row[ field.dataindex ] = null;
			    		});
			    		this.currentChartStore.add(row);
			    		rowEditing.startEdit(this.currentChartStore.getTotalCount(), 0);
		        	}
	        	},
	        	{
		        	text: 'Poista rivi...',
		        	scope: this,
					icon: '/img/icons/16x16/table_row_delete.png',
		        	handler: function( button ) {
		        		var sm = this.currentChartGrid.getSelectionModel();
		                rowEditing.cancelEdit();
						Ext.Msg.confirm('Poista rivi', 'Haluatko varmasti poistaa valitun rivin?', function(button){
							if ( button === 'yes' ) {
				                this.currentChartStore.removeAt(sm.getCurrentPosition().row);
				                if (this.currentChartStore.getCount() > 0) {
				                    sm.select(0);
				                }
							}
						}, this);
		                
		        	}
	        	}
	        ],
	        fbar: [
	        	{
					text: 'Tallenna taulukko',
					scope: this,
					icon: '/img/icons/16x16/disk.png',
				    handler: function(button) {
				    	this.centerPanel.setLoading(true);
				    	var data = [];
				    	Ext.each(this.currentChartStore.data.items, function(record, idx) {
				    		var row = [];
				    		Ext.each( chartObj.data.fields, function( field ){ row.push(record.get(field.dataindex)) });
					    	data.push(row);
				    	}, this);
				    	Ext.Ajax.request({
							url: '/run/lougis/charts/updateData/',
							scope: this,
							params: {
								chart_id: chartObj.id,
								data: Ext.JSON.encode(data)
							},
							success: function( xhr ){
								var res = Ext.JSON.decode(xhr.responseText);
								if ( res.success ) {
									Ext.Msg.alert('Tallennus onnistui', res.msg);
								} else {
									Ext.Msg.alert('Virhe!', res.msg);
								}
								this.centerPanel.setLoading(false);
							}
						});	
				    }
				}
	        ],
	        bbar: Ext.create('Ext.PagingToolbar', {
	            pageSize: 50,
	            store: this.currentChartStore,
	            displayInfo: true
	        })
	        
	    });
		
		return this.currentChartGrid;
	    
    }
    
    ,createInfoForm: function( chartObj ) {
	   	
	   	var chartFieldsFieldset = this.getChartFieldsFieldset( chartObj.data.fields );
	    var infoForm = {
	    	xtype: 'form',
	    	title: 'Perustiedot',
			id: 'chartInfoForm',
    		url: '/run/lougis/charts/saveChartInfo/',
			bodyPadding: 10,
			border: 0,
			autoScroll: true,
			buttonAlign: 'right',
			frame: false,
			items: [
				{
					xtype: 'hiddenfield',
					name: 'chart[id]',
					value: chartObj.id	
				},
				{
					xtype: 'checkboxfield',
					fieldLabel: 'Julkaise tilasto indikaattorina',
					name: 'chart[published]',
					inputValue: 'true',
			                checked	  : chartObj.published	
				},
                                {
					xtype: 'textfield',
					fieldLabel: 'Tilaston otsikko',
					name: 'chart[title]',
					width: 350,
					value: chartObj.title	
				},
                             
		        {
		        	xtype: 'textarea',
		            fieldLabel: 'Tilaston lyhyt kuvaus (enintään 255 merkkiä)',
		            inputId: 'chart_short_description',
		            name: 'chart[short_description]',
                         width: 520,
						 height: 80,
						 maxLength: 255,
                         mmaxLengthText: 'Virhe: Pituus saa olla enintään 255 merkkiä',
		            value: chartObj.short_description/*,
					listeners: {
						afterrender: function() {
							 if ( typeof CKEDITOR.instances.chart_short_description != 'undefined' ) CKEDITOR.instances.chart_short_description.destroy( true );
							CKEDITOR.replace( 'chart_short_description', { toolbar: 'LougisCompact',language: 'fi',width: 530,height: 80 });
						}
					}*/
					
		        },		        
                        {
		        	xtype: 'textarea',
		            fieldLabel: 'Tilaston kuvaus',
		            inputId: 'chart_description',
		            name: 'chart[description]',
		            value: chartObj.description,
					listeners: {
						afterrender: function() {
							 if ( typeof CKEDITOR.instances.chart_description != 'undefined' ) CKEDITOR.instances.chart_description.destroy( true );
							CKEDITOR.replace( 'chart_description', { toolbar: 'Lougis',language: 'fi',width: 530,height: 250 });
						}
					}
		        },
				chartFieldsFieldset,
				{
					xtype: 'button',
					text: 'Poista tilasto',
					icon: '/img/icons/16x16/delete.png',
					scope: this,
					handler: function() {
						var msg = 'Haluatko varmasti poistaa koko tilaston? Tätä toimintoa ei voi peruuttaa!';
						Ext.Msg.confirm('Poista tilasto', msg, function(button){
							if ( button === 'yes' ) this.deleteChart( chartObj.id );
						}, this);
					}
				}
			],
			fbar: [
				{
					text: 'Tallenna tiedot',
					scope: this,
					icon: '/img/icons/16x16/disk.png',
				    handler: function(button) {
				    
				    	var form = button.up('form').getForm();
				    	form.setValues({
					    	"chart[description]": CKEDITOR.instances.chart_description.getData()
				    	});
				    	form.submit({
							scope: this,
							success: function(form, action) {
								Ext.Msg.alert('Tallennus onnistui', action.result.msg);
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
							}
						});
				    }
				}
			]
    	};
    	
    	return infoForm;
	    
    }
    
    
    ,deleteChart: function( chartId ) {
	    
	    this.centerPanel.setLoading(true);

		Ext.Ajax.request({
			url: '/run/lougis/charts/deleteChart/',
			scope: this,
			params: {
				chart_id: chartId
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Kierros poistettu', res.msg);
					this.reloadChartTreeStore();
					this.centerPanel.removeAll(true);
					this.centerPanel.add( this.createChartStartPanel() );
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
				this.centerPanel.setLoading(false);
			}
		});		
	    
    }
    
    ,startNewChart: function(  ) {
    
    	this.addNewDataPanel = this.createNewDataPanel();
    	
    	this.newChartWin = Ext.create('widget.window', {
            title: 'Luo uusi tilasto',
            closable: true,
            width: 700,
            minWidth: 350,
            height: 500,
            modal: true,
            autoScroll: 'auto',
            //bodyStyle: 'padding: 5px;',
			items: [ 
				this.addNewDataPanel
			]
        });
        
        
        if (this.newChartWin.isVisible()) {
            this.newChartWin.close();
        } else {
            this.newChartWin.show();
        }
    
    }
    
    ,createNewDataPanel: function() {
    
    	this.addNewDataPanel = Ext.create('Ext.form.Panel', {
			id: 'newDataForm',
			itemId: 'newDataFormPanel',
    		url: '/run/lougis/charts/uploadData/',
			bodyPadding: 10,
			border: 0,
			autoScroll: true,
			frame: false,
			items: [
				{
					border: 0,
					html: '<h1>Tilastodatan lataus</h1>'
						 +'<p>Voit ladata uutta tilastodataan portaaliin. Tilastodata ladataan CSV-tiedostona, jonka sisällön tulisi täyttää seuraavat kriteerit:</p>'
						 +'<p><img src="/img/tilasto-ohje.png" style="width:600px;" alt="" /></p>'
						 +'<ol>'
						 +'<li>Sarakkeiden (A, B, C ym) otsikot on kirjoitettu riville 1.</li>'
						 +'	<li>Graafeissa käytettävät numerosarakkeet sisältävät vain numeerista dataa. Numeerisen datan yksikkötiedot ovat kuvattu sarakkeen otsikossa (esim. km2 tai as/km2)</li>'
						 +'	<li>Tyhjät solut eivät sisällä sarakkeesta poikkeavaa dataa.</li>'
						 +'</ol>'
						 +'<p>CSV-taulukko on tekstimuotoinen tiedosto, jossa sisältö on eroteltu sarakkeisiin pilkuilla [,] ja solujen tekstit ovat ympäröity lainausmerkeillä ["]. Microsoft Excel ja OpenOffice tallentavat oletuksena CSV-tiedostot kyseisillä asetuksilla. CSV-tiedostoja muilla muodoilla ei ole tuettu.</p>'
						 +'<p>Esimerkki toimivan CSV-tiedoston sisällöstä:</p>'
						 +'<p><img src="/img/csv-esimerkki.png" style="width:600px;" alt="" /></p>'
						 +'<p>Lisäksi tilastodatan osalta on hyvä huomioida:</p>'
						 +'<ul>'
						 +'	<li>Mikäli taulukko sisältää tyhjiä rivejä, tyhjiä rivejä ei tallenneta palveluun.</li>'
						 +'	<li><a href="http://office.microsoft.com/fi-fi/excel-help/tekstitiedostojen-tuominen-tai-vieminen-HP010099725.aspx" target="_blank">'
						 +'	Ohje Office Excel-tiedoston tallennus CSV-tiedostoksi</a></li>'
						 +'</ul>'
						 +'<h1>Aloita tilastodatan siirto</h1>'
				},
				{
					xtype: 'filefield',
					name: 'datafile',
					fieldLabel: 'Datatiedosto',
					emptyText: 'taulukkotiedosto.csv',
					width: 400,
					allowBlank: false,
					buttonText: 'Valitse tiedosto'
				},
				{
					xtype: 'displayfield',
					value: 'Valitse koneeltasi ladattava CSV-tiedosto ja klikkaa "Seuraava &raquo;".'	
				}
			],
			fbar: [
				{
					text: 'Seuraava &raquo;',
					scope: this,
				    handler: function(button) {
				    
				    	var form = button.up('form').getForm();
				    	this.newChartWin.setLoading(true);
				    	form.submit({
							scope: this,
							success: function(form, action) {
								var res = Ext.JSON.decode(action.response.responseText);
								if ( res.success ) {
									this.createNewDataInfo( res.chart );
								} else {
									this.newChartWin.setLoading(false);
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
    	return this.addNewDataPanel;

    },
    
   
    
    getChartFieldsFieldset: function( fieldsArray ) {
    
    	var fieldTypesStoreData = [];
    	for(var typekey in this.fieldTypes) {
	    	var drow = { datatype: typekey, title: this.fieldTypes[typekey] }
	    	fieldTypesStoreData.push( drow );
    	}
		var fieldTypesStore = Ext.create('Ext.data.Store', {
			fields: ['datatype', 'title'],
			data: fieldTypesStoreData
		});
		
		var fieldEditors = [{
			layout: 'hbox',
			border: 0,
			items: [
				{
					xtype: 'displayfield',
					value: 'Sarake',
					width: 50,
					fieldStyle: 'color:#666;'
				},
				{
					xtype: 'displayfield',
					value: 'Otsikko',
					width: 250,
					margin: '0 0 0 10'
				},
				{
					xtype: 'displayfield',
					value: 'Tietotyyppi',
					margin: '0 0 0 20'
				}
			]
		}];
		Ext.each(fieldsArray, function( field, idx ){
			var row = {
				layout: 'hbox',
				border: 0,
				items: [
					{
						xtype: 'displayfield',
						value: 'Kenttä '+(idx+1),
						width: 50,
						fieldStyle: 'color:#666;'
					},
					{
						xtype: 'textfield',
						name: 'fields['+idx+'][name]',
						value: field.name,
						width: 250,
						margin: '0 0 0 10'
					},
					{
						xtype: 'combo',
						margin: '0 0 0 20',
						name: 'fields['+idx+'][type]',
						store: fieldTypesStore,
						queryMode: 'local',
						displayField: 'title',
						valueField: 'datatype',
						value: field.type
					},
					{
						xtype: 'hidden',
						name: 'fields['+idx+'][dataindex]',
						value: field.dataindex
					}
				]
			}
			fieldEditors.push(row);
		}, this);
		var info = {
			xtype: 'fieldcontainer',
			margin: '10 0 10 70',
			defaults: {
				labelWidth: 100	
			},
			items: [
				{
					xtype: 'displayfield',
					fieldLabel: '<b>Tietotyypit</b>'
				},
				{
					xtype: 'displayfield',
					fieldLabel: 'Teksti',
					value: 'Vapaa teksti. Tekstin pituutta ei ole rajoitettu.'
				},
				{
					xtype: 'displayfield',
					fieldLabel: 'Kokonaisluku',
					value: 'Numero ilman desimaaleja. Esim. 1, 2, 3'
				},
				{
					xtype: 'displayfield',
					fieldLabel: 'Desimaaliluku',
					value: 'Numero sisältäen desimaaleja. Esim. 1.0, 2.4, 3.6'
				},
				{
					xtype: 'displayfield',
					fieldLabel: 'Totuusarvo',
					value: 'Totuusarvo kyllä/ei'
				},
				{
					xtype: 'displayfield',
					fieldLabel: 'Päivämäärä',
					value: 'Päiväys, esim. "7.6.2012" tai "7.6.2012 15:30:00"'
				}
			]
		};
		fieldEditors.push(info);
		var fieldset = {
			xtype: 'fieldset',
			title: 'Taulukon sarakkeet',
			padding: '10 10 10 10',
			items: fieldEditors
		}
		
		return fieldset;
	    
    },
    
    createNewDataInfo: function( chartObj ) {
	    
	    this.newChartWin.removeAll();
	    var chartFieldsFieldset = this.getChartFieldsFieldset( chartObj.data.fields );
	    var newDataInfoForm = Ext.create('Ext.form.Panel', {
			id: 'newDataInfoForm',
			itemId: 'newDataInfoFormPanel',
    		url: '/run/lougis/charts/saveChartInfo/',
			bodyPadding: '10 10 10 10',
			border: 0,
			autoScroll: true,
			buttonAlign: 'left',
			frame: false,
			items: [
				{
					xtype: 'hiddenfield',
					name: 'chart[id]',
					value: chartObj.id	
				},
				{
					xtype: 'textfield',
					fieldLabel: 'Tilaston otsikko',
					name: 'chart[title]',
					width: 350,
					value: chartObj.title	
				},
				chartFieldsFieldset
			],
			fbar: [
				{
					text: '&laquo; Takaisin',
					scope: this,
					handler: function(button) {
				    	this.newChartWin.setLoading(true);
						this.addNewDataPanel = this.createNewDataPanel();
						this.newChartWin.removeAll(true);
						this.newChartWin.add(this.addNewDataPanel);
				    	this.newChartWin.setLoading(false);
					}
				}, 
				'->',
				{
					text: 'Seuraava &raquo;',
					scope: this,
				    handler: function(button) {
				    
				    	var form = button.up('form').getForm();
				    	this.newChartWin.setLoading(true);
				    	
				    	form.submit({
							scope: this,
							success: function(form, action) {
								var res = Ext.JSON.decode(action.response.responseText);
								this.reloadChartTreeStore();
								this.setChartToEditor( res.chart );
								//this.loadChartToEditor( res.chart.id );
								this.newChartWin.close();
								
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe!', action.result.msg);
								this.newChartWin.setLoading(false);
							}
						});
				    }
				}
			]
			
    	});
    	this.newChartWin.add( newDataInfoForm );
		this.newChartWin.setLoading(false);
	    
    }
    
            
});
