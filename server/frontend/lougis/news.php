<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class News extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function getNewsArray() {
	
		global $Site;
		
		try {
			
			$res = array();
			
			$News = new \Lougis_news();
			$News->site_id = $Site->id;
			$News->orderBy('created_date');
			$News->find();
			while ( $News->fetch() ) {
				$Data = $News->toArray();
				$Data["news_id"] = intval($Data["id"]);
				$Data["seqnum"] = intval($Data["seqnum"]);
				$Data["created_by"] = intval($Data["created_by"]);
				unset($Data["id"]);
				//$Data["pages"] = $News->getPagesDataArray();
				$res[] = $Data;
			}
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	
	}
	
	
	
	public function newsTreeJson() {
		
		global $Site;
		
		try {
			
			$res = array();
			
			$News = new \Lougis_news();
			$News->site_id = $Site->id;
			$News->orderBy('created_date DESC');
			$News->find();
			while ( $News->fetch() ) {
				$Data = array();
				$Data["text"] = $News->title.' ('.date("d.m.Y", strtotime($News->created_date)).')';
				$Data["leaf"] = true;
				$Data["news_id"] = (int) $News->id;
				$Data["seqnum"] = (int) $News->seqnum;
				$res[] = $Data;
			}
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function getNewsJson() {
		
		global $Site;
		
		try {
			
			if ( !isset($_REQUEST['news_id']) || empty($_REQUEST['news_id']) ) throw new \Exception("Virhe tiedotteen latauksessa");
			$News = new \Lougis_news($_REQUEST['news_id']);
			if ( empty($News->title) || $News->site_id != $Site->id ) throw new \Exception("Tiedotetta ei voitu ladata");
			
			$data = $News->toArray("news[%s]");
			unset($data['news[created_date]']);
			unset($data['news[content]']);
			unset($data['news[id]']);
			
			$data['news[created_date]'] = date("d.m.Y", strtotime($News->created_date));
			$data['news[published]'] = ( $News->published == 't' ) ? true : false ;
			$data['content'] = $News->content;
			$data['news_id'] = $News->id;
			
			$data["pages"] = $News->getPagesIdArray();
			
			$res = array(
				"success" => true,
				"data" => $data
			);

			
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function saveNews() {
		
		global $Site, $User;
		
		try {
			devlog($_REQUEST, 'pyry');
			if ( isset($_REQUEST['news_id']) && !empty($_REQUEST['news_id']) ) {
				$News = new \Lougis_news($_REQUEST['news_id']);
				if ( empty($News->title) ) throw new \Exception("Tiedotetta ei voitu ladata!");
				$msg = "Tiedote tallennettu";
			} else {
				$News = new \Lougis_news();
				$msg = "Uusi tiedote tallennettu";
				$News->site_id = $Site->id;
				$News->lang_id = 'fi';
				$News->created_date = date(DATE_W3C);
				$News->created_by = $User->id;
				$News->seqnum = 0;
				\Lougis_news::growSeqnums();
			}
			$News->setFrom($_REQUEST['news']);
			if ( empty($News->created_date) ) $News->created_date = date(DATE_W3C);
			$News->content = $_REQUEST['news_content'];
			if ( !isset($_REQUEST['news']['published']) && $News->N == 1 ) $News->published = false;
			
			if ( strlen($News->title) < 5 || strlen($News->title) > 250 ) throw new \Exception("Tiedotteen otsikko tulee olla vähintään 5 merkkiä ja maksimissaan 250.");
			if ( !$News->save() ) throw new \Exception('Tiedotteen tallentaminen epäonnistui: '.$News->_lastError->userinfo);
			if ( !$News->setPages( json_decode($_REQUEST['news_pages']) ) ) throw new \Exception('Tiedotteen linkkien tallennus epäonnistui: '.$Pg->_lastError);
			$res = array(
				"success" => true,
				"msg" => $msg,
				"news_id" => $News->id
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function deleteNews() {
		
		global $Site;
		
		try {
		
			$News = new \Lougis_news($_REQUEST['news_id']);
			if ( empty($News->title) ) throw new \Exception('Tiedotteen poistaminen epäonnistui: Tiedotetta ei voitu ladata!');
			if ( $News->site_id != $Site->id ) throw new \Exception('Tiedotteen poistaminen epäonnistui: Virheellinen sivusto!');
			
			if ( !$News->delete() ) throw new \Exception('Tiedotteen poistaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Tiedote poistettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function saveTreeSort() {
		
		try {
			
			$treeData = json_decode($_REQUEST['tree_data']);
			
			foreach($treeData as $idx => $news_id) {
				$News = new \Lougis_news($news_id);
				$News->seqnum = ($idx+1);
				if ( !$News->save() ) throw new \Exception('Järjestyksen tallentaminen epäonnistui: '.$Pg->_lastError);
			}
			
			$res = array(
				"success" => true,
				"msg" => "Rakenne tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function getNewsHtml() {
		
		global $Site;
		
		$this->htmlHeader();
		
		try {
			
			if ( !isset($_REQUEST['nid']) || empty($_REQUEST['nid']) ) throw new \Exception("Virhe tiedotteen latauksessa");
			$News = new \Lougis_news($_REQUEST['nid']);
			if ( empty($News->title) || $News->site_id != $Site->id ) throw new \Exception("Tiedotetta ei voitu ladata");
			?>
			<span class="date"><?=date("d.m.Y", strtotime($News->created_date))?> - <?=$News->source?></span>
			<img class="close" src="/img/close.png" alt="" title="Sulje uutinen" data-nid="<?=$News->id?>" />
			<span class="clr"/>
			<?=$News->getContentHtml(); ?>
			<? if ( !empty($News->source_url) ) { ?>
			<span class="link"><a href="<?=$News->source_url?>" target="_blank">Lue alkuperäinen artikkeli</a></span>
			<?
			}
		
		} catch(\Exception $e) {
			
			?>
			<b><?=$e->getMessage()?></b>
			<?
			
		}
		
		
	}
	
}
?>