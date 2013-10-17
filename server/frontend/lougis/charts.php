<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class Charts extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function getChartsJson() {
	
		global $Site;
		
		try {
			
			
			$Charts = array();
			$Ch = new \Lougis_chart();
			$Ch->orderBy("title");
			$Ch->whereAdd("title IS NOT NULL");
			$Ch->find();
			while( $Ch->fetch() ) {
				$Charts[] = array(
					"text" => $Ch->title,
					"leaf" => true,
					"expanded" => false,
					"chart_id" => intval($Ch->id)
				);
			}
			$res = $Charts;
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	
	}
        //Indikaattorisivun käyttöön indikaattorina julkaistavat tilastot
        public function getPublishedChartsJson() {
	
		global $Site;
		
		try {
			
			
			$Charts = array();
			$Ch = new \Lougis_chart();
			$Ch->orderBy("title");
			$Ch->whereAdd("title IS NOT NULL");
                        $Ch->whereAdd("published IS TRUE");
			$Ch->find();
			while( $Ch->fetch() ) {                            
				$Charts[] = array(
					"text" => $Ch->title,
					"leaf" => true,
					"expanded" => false,
					"chart_id" => intval($Ch->id),
				);
			}
			$res = $Charts;
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	
	}
        
       
	public function buildIframeCode() {
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			$code = $Chart->getIframeCode($_REQUEST['width'], $_REQUEST['height']);
			
			$res = array(
				"success" => true,
				"code" => $code
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function saveChartConfig() {
		
		try {
			
			$ChartData = $_REQUEST['chart'];
			
			$Chart = new \Lougis_chart($ChartData['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Msg = ( $_REQUEST['save'] == 'true' ) ? "Kaavio tallennettu." : null;
			$ChartConf = $Chart->buildExtJsonChart($ChartData);
			if ( $_REQUEST['save'] == 'true' ) $Chart->saveChartConfig($ChartConf, $_REQUEST);
			
			$res = array(
				"success" => true,
				"msg" => $Msg,
				"conf" => $ChartConf
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function updateData() {
		
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Chart->updated_date = date(DATE_W3C);
			$data = json_decode($_REQUEST['data']);
			if ( count($data) == 0 ) throw new \Exception("Taulukko on tyhjä. Tyhjää taulukkoa ei voi tallentaa");
			//json file päivitys
			if ( !$Chart->updateData( $data ) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			//tietokantaan päivitys
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			$res = array(
				"success" => true,
				"msg" => "Taulukko tallennettu."
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function uploadData() {
	
		global $Site, $User;
		
		try {
			
			//if ( $_FILES['datafile']['type'] != 'text/csv' ) throw new \Exception("Virheellinen tiedostotyyppi! Tiedoston tulee olla CSV-tiedosto (tiedostopääte .csv)");
			
			if ( isset($_SESSION['new_chart_id']) ) {
				$Chart = new \Lougis_chart($_SESSION['new_chart_id']);
			} else {
				$Chart = new \Lougis_chart();
				$Chart->setNextKey();
			}
			if ( !$Chart->addUploadedDatafile($_FILES['datafile']) ) throw new \Exception("Datatiedostoa ei voitu tallentaa palvelimelle.");
			if ( !$Chart->buildJsonFileFromCsv() ) throw new \Exception("Dataa ei voitu lukea");
			$Chart->created_date = date(DATE_W3C);
			$Chart->created_by = $User->id;
			if ( !$Chart->save() ) throw new \Exception("Tilastoa ei voitu tallentaa");
			
			$_SESSION['new_chart_id'] = $Chart->id;
			
			$res = array(
				"success" => true,
				"chart" => $Chart->toChartArray()
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonHtmlOut($res);
	
	}
	public function getChartObj() {
                global $Site, $User;
                
                try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei löytynyt!");
			
			$res = array(
				"success" => true,
				//"chart" => $Chart->toChartArray(true, true, true)
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
                
        }
	public function getChartInfo() {
		
		global $Site, $User;
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei löytynyt!");
			
			$res = array(
				"success" => true,
				"chart" => $Chart->toChartArray(true, true, true)
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function deleteChart() {
		
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			if ( !$Chart->delete() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu poistaa. Ota yhteyttä ylläpitoon.");
			
			$res = array(
				"success" => true,
				"msg" => 'Tilasto "'.$Chart->title.'" poistettu.'
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function saveChartInfo() {
		
		global $Site, $User;
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart']['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilaston avaus epäonnistui.");
			$Chart->setFrom($_REQUEST['chart']);
			if ( empty($Chart->title) ) throw new \Exception("Tilaston otsikko on pakollinen!");
			if ( !$Chart->updateJsonFileFields($_REQUEST['fields']) ) throw new \Exception("Tilastotiedon tallennus epäonnistui!");
			if ( !$Chart->save() ) throw new \Exception("Tilaston tallennus epäonnistui.");
			
			if ( isset($_SESSION['new_chart_id']) ) unset($_SESSION['new_chart_id']);
			
			$res = array(
				"success" => true,
				"msg" => "Tilaston perustiedot tallennettu",
				"chart" => $Chart->toChartArray()
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
}
?>