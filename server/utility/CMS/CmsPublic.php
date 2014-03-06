<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');
require_once(PATH_SERVER.'utility/CMS/CmsYmparisto.php');

/**
 * CmsPublic is used by public side scripts to output CMS content.
 */
class CmsPublic extends \Lougis\abstracts\Utility {

	private $Site;
	private $Lang;
	private $NavTree = null;

	public function __construct( $Site, $Lang ) {
		$this->Site = &$Site;
		$this->Lang = &$Lang;
	}
	
    public function showRequestPage() {
        
        global $Page;
        
        $Page = false;
        if ( isset($_REQUEST['p']) ) {
        	$Page = $this->Site->getPageByName($_REQUEST['p'], $this->Lang->id);
        } else {
        	$Page = $this->Site->getFirstPage($this->Lang->id);
        }
        
        switch(true) {
        	
        	case $Page !== false :
        		$this->outputPageHtml($Page);
        	break;
        	default:
        		require_once(PATH_404_FILE);
        	break;
        
        }
        
        
	}
	
	public function getPage() {
		
		global $Page;
		return $Page;
		
	}
	
	public function hasRightColumn() {
	
		global $Page;
		
		if ( $Page->hasNews() || $Page->hasColumnContent() ) return true;
		return false;
	
	}
	
	public function currentPageHasParent() {
	
		global $Page;
		
		if ( !empty($Page->parent_id) ) return $Page->parent_id;
		return false;
	
	}
	
	public function currentPageHasChildren( $Published = null ) {
	
		global $Page;
		
		$Search = new \Lougis_cms_page();
		$Search->parent_id = $Page->id;
    	if ( !empty($Publised) ) $Search->published = $Publised;
    	if ( $Search->count() > 0 ) return true;
    	return false;
    	
	
	}
	
	public function outputBreadcrumb() {
	
		global $Page;
		
		$ParentStack = $this->getCurrentPageParentStack();
		for($i=0; $i < count($ParentStack); $i++) {
			$Pg = $ParentStack[$i];
			//if(count($ParentStack) > 1) => näyttää polussa myös ensimmäisen sivun, kun on alasivulla. Jos vain ensimmäisellä sivulla, polkua ei näy
			if(count($ParentStack) > 1) echo '<a href="'.$Pg->getPageUrl().'">'.trim($Pg->title).'</a>';
			if ( $i < (count($ParentStack)-1) ) echo " | ";
		}
	
	}
	
    public function outputBreadcrumbChartTree() {
        global $Page;

        $Pg = new \Lougis_chart();
        $Pg->published = true;
        $Pg->id = $_GET['id'];
        $Pg->find();
        if( $Pg->fetch() ) {
            echo ' | <a href="'.$Pg->getPageUrl().'">'.trim($Pg->title).'</a>';
        }
    }

	public function getCurrentPageParentStack( ) {
	
		global $Page;
		
		$Parents = array();
		$Parent = clone($Page);
		$Parents[] = $Parent;
		
		while( isset($Parent->parent_id) && !empty($Parent->parent_id) ) {
			$Parent = new \Lougis_cms_page($Parent->parent_id);
			$Parents[] = clone($Parent);
		}
		
		$Parents = array_reverse($Parents);
		return $Parents;
	
	}
	
	public function findCurrentPageTopParent( ) {
	
		global $Page;
		
		$navBranch = null;
		$navTree = $this->_navTreeData();
		foreach($navTree as $topLeaf) {
			if ( isset($topLeaf->children) && is_array($topLeaf->children) 
					&& count($topLeaf->children) > 0  && $this->_pageInBranch($topLeaf->children) ) {
					//echo "Pg: {$Page->id} / Leaf: {$topLeaf->id}<br/>";
					return $topLeaf;
			}
		}
		return $Page;
	
	}
	
	private function _pageInBranch( $Branch ) {
	
		global $Page;
		
		foreach($Branch as $Leaf) {
			if ( $Leaf->id == $Page->id ) return true;
			if ( isset($Leaf->children) && is_array($Leaf->children) && count($Leaf->children) > 0 ) {
				$InSubBranch = $this->_pageInBranch( $Leaf->children );
				if ( $InSubBranch ) return true;
			}
		}
		return false;
		
	}
	
	public function outputPageHtml( $Page ) {
	
		global $Site, $Page;
		
		if ( !empty($Page->template) ) {
			$Template = file_get_contents(PATH_TEMPLATE.$this->Site->id.'/page/'.$Page->template);
			$Template = str_replace('{PAGE_CONTENT}', $Page->getContentHtml(), $Template);
		} else {
			$Template = file_get_contents(PATH_TEMPLATE.$this->Site->id.'/'.$this->Site->default_template);
			$Template = str_replace('{PAGE_CONTENT}', $Page->getContentHtml(), $Template);
		}
		
		if ( $Site->id == 'ymparisto' ) {
			$CmsY = new \Lougis\utility\CmsYmparisto();
			$Template = $CmsY->processYmparistoTemplate( $Template );
		}
		
		echo eval('?>'.$Template.'<?');
	}
	
	public function ouputTopNavigation( ) {
		
		global $Page;
		
		$navTree = $this->_navTreeData();
		?>
		<ul>
		<? foreach($navTree as $topLeaf) { 
			if ( $topLeaf->visible == 't' ) {
		?>
			<li><a href="<?=$topLeaf->getPageUrl()?>"<?=( ( $topLeaf->id == $Page->id || $Page->hasParentPage( $topLeaf->id )) ? ' class="active"' : '' )?>><?=$topLeaf->title?></a></li>
		<? 
			}
		} ?>
		</ul>
		<?
		
	}
	
	public function outputLeftNavigation($Parent) {
		
		global $Page;
		
		$navBranch = null;
		$navTree = $this->_navTreeData();
		foreach($navTree as $topLeaf) {
			if ( $topLeaf->id == $Parent->id ) $navBranch = $topLeaf;
		}
		if ( isset($navBranch->children) && is_array($navBranch->children) && count($navBranch->children) > 0 ) {
		?>
		<ul>
			<? $this->_recurseOutputSubNavTree($navBranch->children) ?>
		</ul>
		<?
		}
		
	}
        
        public function outputChartNavigation() {
		
		global $Page;
		
		$navTree = $this->_chartTreeData();
		?>
		<ul>
		<? foreach($navTree as $topLeaf) { 
			
		?>
			<li><a href="/fi/indikaattorit/?id=<?=$topLeaf->id?>" <?=( ( $topLeaf->id == $_GET['id'] ) ? ' class="active"' : '' )?>><?=$topLeaf->title?></a></li>
                       <? /* <li><a href="<?=$topLeaf->getPageUrl()?>"<?=( ( $topLeaf->id == $Page->id || $Page->hasParentPage( $topLeaf->id ) ) ? ' class="active"' : '' )?>><?=$topLeaf->title?></a></li> */?>
		<? 
			
		} ?>
		</ul>
	<?
	}
	
	private function _recurseOutputSubNavTree($navBranch) {
		
		global $Page;
		foreach($navBranch as $leaf) {
			if ( $leaf->visible == 't' ) {
			?>
			<li><a href="<?=$leaf->getPageUrl()?>" <?=( ( $leaf->id == $Page->id ) ? ' class="active"' : '' )?>><?=$leaf->title?></a>
			<?
                        //Ympäristöohjelman sivulla strategiset tavoitteet täytyy olla erillään muista
                        //if($leaf->title == 'Strategiset tavoitteet 2020') { echo '<li>&nbsp;</li>';} ?>
			<? 
			if ( isset($leaf->children) && is_array($leaf->children) && count($leaf->children) > 0 
					&& ( $Page->id == $leaf->id || $Page->hasParentPage($leaf->id) ) ) { 
			?>
			<ul><? $this->_recurseOutputSubNavTree($leaf->children) ?></ul>
			<? } ?>
			</li>
			<?
			}
		}
		
	}
	
	private function _recurseExtPageTree( $PageTree, $PageVisible = true ) {
		
		$Branch = array();
		foreach($PageTree as $Pg) {
			$Leaf = array(
				"text" => $Pg->nav_name,
				"page_id" => $Pg->id
			);
			if ( isset($Pg->children) ) {
				$Leaf["expanded"] = true;
				$Leaf["children"] = $this->_recurseExtPageTree( $Pg->children );
			} else {
				$Leaf["leaf"] = true;
			}
			array_push($Branch, $Leaf);
		}
		return $Branch;
	
	}
	
	private function _navTreeData() {
	
		if ( $this->navTree !== null ) return $this->navTree;
		
		$Pages = array();
		$Pg = new \Lougis_cms_page();
		$Pg->site_id = $this->Site->id;
		$Pg->orderBy('seqnum ASC');
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
		$this->navTree = $this->_recurseNavTreeData( $Pages );
		return $this->navTree;
		
	}
        
        private function _chartTreeData() {
	
		//if ( $this->navTree !== null ) return $this->navTree;
		
		$Pages = array();
		$Pg = new \Lougis_chart();
		$Pg->published = true;
        //$Pg->orderBy('parent ASC');
		$Pg->orderBy('title ASC');
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
                $this->navTree = $Pages;
		return $this->navTree;
		
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
	
}
?>