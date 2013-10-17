<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

/**
 * CmsYmparisto is used by public side scripts to output Ymparisto-site specific CMS content.
 */
class CmsYmparisto extends \Lougis\abstracts\Utility {

	public function processYmparistoTemplate( $Template ) {
	
		if ( strpos($Template, '[AUTOMAATTINEN_TOIMENPIDETAULUKKO]') ) $Template = $this->replaceToimenpidetaulukko($Template);
		
		if ( strpos($Template, '[AUTOMAATTINEN_TAVOITETAULUKKO]') ) $Template = $this->replaceOhjelmataulukko($Template);
	
		if ( strpos($Template, '[YLEMMAT_TAVOITTEET]') ) $Template = str_replace('[YLEMMAT_TAVOITTEET]', '', $Template);
		
		if ( strpos($Template, '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]') ) $Template = $this->replaceYlemmatTavoitteet($Template);
		
		$Template = $this->replaceExtrat($Template);
		
		return $Template;
	
	}
	
	
	public function getPage() {
		
		global $Page;
		return $Page;
		
	}
	
	public function replaceExtrat( $Template ) {
		
		$Template = str_replace('[mittari]', '', $Template);
		$Template = str_replace('[asiantuntijoiden arvioinnit]', '', $Template);
		$Template = str_replace('[kommentointiosio]', '', $Template);
		$Template = str_replace('[t&auml;h&auml;n <strong>Kommentoi </strong>-osio]', '', $Template);
		
		
		return $Template;
	
	}
	
	public function replaceMittari( $Template ) {
		
		return str_replace('[mittari]', '', $Template);
	
	}
	/*
	public function replaceYlemmatTavoitteet( $Template ) {
	
		global $Site, $Page;
		
		$Parents = $Page->getParentPages();
		
		$Tbl = '<div id="tavoitteet">';
		foreach($Parents as $Pp) {
			switch($Pp->page_type) {
				case 'ohjelma' :
					$Tbl .= '<h3>Tavoite 2013</h3>'.$Pp->extra1;
				break;
				case 'strategia' :
					$Tbl .= '<h3>Tavoite 2020</h3>'.$Pp->extra1;
				break;
			}
		}
		//$Tbl .= '</div><span class="clr"/>';
		$Tbl .= '</div>';
		
		return str_replace('[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]', $Tbl, $Template);
	
	}*/
        public function replaceYlemmatTavoitteet( $Template ) {
	
		global $Site, $Page;
	
		
		$Tbl = '<div id="tavoitteet">';
		//foreach($Parents as $Pp) {
		//	switch($Pp->page_type) {
               
				//$Tbl .= '<h3>Tavoite 2013</h3>'.$Page->extra1;
				
				
				$Tbl .= '<h3>Tavoite 2020</h3>'.$Page->extra1;			
		
		//$Tbl .= '</div><span class="clr"/>';
		$Tbl .= '</div>';
		
		return str_replace('[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]', $Tbl, $Template);
	
	}
	
	public function replaceOhjelmataulukko( $Template ) {
	
		global $Site, $Page;
		
		$Kids = $Page->getChildPagesByType( 'ohjelma' );
		
		$Tbl  = '<ul class="ylist ohjelmatavoitteet">';
		foreach($Kids as $Kid) {
			$Tbl .= '<li><a href="'.$Kid->getPageUrl().'">'.$Kid->title.'</a><br/>'.$Kid->description.'</li>';
		}
		$Tbl .= '</ul>';
		return str_replace('[AUTOMAATTINEN_TAVOITETAULUKKO]', $Tbl, $Template);
	
	}
	
	public function replaceToimenpidetaulukko( $Template ) {
	
		global $Site, $Page;
		
		$Kids = $Page->getChildPagesByType( 'toimenpide' );
		
		$Tbl  = '<ul class="ylist toimenpiteet">';
		foreach($Kids as $Kid) {
			$Tbl .= '<li><a href="'.$Kid->getPageUrl().'">'.$Kid->title.'</a><br/>'.$Kid->description.'</li>';
		}
		$Tbl .= '</ul>';
		return str_replace('[AUTOMAATTINEN_TOIMENPIDETAULUKKO]', $Tbl, $Template);
	
	}

}