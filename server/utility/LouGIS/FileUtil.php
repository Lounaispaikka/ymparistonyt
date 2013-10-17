<?php
namespace Lougis\utility;

class FileUtil {

	public static function outputToWebserver( $FilePath ) {
	
		if ( !file_exists($FilePath) || !is_file($FilePath) ) {
	
			header('HTTP/1.0 404 Not Found');
			echo "<h1>404 Not Found</h1>";
			echo "The page that you have requested could not be found.";
			die;
				
		} else {
			
			header('Cache-Control: public');
	    	header('Accept-Ranges: bytes');
	    	header('Content-Length: ' . filesize($FilePath));
	    	/*
			$finfo = finfo_open(FILEINFO_MIME_TYPE, '/usr/share/file/magic.mgc');
			$mimetype = finfo_file($finfo, $FilePath);
			$mimetype = mime_content_type($FilePath);
			*/
			
			$mimetypes = array(
			    'gif' => 'image/gif',
			    'png' => 'image/png',
			    'jpg' => 'image/jpg',
			    'css' => 'text/css',
			    'js' => 'text/javascript',
			);
			$path_parts = pathinfo($FilePath);
			$ext = strtolower($path_parts['extension']);
			if (array_key_exists($ext, $mimetypes)) {
			    $mime = $mimetypes[$ext];
			} else {
			    $mime = 'application/octet-stream';
			}
			
	    	header("Content-type: ".$mime);
	    	
	    	if ( strpos($mime, 'application') !== false ) {
	    		header('Content-Disposition: attachment; filename='.basename($FilePath));
	    	}
	    	
	    	$mtime = filemtime($FilePath);
	    	$lastModified=date('r',$mtime);
	    	header("Last-Modified: ".$lastModified);
	    	
	    	if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime ) {
	    		header('HTTP/1.0 304 Not Modified');
	    		die;
	    	}
	    	
	    	ob_clean();
		    flush();
		    
		    readfile($FilePath);
		    exit;
		    
		}
	
	}

}
?>