<?php

global $Site, $Page, $LayoutConf;

//$AllNews = $Site->getNews();
$Tapahtumat = $Site->getNewsTapahtumat();
$Uutiset = $Site->getNewsUutiset();

$ShowNewsId = ( isset($_REQUEST['nid']) ) ? $_REQUEST['nid'] : $AllNews[0]->id;

require_once(PATH_TEMPLATE.'ymparisto/include_header.php'); 
?>
<div id="breadcrumb"><? $Cms->outputBreadcrumb(); ?><a href="/fi/sanasto/" style="position:absolute;right:106px; font-size: 12px;">Sanasto</a></div>
<div id="content">
<?
$Con = $Page->getContentHtml();
if ( !empty($Con) ) echo $Con;
?> <? /*
    <div id="news-5">
        <? foreach($TopNews as $News) { ?>
        <table>
            <tbody>
                <a href="<?=$Page->getPageUrl()?>?nid=<?=$News->id?>#n<?=$News->id?>">
                <tr>
                    <th><?=date("d.m.Y", strtotime($News->created_date))?></th>
                    <th><?=$News->title?></th>
                </tr>
                </a>
            </tbody>
            
        </table>
        <? } ?>
    </div> */ ?>
        <div id="news-archive">
                <div id="news-archive_left" style="display: block; float: left; width:562px;">
                    <h2>Uutiset</h2>
                    <ul class="content">               

                        <? 
					
						$uutisia_kpl = 0;
                        foreach($Uutiset as $News) { 
							$uutisia_kpl++;
							if($uutisia_kpl<11) {
						?>
                            <li id="lnid<?=$News->id?>" name="n<?=$News->id?>" class="pag">
                                <div id="nhead">
                                        <a name="n<?=$News->id?>"></a>
                                        <a href="<?=$Page->getPageUrl()?>?nid=<?=$News->id?>#n<?=$News->id?>" class="nid" data-nid="<?=$News->id?>">
                                        <h1><?=$News->title?></h1>
                                        <span class="newsInfo"><?=date("d.m.Y", strtotime($News->created_date))?></span>
                                        <p><?=$News->description?></p>
                                        </a>
                                </div>
                                <div id="ncont" style="display:none;"></div>
                            </li>
                        <?	}
							if($uutisia_kpl==10) { ?> 
								<p id="show_all_uutiset"><span>Lis&auml;&auml; uutisia...</span></p>
							<?}
							
							if($uutisia_kpl>10) {	?>
							 <li id="lnid<?=$News->id?>" name="n<?=$News->id?>" class="pag" style="display:none;">
                                <div id="nhead">
                                        <a name="n<?=$News->id?>"></a>
                                        <a href="<?=$Page->getPageUrl()?>?nid=<?=$News->id?>#n<?=$News->id?>" class="nid" data-nid="<?=$News->id?>">
                                        <h1><?=$News->title?></h1>
                                        <span class="newsInfo"><?=date("d.m.Y", strtotime($News->created_date))?></span>
                                        <p><?=$News->description?></p>
                                        </a>
                                </div>
                                <div id="ncont" style="display:none;"></div>
                            </li> 
							
							<? }
						} ?>
                    </ul>


                </div>
               <? if(count($Tapahtumat) != null) { ?>
                <div id="news_tapahtumat" style="width: 320px; display: block; float:right; border-width: 0px;">
                        <h2>Tapahtumat</h2>
                        <ul class="content" style="background: #e7e7e8;">                              
                        <?      
                        $tapahtumia_kpl = 0;
                        foreach($Tapahtumat as $Tapahtuma) { 
							$tapahtumia_kpl++;
							
							if($tapahtumia_kpl<11) {
						?>
                            <li id="lnid<?=$Tapahtuma->id?>" name="n<?=$Tapahtuma->id?>" class="pag_t">
                               <div id="nhead">
                                        <a name="n<?=$Tapahtuma->id?>"></a>
                                        <a href="<?=$Page->getPageUrl()?>?nid=<?=$Tapahtuma->id?>#n<?=$Tapahtuma->id?>" class="nid" data-nid="<?=$Tapahtuma->id?>">
                                        <h1><?=$Tapahtuma->title?></h1>
                                        <span class="newsInfo"><?=date("d.m.Y", strtotime($Tapahtuma->created_date))?></span>
                                        <p><?=$Tapahtuma->description?></p>
                                        </a>
                                </div>
                                <div id="ncont" style="display:none; background: #fff;"></div>
                            </li>
                        <?  } ?>
						<?	
							if($tapahtumia_kpl==10) { ?> 
								<p id="show_all_tapahtumat"><span>Lis&auml;&auml; uutisia...</span></p>
						<?}
							if($tapahtumia_kpl>10) {	?>
							<li id="lnid<?=$Tapahtuma->id?>" name="n<?=$Tapahtuma->id?>" class="pag_t" style="display: none;">
                               <div id="nhead">
                                        <a name="n<?=$Tapahtuma->id?>"></a>
                                        <a href="<?=$Page->getPageUrl()?>?nid=<?=$Tapahtuma->id?>#n<?=$Tapahtuma->id?>" class="nid" data-nid="<?=$Tapahtuma->id?>">
                                        <h1><?=$Tapahtuma->title?></h1>
                                        <span class="newsInfo"><?=date("d.m.Y", strtotime($Tapahtuma->created_date))?></span>
                                        <p><?=$Tapahtuma->description?></p>
                                        </a>
                                </div>
                                <div id="ncont" style="display:none; background: #fff;"></div>
                            </li>
							<? }
						} ?>
                    </ul>
                </div>
                <? } ?>
        </div>
</div>
<script type="text/javascript">
     
$(function(){
	var fadeTime = 500;
	
	$nids = $("#news-archive a.nid");
	
	$nids.each(function(index, ael){
		$(ael).click(newsItemClick);
	});
	
	function newsItemClick( ev, nid ) {
	
		if ( ev != null ) ev.preventDefault();
		var $ael = ( typeof nid != 'undefined' ) ? $('#lnid'+nid+' a.nid') : $(this);
		var $nhead = $ael.parent('div#nhead');
		var $ncont = $nhead.next();
		$nhead.fadeOut(fadeTime, function(){
			
			$ncont.load('/run/lougis/news/getNewsHtml/', { nid: $ael.attr('data-nid') }, function(){
				var $close = $('img.close', $ncont);
				$close.click(function(ev){
					$ncont.fadeOut(fadeTime, function(){ $nhead.fadeIn(fadeTime) });                                      
                                        var new_url = old_url.substring(0, old_url.indexOf('&'));                                      
                                        top.location.href = new_url;
                                        
				});
				showBigImages('#lnid'+$ael.attr('data-nid'));
			});
			$ncont.fadeIn(fadeTime);
			
		});
		
	}
	//N‰yt‰ kaikki uutiset
    $("#show_all_uutiset").click(function() { 
		$(".pag").show();
		$("#show_all_uutiset").hide();
	});
	    $("#show_all_tapahtumat").click(function() { 
		$(".pag_t").show();
		$("#show_all_tapahtumat").hide();
	});
	<? if ( isset($_REQUEST['nid']) ) { ?>
	$(".pag").show();$("#show_all_uutiset").hide();
	$(".pag_t").show();$("#show_all_tapahtumat").hide();	//n‰ytt‰‰ kaikki uutiset, jotta linkit ajankohtaisiin toimisivat
	newsItemClick( null, <?=$_REQUEST['nid']?> )
	<? } ?>
            /*
        //jPaginate
        $('#paging_container2').pajinate({
            items_per_page: 6,
            nav_label_first: '<<',
            nav_label_last: '>>',
            nav_label_next: '>',
            nav_label_prev: '<',
            show_first_last: true
        });
            
            */
       
	
});
</script>
<script type="text/javascript">
        // fix IE bug where hash anchors don't update scrolltop on some occasions
if (window.location.hash)
window.location = window.location.hash;
</script>
<? require_once(PATH_TEMPLATE.'ymparisto/include_footer.php'); ?>
