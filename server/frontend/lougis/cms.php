<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class CMS extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function getPageJson() {
		
		try {
		
			$PageId = $_REQUEST['page_id'];
			if ( empty($PageId) ) throw new \Exception("Page id required");
		
			$Pg = $this->_getPageInfo( $PageId );
			$Co = $this->_getPageContent($Pg->id, $Pg->lang_id);
			if ( empty($Pg->created_date) )  throw new \Exception("Page not found");
			$Pg->published = ( $Pg->published == 't' ) ? true : false;
			$Pg->visible = ( $Pg->visible == 't' ) ? true : false;
			$Pga = $Pg->toArray("cms_page[%s]");
			$Pga["cms_page[page_id]"] = $Pg->id;
			$Pga["page_id"] = $Pg->id;
			$res = array(
				"success" => true,
				"page" => $Pga,
				"content" => $Co->content,
				"content_column" => $Co->content_column
			);
			
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	private function _getPageInfo( $PageId ) {
		
		if ( empty($PageId) ) throw new Exception("Page id required");
		$Pg = new \Lougis_cms_page( $PageId );
		return $Pg;
		
	}
	
	private function _getPageContent( $PageId, $LangId ) {
	
		if ( empty($PageId) ) throw new Exception("Page id required");
		$Co = new \Lougis_cms_content();
		$Co->page_id = $PageId;
		$Co->lang_id = $LangId;
		$Co->orderBy('date_created DESC');
		$Co->limit(1);
		$Co->find();
		$Co->fetch();
		return $Co;
		
	}
	
	public function savePageContent() {
		
		try {
		
			$Pg = $this->_getPageInfo( $_REQUEST['page_id'] );
			$Co = $this->_getPageContent( $Pg->id, $Pg->lang_id );
			$Co->content = pg_escape_string(trim($_REQUEST['new_content']));
			if ( empty($Co->content) ) $Co->content = 'NULL';
			$Co->content_column = pg_escape_string(trim($_REQUEST['new_column']));
			if ( empty($Co->content_column) ) $Co->content_column = 'NULL';
			if ( empty($Co->date_created) ) {
				$Co->date_created = date(DATE_W3C);
				$Co->published = true;
				$Co->created_by = $_SESSION['user_id'];
			}
			if ( !$Co->save() ) throw new \Exception('Sivun sisällön tallentaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Sivun sisältö tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function savePageInfo() {
		
		try {
		
			//devlog($_REQUEST, 'pyry');
			
			$Pg = new \Lougis_cms_page($_REQUEST['cms_page']['page_id']);
			if ( empty($Pg->created_date) ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: Sivua ei voitu ladata!');
			if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: Virheellinen sivusto!');
			
			$Pg->setFrom($_REQUEST['cms_page']);
			if ( !isset($_REQUEST['cms_page']['parent_id']) || empty($_REQUEST['cms_page']['parent_id']) ) $Pg->parent_id = "NULL";
			if ( !isset($_REQUEST['cms_page']['published']) ) $Pg->published = false;
			if ( !isset($_REQUEST['cms_page']['visible']) ) $Pg->visible = false;
			//devlog($Pg, 'pyry');
			//$Pg->created_date = date(DATE_W3C);
			//$Pg->created_by = $_SESSION['user_id'];
			//$Pg->site_id = $_SESSION['site_id'];
			//$Pg->lang_id = 'fi';
			
			if ( !$Pg->save() ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Sivun tiedot tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			devlog($Pg, 'pyry');
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function createNewPage() {
		
		try {
		
			$Pg = new \Lougis_cms_page();
			$Pg->setFrom($_REQUEST['cms_page']);
			if ( !isset($_REQUEST['cms_page']['published']) ) $Pg->published = false;
			if ( !isset($_REQUEST['cms_page']['visible']) ) $Pg->visible = false;
			$Pg->created_date = date(DATE_W3C);
			$Pg->created_by = $_SESSION['user_id'];
			$Pg->site_id = $_SESSION['site_id'];
			$Pg->lang_id = 'fi';
			$Pg->setNextSeqNum();
			
			if ( !$Pg->save() ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
			
			$Text = "<h1>{$Pg->title}</h1>";
			$Co = $this->_getPageContent( $Pg->id, $Pg->lang_id );
			$Co->content = pg_escape_string($Text);
			if ( empty($Co->date_created) ) {
				$Co->date_created = date(DATE_W3C);
				$Co->published = true;
				$Co->created_by = $_SESSION['user_id'];
			}
			if ( !$Co->save() ) throw new \Exception('Sivun oletussisällön tallentaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Uusi sivu luotu",
				"page_id" => $Pg->id
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	
	}
	
	
	
	public function deletePage() {
		
		try {
		
			//devlog($_REQUEST, 'pyry');
			
			$Pg = new \Lougis_cms_page($_REQUEST['page_id']);
			if ( empty($Pg->created_date) ) throw new \Exception('Sivun poistaminen epäonnistui: Sivua ei voitu ladata!');
			if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun poistaminen epäonnistui: Virheellinen sivusto!');
			
			
			//devlog($Pg, 'pyry');
			
			if ( !$Pg->delete() ) throw new \Exception('Sivun poistaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Sivu poistettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			devlog($Pg, 'pyry');
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	private function _navTreeData() {
	
		$Pages = array();
		$Pg = new \Lougis_cms_page();
		$Pg->site_id = $_SESSION['site_id'];
		$Pg->orderBy('seqnum ASC');
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
		return $this->_recurseNavTreeData( $Pages );
		
	}
	
	private function _recurseNavTreeData( $Pgs, $Parent = null ) {
		
		$Branch = array();
		foreach( $Pgs as $Pg ) {
			if ( empty($Parent) && empty($Pg->parent_id) ) {
				unset($Pgs[$Pg->id]);
				$Kids = $this->_recurseNavTreeData( $Pgs, $Pg->id );
				if ( $Kids ) $Pg->children = $Kids;
				array_push($Branch, $Pg);
			} elseif ( $Parent == $Pg->parent_id ) {
				unset($Pgs[$Pg->id]);
				$Kids = $this->_recurseNavTreeData( $Pgs, $Pg->id );
				if ( $Kids ) $Pg->children = $Kids;
				array_push($Branch, $Pg);
			} 
		}
		if ( count($Branch) < 1 ) return false;
		return $Branch;
		
	}
	
	public function parentComboData( ) {
		
		global $comboResult;
		
		$comboResult = array();
		$row = array(
			"page_id" => null,
			"title" => "&nbsp;",
			"level" => 0
		);
		array_push($comboResult, $row);
		
		$tree = $this->_navTreeData();
		$comboData = $this->_recurseParentCombo( $tree );
		
		$this->jsonOut( $comboData );
	
	}
	
	private function _recurseParentCombo( $tree, $level = 0 ) {
		
		global $comboResult;
		
		foreach($tree as $Pg) {
			$pgTitle = "";
			for($i=0; $i < $level;$i++) {
				$pgTitle .= " &nbsp; &nbsp; ";
			}
			$pgTitle .= ( $level > 0 ) ? "\ _ _" : ""; 
			$pgTitle .= $Pg->nav_name;
			$row = array(
				"page_id" => $Pg->id,
				"title" => $pgTitle,
				"level" => $level
			);
			array_push($comboResult, $row);
			if ( isset($Pg->children) ) $this->_recurseParentCombo($Pg->children, ($level+1));
		}
		
		return $comboResult;
		
	}
	
	public function checkPagesJson() {
		
		$Site = new \Lougis_site( $_SESSION['site_id'] );
		
		$tree = $this->_navTreeData();
		$visible = $this->_recurseExtPageTree( $tree, true );
		$root = array(
			"text" => $Site->title,
			"expanded" => true,
			"children" => $visible
		);
		
		$this->jsonOut( $visible );
		
	}
	
	public function navTreeJson() {
		
		$Site = new \Lougis_site( $_SESSION['site_id'] );
		
		$tree = $this->_navTreeData();
		$visible = $this->_recurseExtPageTree( $tree, false, 0 );
		$root = array(
			"text" => $Site->title,
			"expanded" => true,
			"children" => $visible
		);
		
		$this->jsonOut( $visible );
		
	}
	
	private function _recurseExtPageTree( $PageTree, $Checkbox = false, $level = 0 ) {
		
		$Branch = array();
		foreach($PageTree as $Pg) {
			$Leaf = array(
				"text" => $Pg->nav_name,
				"page_id" => (int) $Pg->id
			);
			if ( isset($Pg->children) ) {
				$Leaf["expanded"] = ( $level < 1 ) ? true : false;
				$Leaf["children"] = $this->_recurseExtPageTree( $Pg->children, $Checkbox, $level+1 );
			} else {
				$Leaf["leaf"] = true;
			}
			if ( $Checkbox ) $Leaf["checked"] = false;
			array_push($Branch, $Leaf);
		}
		return $Branch;
	
	}
	
	
	public function saveTreeSort() {
		
		try {
			
			$treeData = json_decode($_REQUEST['tree_data']);
			
			$this->_recurseSaveTreeSort( $treeData );
			
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
	
	private function _recurseSaveTreeSort( $branch, $ParentId = "NULL" ) {
		
		global $Idx;
		
		if ( $Idx === null ) $Idx = 10;
		
		foreach($branch as $leaf) {
			$Pg = new \Lougis_cms_page($leaf->page_id);
			$Pg->seqnum = $Idx;
			$Pg->parent_id = $ParentId;
			$Idx++;
			if ( !$Pg->save() ) {
				throw new \Exception('Puun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
			}
			if ( count($leaf->children) > 0 ) $this->_recurseSaveTreeSort( $leaf->children, $Pg->id );
		}
	
	}
	
}
?>