<?php

function devlog($msg, $log = NULL, $backtrace = false) {
    
    if ( empty($log) ) $log = $_SERVER['HTTP_HOST'];
    
    $logdir = "/tmp/devlogs/";
    if ( !is_dir($logdir) ) mkdir($logdir, 0775, true);
    $logpath = $logdir.$log.".log";
    if ( !file_exists($logpath) ) touch($logpath);
    
    $fh = fopen($logpath, 'a');
    
	$trace = debug_backtrace();
	$file   = $trace[0]['file'];
	$file = substr($file, strlen(PATH_ROOT));
	$row = date('Y-m-d H.i.s')." - {$file}:{$trace[0]['line']}";
	if ( isset($trace[1]) ) $row .= "{$trace[1]['class']}:{$trace[1]['function']}";

    @fwrite($fh, $row."\r\n");
    @fwrite($fh, print_r($msg, true)."\r\n");
    if ( $backtrace ) @fwrite($fh, print_r($trace, true)."\r\n");
    @fclose($fh);
    @chmod($logpath, 0775);
    
}

function var_export_html($val, $return = false) {
    $ret = '<pre>'. htmlentities(var_export($val, true)). '</pre>';
    if($return) return $ret;
    echo $ret;
}

function print_r_html($val, $return = false) {
    $ret = '<pre>'. htmlentities(print_r($val, true)). '</pre>';
    if($return) return $ret;
    echo $ret;
}

function isDate($string) {

    $t = strtotime($string);
    $m = date('m',$t);
    $d = date('d',$t);
    $y = date('Y',$t);
    if ( $d == '01' && $m == '01' && $y = '1970' ) return false;
    return checkdate ($m, $d, $y);

}

?>