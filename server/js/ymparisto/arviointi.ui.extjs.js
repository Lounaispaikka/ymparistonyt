Ext.onReady(function () {
	
	var arvioStore = Ext.create("Ext.data.Store", {
		storeId: "arvioStore",
		fields: [
			{ name: "arvo", type: "integer" }
		],
		data: [ { arvo: 25 } ]
	});
	arvioStore.getAt(0).set('arvo', arvioData.arvio_arvo );
	
	var arvioChart = Ext.create('Ext.panel.Panel', {
        width: 400,
        height: 100,
        border: 0,
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        margin: '0 0 0 80',
        items: [{
            xtype: 'chart',
            style: 'background:#fff',
            animate: true,
            store: arvioStore,
            insetPadding: 5,
            flex: 1,
            axes: [{
                type: 'gauge',
                position: 'gauge',
                minimum: 0,
                maximum: 50,
                steps: 0,
                margin: 0
            }],
            series: [{
                type: 'gauge',
                field: 'arvo',
                donut: 40,
                colorSet: ['#82B525', '#ddd'],
                renderer: function(sprite, storeItem, attr, i, store) {
                	if ( attr.fill != '#ddd' ) {
                    	var h = storeItem.get('arvo')*2.5;
                    	attr.fill = Ext.draw.Color.fromHSL(h, 0.75, 0.5).toString();
                	}
                    return attr;
                }
            }]
        }]
    });
	
	var arvioForm = Ext.create('Ext.form.Panel', {
		width: 600,
	    border: 0,
	    bodyPadding: 10,
	    renderTo: 'vastausDiv',
	    url: '/run/ymparisto/toimenpide/saveArvio/',
	    buttonAlign: 'center',
	    frame: false,
	    border: false,
	    items: [
	    {
	    	xtype: 'hidden',
	    	name: 'vastaus_id',
	    	value: arvioData.vastaus_id
	    },
	    {
	    	xtype: 'hidden',
	    	name: 'user_id',
	    	value: arvioData.user_id
	    },
	    {
	    	xtype: 'displayfield',
	    	fieldLabel: 'Arvioija',
	    	labelStyle: 'font-weight: bold;',
	    	value: arvioData.user_name
	    },
	    {
	    	xtype: 'displayfield',
	    	fieldStyle: 'font-weight: bold;',
	    	value: kyselyTitle
	    },
	    {
	    	xtype: 'displayfield',
	    	value: 'Toimenpide tulee toteutumaan:'
	    },
	    arvioChart,
	    {
	    	xtype: 'sliderfield',
	        name: 'arvio_arvo',
	        value: arvioData.arvio_arvo,
	        useTips: false,
	        flext: 1,
	        increment: 1,
	        minValue: 0,
	        maxValue: 50,
	        anchor: '94%',
	        padding: '15px 0 0 0',
	        listeners: {
	        	change: function( slider, newValue ) {
	        		arvioStore.getAt(0).set('arvo', newValue);
	        	}
	        }
	    },
	    {
	    	xtype: 'fieldcontainer',
	    	padding: '0 0 15px 0',
	    	layout: {
	    		type: 'hbox'
	    	},
	    	defaults: {
	    		hideLabel: true,
	    		xtype: 'displayfield',
	    		fieldStyle: 'font-weight: bold;',
	    		width: 75,
	    		style: {
	    			textAlign: 'center'
	    		}
	    	},
	    	
	    	items: [
	    		{ 
	    			value: 'ei toteudu',
	        		style: {
	        			margin: '0 0 0 0',
	        			textAlign: 'left'
	        		}
	    		},
	    		{ 
	    			value: 'heikosti',
	        		style: {
	        			margin: '0 0 0 45px',
	        			textAlign: 'center'
	        		}
	    		},
	    		{ 
	    			value: 'kohtalaisesti',
	        		style: {
	        			margin: '0 0 0 45px',
	        			textAlign: 'center'
	        		}
	    		},
	    		{ 
	    			value: 'hyvin',
	        		style: {
	        			margin: '0 0 0 75px',
	        			textAlign: 'center'
	        		}
	    		},
	    		{ 
	    			value: 'toteutuu', 
	        		style: {
	        			margin: '0 0 0 20px',
	        			textAlign: 'right'
	        		}
	    		}
	    	]
	    },
	    {
	    	xtype: 'displayfield',
	    	fieldStyle: 'font-weight: bold;',
	    	value: 'Arvioinnin perustelut:'
	    },
	    {
	    	xtype: 'textarea',
	    	width: 600,
	    	height: 510,
	    	enableColors: false,
	    	enableFont: false,
	    	id: 'arvioPerustelu',
	    	inputId: 'arvioPerusteluField',
	    	name: 'arvio_perustelu',
	    	value: arvioData.arvio_perustelu,
	    	listeners: {
	    		afterrender: function( container, fn ) {
	    		
					CKEDITOR.replace( 'arvioPerusteluField', {
						toolbar: 'YmparistoArviointi',
						language: 'fi',
						width: 560,
						height: 400,
						filebrowserBrowseUrl: '',
						filebrowserImageBrowseUrl: ''
					});
	    		}
	    	}
	    }
	    ],
	    buttons: [
		   	{
	            text: 'Tallenna arviointi',
	            scale: 'large',
	            cls: 'lgBigButton',
	            margin: '20 10 0 10',
	            handler: function(btn, ev){
	            
	            	form = this.up('form').getForm();
	            	form.findField('arvioPerustelu').setValue( CKEDITOR.instances.arvioPerusteluField.getData() );
	            	
	            	form.submit({
	                	success: function(form, action) {
	                		
	                		Ext.Msg.alert({ 
		                    	title: 'Arviointi tallennettu!', 
		                    	msg: action.result.msg, 
		                    	icon: Ext.Msg.INFO, 
		                    	buttons: Ext.Msg.OK,
	                    		fn: function() {
		                    		window.location.reload()
	                    		}
		                    });
	                	},
	                	failure: function(form, action) {
		                    Ext.Msg.alert({ 
		                    	title: 'Virhe!', 
		                    	msg: action.result.msg, 
		                    	icon: Ext.Msg.ERROR, 
		                    	buttons: Ext.Msg.OK 
		                    });
	                	}
	                }); 
	            }
	        }
	    ]
	});
	
	if ( arvioData.arvio_date !== null ) {
		
	    var field = {
	    	xtype: 'displayfield',
	    	fieldLabel: 'Tallennettu',
	    	labelStyle: 'font-weight: bold;',
	    	value: arvioData.arvio_date
	    }
		arvioForm.insert(3, field);
		
	}

});