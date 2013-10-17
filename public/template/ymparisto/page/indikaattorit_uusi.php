<?php

require_once(PATH_SERVER.'/config.php');

require_once(PATH_TEMPLATE.'ymparisto/include_header.php'); 
global $Site, $Cms;
$ifr = $Site->getChartIframe();
if(isset($_GET['id'])) {
        if (!preg_match("/^\d+$/", $_GET['id'])) die("Tilastoa ei löytynyt");
        $Chart = new \Lougis_chart($_GET['id']);
        $Caa = $Chart->toChartArray();
        if ( empty($Chart->created_date) ) {
                echo "Tilastoa ei löytynyt";die;
        }
}



?>

<div id="breadcrumb"><? $Cms->outputBreadcrumb(); ?><a href="/fi/sanasto/" style="position:absolute;right:106px; font-size: 12px;">Sanasto</a></div>
<div id="leftCol" class="col2">	
<? $Cms->outputChartNavigation(); ?>
</div>
        
<div id="content" class="col2">
        <div id="cms_data" style="display: block;">
<?
$Con = $Page->getContentHtml();
if ( !empty($Con) && !(isset($_GET['id'])) ) print $Con; 
?>
                
        </div>
		<? if(isset($_GET['id'])) { ?>
        <div id="chartDiv">
                <script type="text/javascript" src="/js/ymparisto/legend_overrider.tilasto.extjs.js"></script>         
                
				<script type="text/javascript">
                
				Ext.onReady(function () {
                        var chartObj = <?=json_encode($Caa)?>;
                        console.log(chartObj);
                        var colors = ["#94ae0a", "#115fa6","#a61120", /*"#ff8809"*/"#595959", "#ffd13e", "#a61187", "#24ad9a", "#7c7474", "#a66111"];

                        Ext.define('Ext.chart.theme.Indit', {
                            extend: 'Ext.chart.theme.Base',
                            id: 'Indit',
                            constructor: function(config) {
                                this.callParent([Ext.apply({
                                    axisTitleLeft: {
                                            font: 'bold 12px Verdana'
                                    },
                                    axisTitleBottom: {
                                            font: 'bold 12px Verdana'
                                    },
                                    colors: colors
                                }, config)]);
                            }
                        });
                        console.log('1');
                        var storeFields = [];
						//ongelma data on nolla tai ei ole objekti IE8
                                Ext.each(chartObj.data.fields, function(field, idx) {
                                        var storeField = {
                                                name: field.dataindex,
                                                type: field.type
                                        };
                                        storeFields.push(storeField);
                                }, this);

                            var chartStore = Ext.create('Ext.data.ArrayStore', {
                                autoDestroy: true,
                                fields: storeFields,
                                data: chartObj.data.data
                            });
                      

                        chartObj.config.store = chartStore;
                        chartObj.config.width = 600;
                        chartObj.config.height = 400;
                        chartObj.config.theme = 'Indit';
                       
                        function rend(storeItem, item) {
                                var title = item.value[1];
                                this.setTitle(title);
                        }
                      
                        $.each(chartObj.config.series, function(index) {
                                chartObj.config.series[index].tips = new Array();
                                chartObj.config.series[index].tips.trackMouse = true;
                                chartObj.config.series[index].tips.width = 75;
                                chartObj.config.series[index].tips.height = 28;
                                chartObj.config.series[index].tips.renderer = rend;
                           
                        });
                      
						//päivämäärä formaattiin pp.kk.vvvv
						var upd_date = chartObj.updated_date.substr(8,2) + "." + chartObj.updated_date.substr(5,2) + "." + chartObj.updated_date.substr(0,4);
						chartObj.updated_date = upd_date;
                        Ext.define('TilastoTiedot', {
                                extend: 'Ext.data.Model',
                                fields: ['created_by', 'description', 'id', 'original_filename', 'published', 'short_description', 'title', 'updated_date'] 
                        });
                        
                        var store = Ext.create('Ext.data.Store', {
                                model: 'TilastoTiedot',
                                data : chartObj
                                
                        });
						
						var axes = chartObj.config.axes;
						var carr = chartObj.data.data;
        
						 // Lisää minimum-arvon 0, jos axes type on numeric (== kuvaaja pakotetaan alkaa nollasta)
						 var neg = false;
						 $.each(carr, function(index) {
							
								 $.each(carr[index], function(i) {
							   
										if(carr[index][i] < 0) {
												neg = true;
										}
										
								});
						 });
						 if(neg === false) {
								$.each(axes, function(index) {
										if(axes[index].type === 'Numeric') { 
												axes[index].minimum = 0;
										} 
								});
						 }
                       var tpl_h = new Ext.XTemplate(
                                '<tpl for=".">',
                                        '<div style="margin-bottom: 10px;" class="chartinfo">',
                                          '<h1>{title}</h1>',
                                          '<p class="pvm">Tilasto päivitetty: {updated_date}</p>',
                                        '</div>',
                                '</tpl>'
                        );
                      var infoHeading = Ext.create('Ext.view.View', {
                                store: store,
                                itemSelector: 'div.chartinfo',
                                tpl: tpl_h,
                                id: 'chartInfoHeading',
                                width: 680
                        });
                       var tpl_d = new Ext.XTemplate(
                                '<tpl for=".">',
                                        '<div style="margin-bottom: 10px;" class="chartinfo">',
                                        '<p class="short_desc">{short_description}</p>',
                                        '<p>{description}</p>',
                                       
                                        '</div>',
                                '</tpl>'
                        );

                        var infoDescription = Ext.create('Ext.view.View', {
                                store: store,
                                itemSelector: 'div.chartinfo',
                                tpl: tpl_d,
                                id: 'chartInfoDescription',
                                width: 600
                        });
                        
                   
                  
                       var panel = Ext.create('Ext.panel.Panel', {
                                frame: false,
                                border: 0,
                                renderTo: 'chartDiv',
                                width: 600
                              
                        });
                        console.log("panel", panel);
                        
                       
                        panel.add(infoHeading);
                        panel.add(chartObj.config);
                        panel.add(infoDescription);

                        panel.doLayout();
                  
                });
               
                </script>
                 
        </div>
        <?

        ?>
        
        <div id="extraDetails">
                <p>Halutessasi voit vied&auml; kuvaajan organisaatiosi verkkosivulle kopioimalla <span href="#" id="toggle_upotus">upotuskoodin</span></p><textarea readonly="readonly" id="upotus" ><?=$ifr;?></textarea>
                <p>Kuvaajan tiedot <a href="../../ymparisto/dlcsv.php?id=<?=$_GET['id']?>">taulukkona</a> (CSV)</p>
				<script type="text/javascript">$("#toggle_upotus").click(function(){$("#upotus").toggle();});</script>
        </div>
        <? } ?>
        <div id="social">

            <div id="email">
            <a href="mailto:?Subject=<?=$Site->title?> - <?=$Pg->title?>&Body=<?=$Pg->getPageFullUrl()?>">Sähköposti</a>
            </div>

            <div id="fb">
            <iframe src="//www.facebook.com/plugins/like.php?href=<?=urlencode($Pg->getPageFullUrl())?>&amp;locale=fi_FI&amp;layout=button_count&amp;show_faces=false&amp;width=125&amp;action=recommend&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:125px; height:21px;" allowTransparency="true"></iframe>
            </div>

            <div id="twitter">
            <a href="https://twitter.com/share" class="twitter-share-button" data-lang="fi" data-hashtags="ymparistonyt">Twiittaa</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script> 
            </div>
			
    </div>
	

    <? require_once(PATH_PUBLIC.'ymparisto/kommentointi.php'); ?>
</div>
<? require_once(PATH_TEMPLATE.'ymparisto/include_footer.php'); ?>
