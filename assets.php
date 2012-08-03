<?php 
/**
 * Compress and caches asset files: js, css (can be altered for other assets)
 *
 * Written by Miles Johnson (http://www.milesj.me), snippets from original CakePHP team
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

$TIME_START = microtime(true);
// Get asset type
$ext = trim(strrchr($url, '.'), '.');
$assetType = ($ext === 'css') ? 'css' : 'js';

// Wrong file
if (preg_match('|\.\.|', $url) || !(preg_match('|^c'. $assetType .'/(.+)$|i', $url, $regs) || 
        preg_match('|^(.+)/c'. $assetType .'/(.+)$|i', $url, $regs))) {
	die('Wrong File Name');
}

if (count($regs)>2){
    $plugin = 'apps'.DS.$regs[1].DS;
    $file = $regs[2];
} else {
    $plugin = null;
    $file = $regs[1];
}

//$cachePath = CACHE .'assets'. DS . str_replace(array('/','\\'), '-', $regs[1]);
if (!defined('CSS')){
    define('CSS', 'webroot/css/');
}
if (!defined('JS')){
    define('JS', 'webroot/js/');
}

if ($assetType == 'css') {
	$filePath = $plugin . CSS . $file;
	$fileType = 'text/css';
} else {
	$filePath = $plugin . JS . $file;
	$fileType = 'text/javascript';
}

if (!file_exists($filePath)) {
	die('Asset Not Found');
}

/**
 * Compress the asset
 * @param string $path
 * @param string $name
 * @return string
 */
function compress($path, $name, $type) {
	$input = file_get_contents($path);
	
	if ($type == 'css') {
		$stylesheet = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $input);
		$stylesheet = str_replace(array("\r\n", "\r", "\n", "\t", '/\s\s+/', '  ', '   '), '', $stylesheet);
		$stylesheet = str_replace(array(' {', '{ '), '{', $stylesheet);
		$stylesheet = str_replace(array(' }', '} '), '}', $stylesheet);
		$output = $stylesheet;
	} else {
		//App::import('Vendor', 'jsmin');
        require LIBS . 'Vendors' . DS . 'jsmin.php';
		$output = JSMin::minify($input);
	}
	
	$ratio = 100 - (round(strlen($output) / strlen($input), 3) * 100);
	$output = "/* File: $name, Ratio: $ratio% */\n". $output;
	return $output;
}

$pref = 'ASSETS_CACHE'.DS;

// Do compression and cacheing
$cached = Cache::read($pref.$filePath);
$templateModified = filemtime($filePath);

if (!$cached || ($templateModified > $cached['modified'])) {
    $output = compress($filePath, $file, $assetType);
    Cache::write($pref.$filePath, array('modified' => $templateModified, 'contents' => $output));
} else {
    echo '/*found*/';
    $output = $cached['contents'];
}

//header("Etag: ".md5($output));
header("Last-Modified: ". date("D, j M Y G:i:s", $templateModified) ." GMT");
header("Date: ". date("D, j M Y G:i:s", $templateModified) ." GMT");
header("Content-Type: ". $fileType);
header("Expires: ". gmdate("D, j M Y H:i:s", time() + 86400) ." GMT");
header("Cache-Control: public, max-age=86400"); // HTTP/1.1
header("Pragma: cache_asset");        // HTTP/1.0

$time = round(microtime(true) - $TIME_START, 4);
print "/* time: $time s */".$output;
