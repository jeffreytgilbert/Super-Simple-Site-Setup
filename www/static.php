<?php 

ini_set('memory_limit', '1024M');//
ini_set('max_execution_time', '6000');//
set_time_limit(6000);

if (!ini_get('display_errors')) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL | E_STRICT);
}

date_default_timezone_set('UTC');

function handle_static_content_request(){
	if(isset($_GET['type']) && isset($_GET['path'])){
		
		if( strstr($_GET['path'],'//') || 
			strstr($_GET['path'],'..') ||
			!in_array($_GET['type'], array('css','js','img'))){
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		
		if(substr($_GET['path'],0,1) == '/'){
			$path = substr($_GET['path'],1);
		} else {
			$path = $_GET['path'];
		}
		
		$file_path = dirname(__FILE__).'/../'.$_GET['type'].'/'.$path;
		if(!file_exists($file_path)){ header('HTTP/1.0 404 Not Found'); exit; }
		
		switch($_GET['type']){
			case 'css':
				header('Content-Type: text/css');
				echo file_get_contents($file_path);
			break;
			case 'js':
				header('Content-Type: text/javascript');
				echo file_get_contents($file_path);
			break;
			case 'img':
				header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600000));
				if(function_exists('mime_content_type')){
					header('Content-Type: '.mime_content_type($file_path));
				} else if(class_exists('finfo')) {
					header('Content-Type: '.finfo::file($file_path,FILEINFO_MIME_ENCODING));
				}
				echo file_get_contents($file_path);
			break;
		}
	} else {
		header('HTTP/1.0 404 Not Found');
		exit;
	}
}

handle_static_content_request();
