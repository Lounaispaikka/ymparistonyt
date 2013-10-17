function createArvioChart( chartVal, renderToTarget, cWidth, cHeight) {
	
	Ext.create('Ext.chart.Chart', {
		id: 'arvioChart',
        width: cWidth,
        height: cHeight,
        style: 'background:#fff',
        animate: true,
        renderTo: renderToTarget,
	    store: Ext.create("Ext.data.Store", {
			fields: [
				{ name: "arvo", type: "float" }
			],
			data: [ { arvo: 0 } ]
		}),
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
        }],
        listeners: {
	        afterrender: function(chart) {
		        chart.store.getAt(0).set('arvo', chartVal);
	        }
        }
    });
    

}

function showArviointi( arviointiId ) {
	
	var abtn = Ext.get('abtn'+arviointiId);
	var ashort = Ext.get('ashort'+arviointiId);
	var atext = Ext.get('atext'+arviointiId);
	ashort.animate({
		to: {
			opacity: 0	
		},
		listeners: {
			beforeanimate: function() {
				abtn.setStyle({ display: 'block' }).animate({
					to: {
						opacity: 1.0
					}	
				})	
			},
			afteranimate: function(anim) {
				anim.target.target.setStyle({
					display: 'none'	
				});
				atext.animate({
					to: {
						opacity: 1.0
					},
					listeners: {
						beforeanimate: function(anim) {
							anim.target.target.setStyle({
								display: 'block'	
							});
						}	
					}
				});
			}	
		}
	});
	return false;
	
}

function hideArviointi( arviointiId ) {
	
	var abtn = Ext.get('abtn'+arviointiId);
	var ashort = Ext.get('ashort'+arviointiId);
	var atext = Ext.get('atext'+arviointiId);
	atext.animate({
		to: {
			opacity: 0	
		},
		listeners: {
			beforeanimate: function() {
				abtn.animate({
					to: {
						opacity: 0.0
					},
					listeners: {
						afteranimate: function(banim){
							abtn.setStyle({ display: 'none' })	
						}	
					}
				});
			},
			afteranimate: function(anim) {
				anim.target.target.setStyle({
					display: 'none'	
				});
				ashort.animate({
					to: {
						opacity: 1	
					},
					listeners: {
						beforeanimate: function(anim) {
							anim.target.target.setStyle({
								display: 'block'	
							});
						}	
					}
				});
			}	
		}
	});
	return false;
	
}