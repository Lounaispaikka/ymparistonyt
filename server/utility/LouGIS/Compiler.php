<?php
namespace Lougis\utility;

define('PATH_COMPILE_CACHE', PATH_CACHE.'compile/');
define('PATH_CLOSURE_JAR', PATH_SCRIPTS.'closure/compiler.jar');
class Compiler {

	public $name;
	public $type;
	public $jsFiles = array();
	public $jsMaxMTime = null;
	
	public function __construct( $name, $type = 'js' ) {
		
		$this->name = $name;
		$this->type = $type;
		
	}
	
	public function addJs( $relpath ) {
	
		if ( substr(strtolower($jsfile), -3) == '.js' ) throw new \Exception("This is not javascript!");
		$filepath = substr(PATH_SERVER, 0, -1).$relpath;
		if ( !file_exists($filepath) ) throw new \Exception("File ".$relpath." does not exist!");
		$fmtime = filemtime($filepath);
		if ( $fmtime > $this->jsMaxMTime ) $this->jsMaxMTime = $fmtime;
		$this->jsFiles[] = $filepath;
		return true;
		
	}
	
	public function outputFilesScriptTags() {
		
		foreach($this->jsFiles as $jsfile) {
			$relurl = '/'.substr($jsfile, strlen(PATH_SERVER));
			echo "\t".'<script type="text/javascript" src="'.$relurl.'"></script>'."\r\n";
		}
		
	}
	
	public function outputScriptHtml() {
		
		$minfile = PATH_COMPILE_CACHE.'js/'.$this->name.'-'.$this->jsMaxMTime.'.js';
		$md5file = PATH_COMPILE_CACHE.'js/'.$this->name.'-'.$this->jsMaxMTime.'.md5';
		
		$cacheDir = dirname($minfile);
		if ( !file_exists($cacheDir) ) mkdir($cacheDir, 0775, true);
		if ( !$this->fileOk($minfile) || !$this->checkMD5Match($md5file) ) $this->_compileJs( $minfile, $md5file );
		
		$relurl = '/cache/'.substr($minfile, strlen(PATH_COMPILE_CACHE));
		echo '<script type="text/javascript" src="'.$relurl.'"></script>';
		
	}
	
	public function fileOk( $file ) {
		
		if ( file_exists($file) && filesize($file) > 0 ) return true;
		return false;
		
	}
	
	
	private function checkMD5Match( $md5file ) {
		
		if ( file_exists($md5file) && file_get_contents($md5file) == $this->filesMD5Hash() ) return true;
		return false;
		
	}
	
	private function filesMD5Hash() {
		
		return md5(implode('', $this->jsFiles));
		
	}
	
	private function _compileJs( $minfile, $md5file ) {
		
		$cacheDir = dirname($minfile);
		$errorfile = $minfile.".errors";
		
		if ( !file_exists($cacheDir) ) mkdir($cacheDir, 0775, true);
		
		if ( !empty($this->name) ) exec("rm ".PATH_COMPILE_CACHE.'js/'.$this->name.'-*.*');
		
		$cmd  = "nohup java -jar ".PATH_CLOSURE_JAR." --compilation_level SIMPLE_OPTIMIZATIONS";
		foreach( $this->jsFiles as $jsfile ) {
			$cmd .= " --js ".$jsfile;
		}
		$cmd .= " --js_output_file ".$minfile;
		$cmd .= " 2>&1";
		$res = shell_exec($cmd);
		if ( $res != null ) { 
			print_r_html($res);die;
		} else {
			file_put_contents($md5file, $this->filesMD5Hash());
		}
		return true;
		
	}
	
}
?>