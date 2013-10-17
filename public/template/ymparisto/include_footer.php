	</div>
</div>
<div id="footer">
	<ul id="partners">
		<li id="ely">
			<a href="http://www.ely-keskus.fi/varsinais-suomi" target="_blank" title="Varsinais-Suomen ELY-keskus">
			<img src="/img/spacer.png" alt="Varsinais-Suomen ELY-keskus" style="width: 50px;height: 50px;"></a>
		</li>
		<li id="vsl">
			<a href="http://www.varsinais-suomi.fi/" target="_blank" title="Varsinais-Suomen liitto">
			<img src="/img/spacer.png" alt="Varsinais-Suomen liitto" style="width: 36px;height: 53px;"></a>
		</li>
		<li id="skl">
			<a href="http://www.satakuntaliitto.fi/" target="_blank" title="Satakuntaliitto">
			<img src="/img/spacer.png" alt="Satakuntaliitto" style="width: 35px;height: 53px;"></a>
		</li>
		<li id="turku">
			<a href="http://www.turku.fi/" target="_blank" title="Turun kaupunki">
			<img src="/img/spacer.png" alt="Turun kaupunki" style="width: 149px;height: 43px;margin-top: 10px;"></a>
		</li>
		<li id="pori">
			<a href="http://www.pori.fi/" target="_blank" title="Porin kaupunki">
			<img src="/img/spacer.png" alt="Porin kaupunki" style="width: 81px;height: 43px;margin-top: 10px;"></a>
		</li>
	</ul>
</div>
<?
$Co = new \Lougis\utility\Compiler("ymparisto-ui-jquery", "js");
$Co->addJs("/js/ymparisto/ymparisto.ui.jquery.js");
if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
	$Co->outputFilesScriptTags();
} else {
	$Co->outputScriptHtml();
}
?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto({
            deeplinking:false,
			social_tools:false
		});
	   
	});
</script>
</body>
</html>