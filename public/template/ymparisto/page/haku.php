<?php

global $Site, $Page, $LayoutConf;


require_once(PATH_TEMPLATE.'ymparisto/include_header.php'); 
?>
<!--<script type="text/javascript" src="http://google.com/jsapi?key=000855416783566672576:b3jhy9nir_w"></script>
<script type="text/javascript">google.load("jquery", "1");</script>
<script type="text/javascript">google.load("search", "1");</script>
<script type="text/javascript">google.load({"language" : "fi"});</script>
<script type="text/javascript" src="/js/jqueryPlugins/jquery.gSearch-1.0-min.js"></script>-->
<script>
  (function() {
    var cx = '000855416783566672576:b3jhy9nir_w';
    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  })();
</script>
<div id="content">
<!--    <div id="searchcontrol">
        <input id="s-box" type="text" size="100"><button id="hae">Hae</button>
    </div>
   
    <div id="search-results">-->
    <gcse:search></gcse:search>    
    </div>
      
</div>
<script type="text/javascript">
//var query = '';
//$(function() {
//    
//        $("#hae").click(function() {
//            query = $('#s-box').val();
//                $("#search-results").gSearch({
//                search_text : query,
//                count : 4,
//                pagination : false	
//            });
//        });
//    
//});
//</script>

<? require_once(PATH_TEMPLATE.'ymparisto/include_footer.php'); ?>