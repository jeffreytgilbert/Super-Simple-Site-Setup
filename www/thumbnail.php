<?php

include('../source/common.php');

$path = _g('path');

$path = str_replace('/', '', $path);
$path = str_replace('\\', '', $path);
$path = str_replace('..', '', $path);

$target_width = 220;
$target_height = 160;

if(!is_dir('../cache/'.$target_width.'x'.$target_height)){
	mkdir('../cache/'.$target_width.'x'.$target_height, 0777, true);
	chmod('../cache/'.$target_width.'x'.$target_height, 0777);
}

header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600000));
if(file_exists('../cache/'.$target_width.'x'.$target_height.'/'.$path)){
	
	if(function_exists('mime_content_type')){
		header('Content-Type: '.mime_content_type('../cache/'.$target_width.'x'.$target_height.'/'.$path));
	} else if(class_exists('finfo')) {
		header('Content-Type: '.finfo::file('../cache/'.$target_width.'x'.$target_height.'/'.$path, FILEINFO_MIME_ENCODING));
	}
	echo file_get_contents('../cache/'.$target_width.'x'.$target_height.'/'.$path);
		
} else if(file_exists('../img/content/pictures/'.$path)){
	
	$Image = ImageFile::create('../img/content/pictures/'.$path);
	
	$NewImage = ImageFile::resize($Image, imagesx($Image), imagesy($Image), 220, 160, true);
	
	ImageFile::outputJPEGToFile($NewImage, '../cache/'.$target_width.'x'.$target_height.'/'.$path, 100, false);
	
	ImageFile::outputJPEGToScreen($NewImage);
	
	@imagedestroy($Image);
}