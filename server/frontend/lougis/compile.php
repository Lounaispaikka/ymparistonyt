<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');
require_once(PATH_SERVER.'utility/LouGIS/FileUtil.php');

define('PATH_COMPILE_CACHE', PATH_CACHE.'compile/');
define('PATH_CLOSURE_JAR', PATH_SCRIPTS.'closure/compiler.jar');

class Compile extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function js() {
		
		try {
		
			$jsfile = substr(PATH_SERVER, 0, -1).$_SERVER['REQUEST_URI'];
			if ( file_exists($jsfile) && $this->_isJavascript($jsfile) ) {
				$jsfile = $this->_getCompiledJs( $jsfile );
			}
			\Lougis\utility\FileUtil::outputToWebserver($jsfile);
			
		} catch(\Exception $e) {
		
			
		}
		
	}
	
	private function _getCompiledJs( $jsfile ) {
		
		$minfile = PATH_COMPILE_CACHE.substr($jsfile, strlen(PATH_SERVER), -3).'.min.js';
		$md5file = PATH_COMPILE_CACHE.substr($jsfile, strlen(PATH_SERVER), -3).'.js.md5';
		if ( !file_exists($minfile) || !file_exists($md5file) || ( md5_file($jsfile) != file_get_contents($md5file) ) ) return $this->_compileJs($jsfile, $minfile, $md5file);
		return $minfile;
		
	}
	
	private function _compileJs( $jsfile, $minfile, $md5file ) {
		
		$cacheDir = dirname($minfile);
		if ( !file_exists($cacheDir) ) mkdir($cacheDir, 0775, true);
		$cmd = "java -jar ".PATH_CLOSURE_JAR." --compilation_level SIMPLE_OPTIMIZATIONS --js ".$jsfile." --js_output_file ".$minfile;
		$return = shell_exec($cmd);
		var_dump($cmd);
		var_dump($return);die;
		if ( !file_exists($minfile) ) { 
			file_put_contents($minfile, $return);
			readfile($minfile);
			die;
		} else {
			file_put_contents($md5file, md5_file($jsfile));
		}
		return $minfile;
		
	}
	
	private function _isJavascript( $jsfile ) {
		
		if ( substr(strtolower($jsfile), -3) == '.js' ) return true;
		return false;
		
	}
	
	
}
?>