<?php 

if (!ini_get('display_errors')) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL | E_STRICT);
}

date_default_timezone_set('UTC');

// read a cookie
function _c($key){
	// i use the prefilled_ prefix here because i want these things to eventually expire
	$c = isset($_COOKIE[$key])?$_COOKIE[$key]:null;
	//trim($c);
	return !empty($c)?$c:null;
}

// check if a post is set
function _p($key){
	$p = isset($_POST[$key])?$_POST[$key]:null;
	//trim($p);
	return !empty($p)?$p:null;
}

// check if a server var is set
function _s($key){
	$s = isset($_SERVER[$key])?$_SERVER[$key]:null;
	return !empty($s)?$s:null;
}

// output something from the db to the webpage. if you don't want it to break the page, use htmlentities to make it sanitized.
function _o($string){
	return htmlentities($string);
}

// check if a get var is set
function _g($key){
	$g = isset($_GET[$key])?$_GET[$key]:null;
	return !empty($g)?$g:null;
}

function in($needle, $haystack, $strict = false){
	if(is_array($haystack) === false || count($haystack) < 1) { return false; }
	return in_array($needle, $haystack, $strict)?true:false;
}

function array_contains($needles, $haystack, $strict = false) {
	if( is_array($haystack) === false ||
			count($haystack) < 1 ||
			is_array($needles) === false ||
			count($needles) < 1
	) { return false; }
	foreach ($needles as $needle) {
		if ( !in_array($needle, $haystack, $strict) ) { return false; }
	}
	return true;
}

function e($string,$encryption_type='sha256'){ // encrypt zee stringz
	return hash($encryption_type,$string);
}

function he($str) {
	return htmlentities($str);
}

function pr($var, $return=false, $encode=false) {
	$pre = '<pre style="text-align:left;">'.($encode ? he(print_r($var, 1)) : print_r($var, 1)).'</pre>';
	if ( $return ) {
		return $pre;
	}
	echo $pre;
}

function lower($str){
	return strtolower($str);
}

function upper($str){
	return strtoupper($str);
}

function pre($var, $return=false) {
	if ( $return ) return pr($var, true);
	pr($var, $return);
}

function vd($obj, $return=false) {
	if($return){
		return '<pre>'.var_dump($obj,1).'</pre>';
	}else{
		echo '<pre>'; var_dump($obj); echo '</pre>';
	}
}

// because fake ips from proxies are bogus. // read up here: http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
function guess_ip(){
	//check ip from share internet
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip=$_SERVER['HTTP_CLIENT_IP']; }
	//to check ip is pass from proxy
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
	// otherwise, you're probably an average joe or jane
	else { $ip=$_SERVER['REMOTE_ADDR']; }

	if($ip == '::1') {return '108.100.193.129';}
	return $ip;
}

/**
 * An XML analog to htmlentities
 * @param string $str
 * @return string
 */
function xmlentities($str)
{
	if (strlen($str)<1) return false;

	$str=str_replace(array ('&','"',"'",'<','>','?'),array ('&amp;','&quot;','&apos;','&lt;','&gt;','&apos;'),$str);

	$asc2uni=array();
	for($i=128;$i<256;$i++){
		$asc2uni[chr($i)] = "&#x".dechex($i).";";
	}
	$str=strtr($str,$asc2uni);

	return $str;
}

/**
 * Safe way to exit from a fatal error.
 * Shows error screen full of games to keep users appeased as well a hidden error.
 * @param string $error
 */
function and_die($error='', $message='')
{
	echo $error.$message;
	exit;
}


function throw_status_code($code){
	$codes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Requested range not satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out'
	);

	if(isset($codes[$code])) {
		$status = 'HTTP/1.1 '.$code.' '.$codes[$code];
		return true;
	} else { return false; }
}

/**
 * Redirect a page to a new location
 * @param string $where
 * @param bool $delayed
 */
function redirect($where='', $delayed=false)
{
	//	die('Script tried to redirect to '.$where.' using the old redirect method.');
	if(strlen($where) > 4)	{ $header_redirect = $where; }
	else					{ $header_redirect = '/'; }

	//	if(!strpos($where,'thedilly.com')) { $header_redirect='http://thedilly.com/'.$header_redirect; }

	if($delayed===true)		{ header('Location: '.$header_redirect); }			//What's the point? // the point is sometimes you dont want to exit the script instantly
	else					{ header('Location: '.$header_redirect); if (function_exists('session_write_close')) { session_write_close(); }  exit; }
}

function get_load_average(){
	$load_average = exec('/usr/bin/uptime');
	$load_average = explode('load average: ',$load_average);
	$load_average = explode(',',$load_average[1]);
	$load_average = trim($load_average[0]);
	return $load_average;
}

function get_url_params($params) {
	$post_params = array();
	foreach ($params as $key => &$val) {
		if (is_array($val)){
			$val = implode(',', $val);
		}
		$post_params[] = $key.'='.urlencode($val);
	}
	return implode('&', $post_params);
}



// this only works for ipv4. ip2long breaks on large ints so this is a workaround to phps bugginess
function ip_to_long($ip){
	$ips = explode('.',$ip); // why is this period escaped?
	if(count($ips)<4) {$long=0; } // was 1111111111. dunno why
	else { $long=($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256); }
	return $long;
}

/**
 * Turn a user name into a users folder by their user id.
 * @param int $user_id
 * @return string
 */
//function user_path($user_id) { return floor((int)$user_id/10000).'/'.$user_id; }

function build_options($options, $selected_val=null){
	$html_options = '';
	foreach($options as $val => $label){
		$html_options .= '<option value="'.$val.'" '. ($selected_val==$val?'selected="selected"':'') .'>'.$label.'</option>';
	}
	return $html_options;
}

function array_of_years($start_year, $less_years_from_today=0, $reversed=true){
	$a = array();
	if($reversed){
		for($i = date('Y')-$less_years_from_today;$i >= $start_year;$i--){ $a[$i]=$i; }
	} else {
		for($i = $start_year;$i <= date('Y')-$less_years_from_today;$i++){ $a[$i]=$i; }
	}
	return $a;
}

class HTMLHelper{
	public static function selectOptions($options, $selected_val=null){
		$html_options = '';
		foreach($options as $val => $label){
			$html_options .= '<option value="'.$val.'" '. ($selected_val==$val?'selected="selected"':'') .'>'.$label.'</option>';
		}
		return $html_options;
	}

	public static function radioOptions($name, $options, $selected_val=null){
		$html_options = '';
		foreach($options as $val => $label){
			$html_options .= '<label><input name="'.$name.'" type="radio" value="'.$val.'" '. ($selected_val==$val?'checked="checked"':'') .'> '.$label.'</label>';
		}
		return $html_options;
	}

	public static function wufooFormatedRadioList($name, $options, $selected_val=null, TemplateParser $tpl, $tab_index, $form_name){
		//		$tpl->load('fragments/form/radio_item_default.htm');
		//
		//		$html_options = $tpl->parse(array(
		//			'form_name' => $form_name,
		//			'input_name' => $name,
		//			'tab_index' => '',
		//			'option_number' => '',
		//			'val' => '',
		//			'checked' => '',
		//			'label' => ''
		//		));

		$html_options = '';
		$tpl->load('fragments/form/radio_item.htm');
		$x = 1;
		foreach($options as $val => $label){
			$html_options .= $tpl->parse(array(
					'form_name' => $form_name,
					'input_name' => $name,
					'tab_index' => ($tab_index + $x),
					'option_number' => $x,
					'val' => $val,
					'checked' => ($selected_val==$val?'checked="checked"':''),
					'label' => $label
			));
			$x++;
		}
		return $html_options;
	}

	public static function multiselectOptions($options, Array $selected_val=array()){
		$html_options = '';
		foreach($options as $val => $label){
			$html_options .= '<option value="'.$val.'" '. (in_array($val,$selected_val)?'selected="selected"':'') .'>'.$label.'</option>';
		}
		return $html_options;
	}

	public static function checkboxOptions($name, $options, Array $selected_val=array()){
		$html_options = '';
		foreach($options as $val => $label){
			$html_options .= '<label><input name="'.$name.'[]" type="checkbox" value="'.$val.'" '. (in_array($val,$selected_val)?'checked="checked"':'') .'> '.$label.'</label>';
		}
		return $html_options;
	}
}

function generate_random_code(){
	return sha1(uniqid(rand(), true));
}

/*
 function generate_secure_id($length=32){
$salt='';
srand((double)microtime()*1000000);
$chars = array ( 'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0');
for ($rand = 0; $rand <= $length-1; $rand++){
$random = rand(0, count($chars) -1);
$salt .= $chars[$random];
}
return $salt;
}
function ifdef($name,$value){
if(!defined($name)){ define($name,$value); }
}

*/
/**
 * Remove unwanted/unexpected whitespace from feed data
 * @param string $buffer
 * @return string
 * @author Chris Gregory
 function whitespace_is_evil($buffer)
 {
 // replace all the apples with oranges
 $buffer_array=explode("\n", $buffer);
 $html='';
 foreach($buffer_array as $stuff) { if(trim($stuff)!='') { $html.=$stuff."\n"; } }
 return $html;
 }
 */
/**
 * trims text to a space then adds ellipses if desired
 * @param string $input text to trim
 * @param int $length in characters to trim to
 * @param bool $ellipses if ellipses (...) are to be added
* @param bool $strip_html if html tags are to be stripped
* @return string
function trim_text($input, $length, $ellipses = true, $strip_html = true) {
//strip tags, if desired
if ($strip_html) {
$input = strip_tags($input);
}

//no need to trim, already shorter than trim length
if (strlen($input) <= $length) {
return $input;
}

//find last space within length
$last_space = strrpos(substr($input, 0, $length), ' ');
$trimmed_text = substr($input, 0, $last_space);

//add ellipses (...)
if ($ellipses) {
$trimmed_text .= '...';
}

return $trimmed_text;
}
*/


if(stristr($_SERVER['SERVER_SOFTWARE'],'Win32')){ define('SLASH','\\'); }
else { define('SLASH','/'); }

function os_path($file_path){
	return str_replace('/',SLASH,$file_path);
}

class File
{
	public static function filesInFolderToArray($path, $include_hidden_files=false, $exclude_types=array()){
		$file_array = array();
		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && !is_dir($path.'/'.$file)) {
					$extention = explode('.',$file);
					$extention = array_pop($extention);
						
					if($include_hidden_files && !in($extention,$exclude_types)){
						$file_array[$path.'/'.$file] = $file;
					} else {
						if(substr($file,0,1) != '.' && $file != "Thumb.db" && $file != "Thumbs.db" && !in($extention,$exclude_types)){
							$file_array[$path.'/'.$file] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}

	public static function folderToArray($path, $include_hidden_files=false){
		$file_array = array();
		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if($include_hidden_files){
						$file_array[$path.'/'.$file] = $file;
					} else {
						if(substr($file,0,1) != '.' && $file != "Thumb.db" && $file != "Thumbs.db"){
							$file_array[$path.'/'.$file] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}

	public static function folderWithSubfoldersToArrays($path, $include_hidden_files=false){
		$file_array = array();
		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if(is_dir($path.'/'.$file)){
						if($include_hidden_files){
							$file_array[$path.'/'.$file] = self::folderWithSubfoldersToArrays($path.'/'.$file, $include_hidden_files);
						} else {
							if(substr($file,0,1) != '.' && $file != "Thumb.db" && $file != "Thumbs.db"){
								$file_array[$path.'/'.$file] = self::folderWithSubfoldersToArrays($path.'/'.$file, $include_hidden_files);
							}
						}
					} else {
						if($include_hidden_files){
							$file_array[$path.'/'.$file] = $file;
						} else {
							if(substr($file,0,1) != '.' && $file != "Thumb.db" && $file != "Thumbs.db"){
								$file_array[$path.'/'.$file] = $file;
							}
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}
}

final class ImageFile {

	/**
	 * Internal tool used to create a gd resource which can be molded into any image type from supported formats
	 */
	public static function create($image_path){
		if(!file_exists($image_path)) { return false; }

		switch(exif_imagetype($image_path))
		{
			case IMAGETYPE_JPEG:
				if(@imagetypes() & IMG_JPG) { return @imagecreatefromjpeg($image_path); }
				else { return false; }
				break;
				/*
				 case IMAGETYPE_GIF:
				if (imagetypes() & IMG_GIF) { return imagecreatefromgif($image_path); }
				else { return false; }
				break;
				*/
			case IMAGETYPE_PNG:
				if(@imagetypes() & IMG_PNG) { return @imagecreatefrompng($image_path); }
				else { return false; }
				break;
			default:
				return false; // unsupported image type (by the gd)
				break;
		}
	}

	// this method from php.net example. comment posted by thciobanu. works really well.
	public static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $trans = NULL){
		$dst_w = imagesx($dst_im);
		$dst_h = imagesy($dst_im);

		// bounds checking
		$src_x = max($src_x, 0);
		$src_y = max($src_y, 0);
		$dst_x = max($dst_x, 0);
		$dst_y = max($dst_y, 0);
		if ($dst_x + $src_w > $dst_w)
			$src_w = $dst_w - $dst_x;
		if ($dst_y + $src_h > $dst_h)
			$src_h = $dst_h - $dst_y;

		for($x_offset = 0; $x_offset < $src_w; $x_offset++)
			for($y_offset = 0; $y_offset < $src_h; $y_offset++)
			{
				// get source & dest color
				$srccolor = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x + $x_offset, $src_y + $y_offset));
				$dstcolor = imagecolorsforindex($dst_im, imagecolorat($dst_im, $dst_x + $x_offset, $dst_y + $y_offset));

				// apply transparency
				if (is_null($trans) || ($srccolor !== $trans))
				{
					$src_a = $srccolor['alpha'] * $pct / 100;
					// blend
					$src_a = 127 - $src_a;
					$dst_a = 127 - $dstcolor['alpha'];
					$dst_r = ($srccolor['red'] * $src_a + $dstcolor['red'] * $dst_a * (127 - $src_a) / 127) / 127;
					$dst_g = ($srccolor['green'] * $src_a + $dstcolor['green'] * $dst_a * (127 - $src_a) / 127) / 127;
					$dst_b = ($srccolor['blue'] * $src_a + $dstcolor['blue'] * $dst_a * (127 - $src_a) / 127) / 127;
					$dst_a = 127 - ($src_a + $dst_a * (127 - $src_a) / 127);
					$color = imagecolorallocatealpha($dst_im, $dst_r, $dst_g, $dst_b, $dst_a);
					// paint
					if (!imagesetpixel($dst_im, $dst_x + $x_offset, $dst_y + $y_offset, $color))
						return false;
					imagecolordeallocate($dst_im, $color);
				}
			}
			return true;
	}

	/*
	 // this method from php.net example. It leaves crunchy bits around the image (maybe uses 8bit mapping?)
	public static function alphaOverlay($destImg, $overlayImg, $imgW, $imgH){
	for($y=0;$y<$imgH;$y++){
	for($x=0;$x<$imgW;$x++){
	$ovrARGB = imagecolorat($overlayImg, $x, $y);
	$ovrA = ($ovrARGB >> 24) << 1;
	$ovrR = $ovrARGB >> 16 & 0xFF;
	$ovrG = $ovrARGB >> 8 & 0xFF;
	$ovrB = $ovrARGB & 0xFF;

	$change = false;
	if($ovrA == 0){
	$dstR = $ovrR;
	$dstG = $ovrG;
	$dstB = $ovrB;
	$change = true;
	} elseif($ovrA < 254){
	$dstARGB = imagecolorat($destImg, $x, $y);
	$dstR = $dstARGB >> 16 & 0xFF;
	$dstG = $dstARGB >> 8 & 0xFF;
	$dstB = $dstARGB & 0xFF;

	$dstR = (($ovrR * (0xFF-$ovrA)) >> 8) + (($dstR * $ovrA) >> 8);
	$dstG = (($ovrG * (0xFF-$ovrA)) >> 8) + (($dstG * $ovrA) >> 8);
	$dstB = (($ovrB * (0xFF-$ovrA)) >> 8) + (($dstB * $ovrA) >> 8);
	$change = true;
	}

	if($change){
	$dstRGB = imagecolorallocatealpha($destImg, $dstR, $dstG, $dstB, 0);
	imagesetpixel($destImg, $x, $y, $dstRGB);
	}
	}
	}
	return $destImg;
	}
	*/

	public static function layerPNGs(Array $image_paths, $image_root=''){
		clearstatcache();
		$last_pos = (count($image_paths)-1);
		if(count($image_paths) < 2) { throw('Array must be at least 2 elements in length for Image::layer'); return false; }

		// get image size
		$size_info = getimagesize($image_root.$image_paths[0]);
		$dst_w = $src_w = $size_info[0];
		$dst_h = $src_h = $size_info[1];

		$master_image = imagecreatetruecolor($src_w, $src_w);
		//$trans_color = imagecolorallocatealpha($master_image, 0, 0, 0, 127);
		imagefill($master_image, 0, 0, imagecolorallocatealpha($master_image, 255, 255, 255, 127) ); // blends well with white bgs but not dark ones

		imagealphablending($master_image, false);
		imagesavealpha($master_image, true);

		$i=0;
		$images = array();
		foreach($image_paths as $image_path){
			//			clearstatcache();
			//$images[$i] = @imagecreatefrompng($image_root.$image_path);
			$images[$i] = self::create($image_root.$image_path);
			imagealphablending($images[$i], false);
			imagesavealpha($images[$i], true);
			if($images[$i] === false){ throw('An element in Array paramater did not load for Image::layer'); return false; }
			$i++;
		}

		for($x = 0; $x <= $last_pos; $x++){
			// none of these commented out examples work perfectly. The only one that does is the imagecopymerge_alpha method
			//			imagecopymerge($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
			//			imagecopyresam($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
			//			imagecopyresampled($images[$x], $images[($x+1)], 0, 0, 0, 0, $src_w, $src_h, 100);
			//			$master_image = self::alphaOverlay($master_image, $images[$x], $src_w, $src_h);
			//			imagecopyresampled($master_image, $images[$x], 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
			self::imagecopymerge_alpha($master_image, $images[$x], 0, 0, 0, 0, $src_w, $src_h, 100);
		}

		foreach($images as $image){
			@imagedestroy($image);
		}

		//		header('Expires: Thu, 15 Apr 2015 20:00:00 GMT');
		//		header('Cache-Control "max-age=290304000, public"');
		//		header('Last-Modified: Fri, 27 Jun 2008 21:29:33 GMT'); // doesnt matter what time, just as long as its in the past and its the same
		//		header('Content-type: image/png');
		//		imagepng($master_image, null);

		return $master_image;
	}

	/**
	 * Internal tool used to resize a file
	 */
	public static function resize($old_image, $width, $height, $target_width=140, $target_height=105, $crop=false){ // , $is_gif=false

		/*
		 // If we're using a gif (from a fresh upload) make sure we dont use true color or it will cause php to crash
		if($is_gif) { $new_image = imagecreate($target_width, $target_height); }
		else { $new_image = imagecreatetruecolor($target_width, $target_height); }
		*/
		$new_image = imagecreatetruecolor($target_width, $target_height);

		// Crop an image to a certain aspect ratio based on width and height. Great for thumbnail making.
		if($width > $height) {
			// width * ratio
			$adjusted_width = $width * $target_height / $height;
			// chop off
			if($crop) { $starting_point_x = $target_width - $adjusted_width; }
			else { $starting_point_x = 0; }
			imagecopyresampled($new_image, $old_image, $starting_point_x, 0, 0, 0, $adjusted_width, $target_height, $width, $height);

		} else if(($width < $height) || ($width == $height)) {
			// height * ratio
			$adjusted_height = $height * $target_width / $width;
			imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $target_width, $adjusted_height, $width, $height);

		} else {
			imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $target_width, $target_height, $width, $height);
		}

		return $new_image;
	}

	public static function outputJPEGToScreen($image, $quality=100, $destroy_original=true){
		header('Content-type: image/jpeg');
		if(@imagejpeg($image, null, $quality)){
			// prevent memory leaks
			if($destroy_original){ @imagedestroy($image); }
			return true;
		} else { return false; }
	}

	public static function outputPNGToScreen($image, $destroy_original=true){
		header('Content-type: image/png');
		if(@imagepng($image, null)){
			// prevent memory leaks
			if($destroy_original){ @imagedestroy($image); }
			return true;
		} else { return false; }
	}

	public static function outputJPEGToFile($image, $save_to=false, $quality=100, $destroy_original=true){
		if(@imagejpeg($image, $save_to, $quality)){
			chmod($save_to,0755);
			// prevent memory leaks
			if($destroy_original){ @imagedestroy($image); }
		} else { return false; }
	}

	public static function outputPNGToFile($image, $save_to=false, $destroy_original=true){
		if(@imagepng($image, $save_to)){
			chmod($save_to,0755);
			// prevent memory leaks
			if($destroy_original){ @imagedestroy($image); }
		} else { return false; }
	}
}

/**
 * Create a standardized way to generate links for pages of sql results
 * @package Navitation
 */
class PageData
{
	// original call args
	private $origin=0;
	private $start=0;
	private $total_items=0;
	private $items_per_page=0;
	private $pages_shown=7;
	private $url='';
	//	private $css='pages';
	//	private $style='standard';
	// calculated data
	private $total_pages=0;
	private $first_page=0;
	private $previous_page=0;
	private $next_page=0;
	private $last_page=0;
	private $this_page=0;
	private $remaining_items=0;
	// page links
	// - can be html, images, whatever
	private $first_button = '&laquo; First';
	private $back_button = '&#8249; Back';
	private $next_button = 'Next &#8250;';
	private $last_button = 'Last &raquo;';
	// - actual link with button inside it
	private $first_link = '';
	private $back_link = '';
	private $next_link = '';
	private $last_link = '';
	// store rendered pages so they dont rerender if they're recalled (its a speed thing)
	private $cache=array();

	public function getTotalItems(){
		return $this->total_items;
	}

	/**
	 * Return a list of pages in a string given the page position, total results, results per page, and url to target.
	 * @param int $start
	 * @param int $total_items
	 * @param int $items_per_page
	 * @param string $url
	 * @param string $css
	 * @param string $style
	 * @return string
	 */
	public function __construct($start, $total_items, $items_per_page, $pages_shown=7)
	{
		// set initial values
		$this->origin=$start;
		$this->total_items=$total_items;
		$this->items_per_page=$items_per_page;
		$this->pages_shown=$pages_shown;

		// page math
		$this->total_pages = ($this->items_per_page > 0)?ceil($this->total_items/$this->items_per_page):0;

		$remainder=($this->items_per_page > 0)?$start%$this->items_per_page:0;
		if($remainder == 0) { $this->start=$start; }
		else { $this->start=$start-$remainder; }
		unset($remainder);

		if($this->total_pages > 0)
		{
			$this->previous_page=$this->start-$this->items_per_page;
			$this->next_page=$this->start+$this->items_per_page;
			$this->this_page= ($this->items_per_page > 0)?$this->start/$this->items_per_page:0;

			// If this isnt the first page, figure out how many pages will be displayed
			if($this->this_page > 0)
			{
				// half the total pages subtracted from your starting point
				$this->remaining_items=ceil($this->this_page-($this->pages_shown/2));
				// if the remainder is a positive number then just use half for the rest of the display
				if($this->remaining_items >= 0)
				{
					$this->first_page=$this->remaining_items;
					$this->remaining_items=$this->pages_shown/2;
				}
				// otherwise start from 0
				else
				{
					$this->first_page=0;
					$this->remaining_items=$this->pages_shown-(($this->pages_shown/2)+$this->remaining_items);
				}
			}
			else
			{
				// If no pages have been displayed you still have to show all the pages possible.
				$this->remaining_items=$this->pages_shown;
				$this->first_page=0;
			}

			// Calculate where the last page would be by default.
			$this->last_page=$this->remaining_items+$this->this_page;
			// If the remainder of pages is more than the total of pages available, just use the total pages
			if($this->last_page > $this->total_pages) { $this->last_page=$this->total_pages; }
		}
	}

	public function display($url, $css='pages', $style='standard')
	{
		// if we've already rendered the page, return the finished copy without rerendering it
		if(isset($this->cache[$url.$css.$style])) { return $this->cache[$url.$css.$style]; }

		$this->url=$url;

		if($this->total_pages==1) { return ''; } // yeah, just dont display 1 page things
		$page_list='';

		// First
		if($this->start > 0)
		{ $this->first_link = self::link($this->url, $this->first_button, 0).' '; }
		else
		{ $this->first_link = ''; }

		// Back
		if($this->previous_page >= 0)
		{ $this->back_link = self::link($this->url, $this->back_button, $this->previous_page).' '; }
		else
		{ $this->back_link = ''; }

		// Next
		if($this->next_page < $this->total_items)
		{ $this->next_link = ' '.self::link($this->url, $this->next_button, $this->next_page); }
		else
		{ $this->next_link = ''; }

		// Last
		if(($this->start+$this->items_per_page) < $this->total_items)
		{ $this->last_link = ' '.self::link($this->url, $this->last_button, ($this->total_pages-1)*$this->items_per_page); }
		else
		{ $this->last_link = ''; }

		// Individual pages
		$iterations = 0;
		for($x=$this->first_page ; $x<$this->last_page ; $x++)	// march version
		{
			// why is this here? should this be a "return" when nothings finished rendering?
			//if($iterations > 50) 		{ return $page_list; }
			if($x==$this->this_page)	{ $page_list.='<span class="current">'.number_format($x+1).'</span> '; }
			else						{ $page_list.=self::link($this->url, number_format($x+1), ($x*$this->items_per_page),null).' '; }
			$iterations++;
		}

		switch($style)
		{
			case 'standard':
				// 1-20 of 1000 << First | < Back | Next > | Last >>
				if(($this->start+$this->items_per_page) > $this->total_items)	{ $page_end=$this->total_items; }
				else 															{ $page_end=$this->start+$this->items_per_page; }

				$this->cache[$url.$css.$style]='<div style="margin:5px 0px 5px 0px">'
						.'<div class="'.$css.'" style="width:200px; float:right; text-align:right">'.$this->first_link.' '.$this->back_link.' '.$this->next_link.' '.$this->last_link.'</div>'
								.'<div class="'.$css.'" style="width:200px; float:left; text-align:left">'.number_format(($this->start+1)).' - '.number_format(($page_end)).' of '.number_format($this->total_items).' Results. </div>'
										.'<div class="'.$css.'">'.$page_list.'</div>'
												.'</div>';
				break;
			default:
				$this->cache[$url.$css.$style]='Unsupported linking type.';
				break;
		}
		return $this->cache[$url.$css.$style];
	}

	/**
	 * Create a link
	 * @param string $url
	 * @param string $label
	 * @param int $pos
	 * @param string $css
	 * @return string
	 */
	private static function link($url, $label, $pos, $css=null)
	{
		if(isset($css)) { $style=' class="'.$css.'"'; }
		else { $style=''; }

		if(empty($pos)) { $pos = 0; }

		return '<a'.$style.' href="'.$url.'start='.$pos.'">'.$label.'</a>';
	}

	public function getData($data)
	{
		if(isset($this->$data)) { return $this->$data; }
		else { return null; }
	}
}


class PagingConfig{
	private $_start;
	private $_limit;
	private $_isQueryUpdatingOk;

	public function __construct($start, $limit, $do_add_paging_to_query=true){
		$this->_start = $start;
		$this->_limit = $limit;
		$this->_isQueryUpdatingOk = $do_add_paging_to_query;
	}

	public function getStart(){ return (int)$this->_start; }
	public function getLimit(){ return (int)$this->_limit; }
	public function isQueryUpdatingOk(){ return (bool)$this->_isQueryUpdatingOk; }
}


/**
 * I got this abstraction layer from zend.com's code gallery under abstraction classes version 1.2
 * This I guess would be 1.3 since it's running under php5 strict, not php4.
 * I changed the casing, white spacing, and names to conform to common practice
 * @package CoreComponents
 */
abstract class SQLConnection
{
	protected $host				= '';
	protected $db_handle		= null;
	protected $statement		= null;
	protected $query_result		= null;
	protected $autocommit		= true;
	protected $next_row_number	= 0;
	protected $rows				= 0;
	protected $last_type		= '';
	protected $prepared_format	= array();
	protected $bad_queries		= array();
	protected $slow_queries		= array();
	protected $queries			= array();
	protected $_connection_name = '';

	public $row_data			= array();

	const LIKE_WC_LAST = 'last';
	const LIKE_WC_FIRST = 'first';
	const LIKE_WC_FIRST_LAST = 'first_last';
	const LIKE_WC_SPACES = 'spaces';

	/**
	 * Open a connection to your database
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @param bool $autocommit
	 */
	abstract function __construct($connection_name, $host, $user, $pass, $db='', $port='', $autocommit=true);

	/**
	 * Disconnects from the database
	*/
	abstract function __destruct();

	/**
	 * Opens a database connection
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @param bool $autocommit
	*/
	abstract function open($host, $user, $pass, $db, $port, $autocommit);

	/**
	 * Close connections to current db
	*/
	abstract function close();

	/**
	 * Queries the database
	 * @param string $query
	*/
	abstract function query($query, $line, $file);

	/**
	 * Prepare an sql statement for execution
	 * @param string $query
	*/
	abstract function prepare($query, $line, $file);

	/**
	 * Execute a previously prepared sql statement
	 * @param array $values
	*/
	abstract function execute(Array $values, Array $integers=array()); // the integers part is a compatiblity fix for this bug: http://bugs.php.net/bug.php?id=40740

	/**
	 * Terminate the last prepared sql statement
	 * @param array $values
	*/
	abstract function terminate();

	/**
	 * Reads one row of data
	*/
	abstract function readRow($return_type='ASSOC');

	/**
	 * Commit queries in  current transaction
	*/
	abstract function commit();

	/**
	 * Rollback queries in current transaction
	*/
	abstract function rollback();

	/**
	 * Escape strings for queries (useful for things like sessions.)
	*/
	abstract function escape($data);

	public function like($string, $style){
		$string = $this->db_handle->quote($string);
		switch($style){
			case SQLConnection::LIKE_WC_FIRST:
				return "'%".substr($string,1);
			case SQLConnection::LIKE_WC_LAST:
				return substr($string,0,-1)."%'";
			case SQLConnection::LIKE_WC_FIRST_LAST:
				return "'%".substr($string,1,-1)."%'";
			case SQLConnection::LIKE_WC_SPACES:
				return preg_replace('/\s+/', '%', "'%".substr($string,1,-1)."%'");
			default:
				die('Unsupported format. Use SQLConnection constants.');
		}
	}

	/**
	 * Return the number of affected rows
	 */
	abstract function affectedRows();

	/**
	 * Return the last inserted id
	*/
	abstract function insertId();

	/**
	 * Turn autocommits on or off
	 * @param bool $autocommit
	*/
	final public function setAutoCommit($autocommit) { $this->autocommit=$autocommit; }

	/**
	 * Returns all queries that errored and the reason of the error in an array.
	 * @return array
	 */
	final public function getErrors() { return $this->bad_queries; }

	/**
	 * Returns all queries that ran
	 * @return array
	 */
	final public function getQueries() { return $this->queries; }

	/**
	 * Returns slow queries that ran
	 * @return array
	 */
	final public function getSlowQueries() { return $this->slow_queries; }
}


/**
 * This object class allows functions to access (by reference) the default, current, and one time settings
 * for use in nested functions (see ReturnHandler for examples). This helps ease code creation by creating
 * a common reference: the settings registry. The functions can then access it thereby eliminating
 * the need for additional arguments in the calling function and also extends the ability of these
 * functions by allowing you to change their functionality  without changing the function call.
 * This encourages the use of a centralized settings structure within the core for easier management
 * of standardized use.
 * @package CoreComponents
 */
final class SettingsRegistry
{
	private $original=array();
	private $default=array();
	private $current=array();

	/**
	 * Adds default settings into an array and sets the current values as such
	 * @param array $default
	*/
	function __construct($default)
	{
		$this->current=$default;
		$this->default=$default;
		$this->original=$default;
	}

	/**
	 * Get current setting
	 * @param string $setting
	 * @return unknown
	 */
	function getCurrent($setting)
	{
		$return=$this->current[$setting];
		$this->current[$setting]=$this->default[$setting];
		return $return;
	}

	/**
	 * Stores a value which is restored to the default after it has been read once
	 * @param string $setting
	 * @param unknown $value
	 */
	function setCurrent($setting, $value)
	{
		$this->current[$setting]=$value;
	}

	/**
	 * Creates a default value which will be stored until the script ends or it is changed again
	 * @param string $setting
	 * @param unknown $value
	 */
	function setDefault($setting, $value)
	{
		$this->default[$setting]=$value;
		$this->current[$setting]=$this->default[$setting];
	}

	/**
	 * Restore registry settings to the way they were upon construction
	 */
	function restoreDefaults()
	{
		$this->default=$this->original;
		$this->current=$this->original;
	}
}


class RuntimeInfo{

	public static function instance(Startup $Startup=null){
		static $RuntimeInfo = null;
		if(!is_null($Startup)) {
			$RuntimeInfo['site'] = $Startup;
			return $Startup;
		} else {
			return $RuntimeInfo['site'];
		}
	}
	
}

RuntimeInfo::instance(new Startup());

class Startup{
	
	public function __construct(){
		$this->_settings = new SettingsRegistry(array(
				'cols'=>50,
				'cut'=>' <wbr>',
				'min'=>null,
				'max'=>null,
				'country'=>'US',
				'check_mx'=>false,
				'use_timestamps'=>false,
				'gmt_offset'=>-5,
				'use_offset'=>true,
				'date_to'=>date( 'Y-m-d H:i:s' ),
				'highlight'=>'',
				'width'=>320,
				'height'=>240,
				'work_mode'=>null,
				'strip_smilies'=>null,
				'strip_images'=>null,
				'strip_nudes'=>null,
				'strip_tags'=>null,
				'strip_colors'=>null,
				'strip_sounds'=>null,
				'strip_videos'=>null,
				'strip_sizes'=>null,
				'strip_highlights'=>null,
				'strip_swears'=>true,
				'strip_slander'=>true,
				'strip_exploits'=>null,
				'language'=>'en'
		));
	}
	
	// settings registry
	private $_settings;
	public function settings(){
		return $this->_settings;
	}
}

class DataType{
	const BOOLEAN='boolean';
	const TEXT='text';
	const DATE='date';
	const NUMBER='number';
	const OBJECT='object';
	const COLLECTION='collection';
	const BINARY='binary';
	const TIMESTAMP='timestamp';
	const PHP_ARRAY='php_array';
	const SECRET='secret';
}

class Parse{
	/**
	 * Remove excess slashes and decode strange utf-8 codes for output
	 * does not parse bbcode, language, smilies, etc
	 * @param string $str
	 * @return string
	 */
	public static function decode($str,$return_html=false)
	{
		$str=str_replace("\x00",'',stripslashes(trim($str)));
	
		$convmap = array(0x0, 0x2FFFF, 0, 0xFFFF); // i dont know what this does, but i do know its necessary for utf8 support -- jeff
		$str = mb_encode_numericentity($str, $convmap, 'UTF-8');
	
		// NOTE: UTF-8 does not work! -- php.net author
		// if its from a latin1 source, works fine from a utf-8 source --Jeff
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		$str = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $str);
	
		if($return_html === true) { return $str; }
		else { return htmlspecialchars(strip_tags($str)); }
	}
}

class Model implements Iterator{
	// Whatevers used most often could be specified here.
	protected $_return_type	= '$return = Parse::decode()'; // parse everything
	protected $_last_filter	= null; // keep previous filter on record
	protected $_current_filter	= null; // the filter currently selected
	protected $_default_filter = null; // parse everything
	// Last call type $_cached_returns[$str_name][$return_type] caches the parsed string
	// so it doesnt have to reparse if it echos again.
	protected $_cached_returns = array();

	// Store all the data in one associatively indexed array for dynamic handling of any number of fields
	protected $_data = array();
	protected $_allowed_data = array();

	protected $_size_of_allowed_data = 0;

	protected $_size_of_data = 0;

	/**
	 *This should set the data based on the results returned from the db and the column names returned.
	 * @param array $data
	 * @param string $default_filter
	 */
	public function __construct(Array $data=array(), $default_filter='Parse::decode'){
		$this->setDataFromArray($data);
		$this->_default_filter=$default_filter;
	}

	public function set($key_name, $data_value){
		if($this->_size_of_allowed_data === 0 || in_array($key_name,array_keys($this->_allowed_data))){
			$this->_data[$key_name] = $data_value;
			return true;
		}
		return false;
	}

	public function setDataFromArray(Array $data){
		//		print_r($data);
		foreach($data as $column => $value){
			//			echo "{$column} => {$value}";
			$this->set($column,$value);
		}
	}

	public function setNull(){
		$this->_data = array();
	}

	public function get($key_name, $return_blank_object_if_null=false){
		if(!$return_blank_object_if_null){
			return isset($this->_data[$key_name])?$this->_data[$key_name]:null;
		} else {
			return isset($this->_data[$key_name])?$this->_data[$key_name]:new Model();
		}
	}

	public function boolean($key_name){
		$return = isset($this->_data[$key_name])?$this->_data[$key_name]:false;
		return (
				!isset($return) ||
				is_null($return) ||
				empty($return) ||
				strtolower($return) === "false"
		)?false:true;
	}

	public function getArray($key_name){
		$return = isset($this->_data[$key_name])?$this->_data[$key_name]:array();
		return (is_array($return))?$return:array();
	}

	public function getObject($key_name,$object_type_if_null='DataObject'){
		$return = isset($this->_data[$key_name])?$this->_data[$key_name]:new $object_type_if_null();
		return ($return instanceof $object_type_if_null)?$return:new $object_type_if_null();
	}

	public function getCollection($key_name,$object_type_if_null='DataCollection'){
		$return = isset($this->_data[$key_name])?$this->_data[$key_name]:new $object_type_if_null();
		return ($return instanceof $object_type_if_null)?$return:new $object_type_if_null();
	}

	public function getCurrentDataFieldsToString(){
		$keys = array_keys($this->_data);
		$dataFields = "'".implode("','",$keys)."'";
		return $dataFields;
	}

	public function getAllowData(){
		return $this->_allowed_data;
	}

	protected function setAllowedData(Array $a, $typed_array = false){
		if(!$typed_array){
			$a = array_flip($a);
			foreach($a as $key => $value){ $a[$key] = 'text'; }
		}

		$this->_allowed_data = $a;
		$this->_size_of_allowed_data = count($this->_allowed_data);
	}

	protected function addAllowedData(Array $a, $typed_array = false){
		if(!$typed_array){
			$a = array_flip($a);
			foreach($a as $key => $value){ $a[$key] = 'text'; }
		}
		if($this->_size_of_allowed_data == 0){
			$this->_allowed_data = $a;
		} else {
			$this->_allowed_data = array_merge($this->_allowed_data,$a);
		}
		$this->_size_of_allowed_data = count($this->_allowed_data);
	}

	public function parse($var_name){
		switch($this->_allowed_data[$var_name]){
			case DataType::BINARY: die('Unsupported filtered data type: Binary');
			case DataType::BOOLEAN: return $this->getData($var_name,'boolval');
			case DataType::COLLECTION: die('Unsupported filtered data type: Collection');
			case DataType::DATE: return $this->getData($var_name,'Parse::localdate');
			case DataType::NUMBER: return $this->getData($var_name,'Parse::number');
			case DataType::OBJECT: die('Unsupported filtered data type: Object');
			case DataType::PHP_ARRAY: die('Unsupported filtered data type: PHP Array');
			case DataType::SECRET: return '************';
			case DataType::TEXT: return $this->getData($var_name,'Parse::title');
			case DataType::TIMESTAMP: return $this->getData($var_name,'Parse::localtime'); // this is likely wrong. Not currently using timestamps anywhere
		}
	}

	/**
	 * Return parsed data from a variable
	 *
	 * @param string $var_name
	 * @param string $filter
	 * @param int $min
	 * @param int $max
	 * @param bool $cache
	 * @return mixed
	 */
	public function getData($var_name, $filter=null, $min=null, $max=null, $cache=false)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		// If the new min or max has been set, use the settings registry to avoid errors and avoid additional code
		if(isset($min)) { $SetReg->setCurrent('min', $min); }
		if(isset($max)) { $SetReg->setCurrent('max', $max); }

		// If the filter isn't passed, use the default
		if(is_null($filter)){ $this->_current_filter = $this->_default_filter; }
		else { $this->_current_filter = $filter; }

		// if the data doesnt exist, save php from erroring by setting it as null
		if(!isset($this->_data[$var_name])) { $data = null; }
		else { $data = $this->_data[$var_name]; }

		// If we're caching results, call this as a caching request
		if($cache===true) { return $this->returnParsedString($data, $var_name, $cache); }
		// otherwise rerender it
		else { return $this->returnParsedString($data, $var_name); }
	}

	/**
	 * Clear the cached returns to free memory
	 */
	public function clearCache() { unset($this->_cached_returns); }

	/**
	 * Set new default filter
	 * @param string $filter
	 */
	public function setDefaultFilter($filter) { $this->_default_filter = $filter; }

	/**
	 * Automatically parse data with the option to cache returns in an array
	 * @param string $string
	 * @param string $var_name
	 * @return string
	 */
	public function returnParsedString($string, $var_name=null, $cache_result=false)
	{
		$this->_last_filter = $this->_current_filter;

		// set the default return type
		if(strpos($this->_current_filter,'::')){ // normally, you'd want this to be !== false, but since we also dont want it to be position 0, we'll allow it
			$class_method_array = explode('::', $this->_current_filter);
			$class = $class_method_array[0];
			$method = $class_method_array[1];
		} else {
			$function = $this->_current_filter;
		}

		if($cache_result && isset($this->_cached_returns[$var_name][$this->_current_filter])){
			return $this->_cached_returns[$var_name][$this->_current_filter];
		}

		if(isset($class)){ // when calling through use of a class
			$return = $class::$method($string);
			if(!empty($var_name)){ $this->_cached_returns[$var_name][$this->_current_filter] = $return; }
			return $return;
			// when calling through use of language contructs that can't be called through variables
		} else if(in_array($function, array('isset','unset','print','empty','include','require','include_once','require_once','die','echo','exit','eval','return'))){
			if($cache_result){ die('Cannot cache language construct results. Use user defined functions and methods instead.'); }
			switch($function){
				case 'isset': $return = isset($this->_data[$var_name]); break;
				case 'empty': $return = isset($this->_data[$var_name])?empty($this->_data[$var_name]):true; break;
				case 'unset': unset($this->_data[$var_name]); break;
				case 'print': print($this->_data[$var_name]); break;
				case 'include': $return = include($this->_data[$var_name]); break;
				case 'require': $return = require($this->_data[$var_name]); break;
				case 'include_once': $return = include_once($this->_data[$var_name]); break;
				case 'require_once': $return = require_once($this->_data[$var_name]); break;
				case 'eval': die('Eval not supported by this framework. Utilizing it will confuse opcaching plugins.'); break;
				case 'die': die($this->_data[$var_name]); break;
				case 'exit': exit($this->_data[$var_name]); break;
				case 'return': return($this->_data[$var_name]); break;
			}
			if(isset($return)){ return $return; }
			return;
		} else { // normal function filtering
			if($function == ''){
				if(!empty($var_name)){ $this->_cached_returns[$var_name][$this->_current_filter] = $string; }
				return $string;
			} else {
				$return = $function($string);
				if(!empty($var_name)){ $this->_cached_returns[$var_name][$this->_current_filter] = $return; }
				return $return;
			}
		}
	}

	/**
	 * Returns original data entered into filtered object in an unfiltered array
	 * @return array
	 */
	public function getDataArrayInSqlRequestFormat() {
		$sql_format = array();
		if($this->_size_of_allowed_data > 0){
			foreach($this->_allowed_data as $key => $data_type){ $sql_format[':'.$key] = $this->get($key); }
		}else{
			echo 'Warning: This is not a mapped array so data may be missing from return. For best results, use staticly typed data objects extended from the Model class.<br>';
		}
		//		pr($sql_format);
		return $sql_format;
	}

	/**
	 * Returns original data entered into filtered object in an unfiltered array
	 * @return array
	 */
	public function toArray() { return $this->_data; }

	public function length(){ return count($this->_data); }

	public function rewind(){
		//		echo "rewinding\n";
		if(count($this->_allowed_data)){
			reset($this->_allowed_data);
		} else {
			reset($this->_data);
		}
	}

	public function current(){
		if(count($this->_allowed_data)){
			$key = key($this->_allowed_data);
		} else {
			$key = key($this->_data);
		}

		//		echo "current: $key\n";
		return isset($this->_data[$key])?$this->_data[$key]:null;
	}

	public function key(){
		if(count($this->_allowed_data)){
			$key = key($this->_allowed_data);
		} else {
			$key = key($this->_data);
		}

		//		echo "key: $key\n";
		return $key;
	}

	public function next(){
		if(count($this->_allowed_data)){
			next($this->_allowed_data);
			$var = key($this->_allowed_data);
		} else {
			next($this->_data);
			$var = key($this->_data);
		}
		//		echo "next: $var\n";
		return isset($this->_data[$var])?$this->_data[$var]:null;
	}

	public function valid(){
		if(count($this->_allowed_data)){
			$key = key($this->_allowed_data);
		} else {
			$key = key($this->_data);
		}

		$var = ($key !== NULL && $key !== FALSE);
		//		echo "valid: $var\n";
		return ($key !== NULL && $key !== false);
	}
}

/**
 * Generic Data Collection type for objects that don't have a matching collection type. Having this prevents warnings for Objects which don't need data collections
 * but from which data is queried from the db
 * @package CoreComponents
 */
class ModelCollection implements Iterator{
	private $_data_object_array = array();
	private $_collection_of_object_type = ''; // by setting this to blank, it will error if it's not set in the constructor

	// for pagination
	private $_PageData;

	public function __construct(Array $array_of_objects=null){
		if(!is_null($array_of_objects)){
			$this->addAll($array_of_objects);
		}
	}

	public function setPaginationData($start, $limit, $total){
		$this->_PageData = new PageData($start,$total,$limit);
	}

	public function getPageData(){
		return ($this->_PageData instanceof PageData)?$this->_PageData:new PageData(1,1,1);
	}

	public function addAll(Array $data_array){
		foreach($data_array as $DataObject){
			$this->addItem($DataObject);
		}
	}

	protected function setCollectionType($string){
		$this->_collection_of_object_type = $string;
	}

	public function getArrayBy($column_name){
		$new_array = array();
		foreach($this->_data_object_array as $DataObject){
			$new_array[] = $DataObject->get($column_name);
		}
		return $new_array;
	}

	public function getUniqueArrayBy($column_name){
		$new_array = array();
		foreach($this->_data_object_array as $DataObject){
			$new_array[] = $DataObject->get($column_name);
		}
		return array_unique($new_array);
	}

	public function putThisDataInAnotherCollection(DataCollection $ParentObjectCollection, $parent_column, $child_column, $object_type, $parent_object_name=null){

		if(!isset($parent_object_name)){ $parent_object_name = $object_type; }

		foreach($ParentObjectCollection as $ParentObject){
			$ParentObject->set($parent_object_name, new $object_type());
		}

		// go through childs array of results
		foreach($this->_data_object_array as $ChildObject){
			// grab the parent objects that have a match for this childs data
			$array = $ParentObjectCollection->getItemsBy($parent_column, $ChildObject->get($child_column));
				
			foreach($array as $ParentObject){
				// set the parents data with this child object, for each of  the objects found matching this child data
				$ParentObject->set($parent_object_name, $ChildObject);
			}
		}
	}

	public function addAllAt(Array $data_array, $index){
		if(!empty($this->_collection_of_object_type)){
			if($index > 0){
				$second_half = array_slice($this->_data_object_array,$index);
				$first_half = array_slice($this->_data_object_array,0,$index--);
				foreach($data_array as $DataObject){
					if($DataObject instanceof $this->_collection_of_object_type){
					} else {
						echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
					}
				}
				$this->_data_object_array = array_merge($first_half,$data_array,$second_half);
			} else {
				array_unshift($this->_data_object_array,$data_array);
			}
		} else {
			if($index > 0){
				$second_half = array_slice($this->_data_object_array,$index);
				$first_half = array_slice($this->_data_object_array,0,$index--);
				$this->_data_object_array = array_merge($first_half,$data_array,$second_half);
			} else {
				array_unshift($this->_data_object_array,$data_array);
			}
		}
	}

	public function addItem($DataObject){
		if(!empty($this->_collection_of_object_type)){
			if($DataObject instanceof $this->_collection_of_object_type){
				array_push($this->_data_object_array, $DataObject);
			} else {
				echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
			}
		} else {
			array_push($this->_data_object_array, $DataObject);
		}
	}

	public function addItemAt($DataObject, $index){
		if(!empty($this->_collection_of_object_type)){
			if($DataObject instanceof $this->_collection_of_object_type){
				if($index > 0){
					$second_half = array_slice($this->_data_object_array,$index);
					$first_half = array_slice($this->_data_object_array,0,$index--);
					$this->_data_object_array = array_merge($first_half,array($DataObject),$second_half);
				} else {
					array_unshift($this->_data_object_array,$DataObject);
				}
			} else {
				echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
			}
		} else {
			if($index > 0){
				$second_half = array_slice($this->_data_object_array,$index);
				$first_half = array_slice($this->_data_object_array,0,$index--);
				$this->_data_object_array = array_merge($first_half,array($DataObject),$second_half);
			} else {
				array_unshift($this->_data_object_array,$DataObject);
			}
		}
	}

	public function contains($Object){
		return (bool)array_search($Object, $this->_data_object_array, true);
	}

	public function getItemAt($index, $return_blank_object_if_null=false){
		return isset($this->_data_object_array[$index])
		?$this->_data_object_array[$index]
		:($return_blank_object_if_null?new DataObject():null)
		;
	}

	public function getItemIndex($Object){
		return array_search($Object, $this->_data_object_array, true);
	}

	public function getIndexBy($column_name, $data_value, $return_blank_object_if_null=false){
		foreach($this->_data_object_array as $index => $DataObject){
			if($DataObject->get($column_name) == $data_value){
				return $index;
			}
		}
		return $return_blank_object_if_null?new DataObject():null;
	}

	public function getIndicesBy($column_name, $data_value){
		$return = array();
		foreach($this->_data_object_array as $index => $DataObject){
			if($DataObject->get($column_name) == $data_value){
				$return[] = $index;
			}
		}
		return $return;
	}

	public function getItemBy($column_name, $data_value, $return_blank_object_if_null=false){
		foreach($this->_data_object_array as $DataObject){
			if($DataObject->get($column_name) == $data_value){
				return $DataObject;
			}
		}
		return $return_blank_object_if_null?new DataObject():null;
	}

	public function getItemsBy($column_name, $data_value){
		$return = array();
		foreach($this->_data_object_array as $DataObject){
			if($DataObject->get($column_name) == $data_value){
				$return[] = $DataObject;
			}
		}
		return $return;
	}

	public function removeAll(){
		$this->_data_object_array = array();
	}

	public function removeItemAt($index){
		if(isset($this->_data_object_array[$index])){
			unset($this->_data_object_array[$index]);
			return true;
		}
		return false;
	}

	public function setItemAt($DataObject, $index){
		if(!empty($this->_collection_of_object_type)){
			if($DataObject instanceof $this->_collection_of_object_type){
				$this->_data_object_array[$index] = $DataObject;
			} else {
				echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
			}
		} else {
			$this->_data_object_array[$index] = $DataObject;
		}
	}

	// recursive limiting so that object chains don't get infinite and kill the process
	public function toArrayRecursive($limit=10){
		if($limit < 0){ return array(); }

		$array_to_return = array();

		foreach($this->_data_object_array as $key => $value){
			if($value instanceof Model){
				$array_to_return[$key] = $value->toArray();
			} else if($value instanceof ModelCollection) {
				$array_to_return[$key] = $value->toArrayRecursive($limit-1);
			} else if(is_array($value)) {
				$array_to_return[$key] = $value;
			} else if(is_object($value)){
				$array_to_return[$key] = get_class_vars($value);
			} else {
				$array_to_return[$key] = $value;
			}
		}

		return $array_to_return;
	}

	public function length(){
		return count($this->_data_object_array);
	}

	public function toArray(){
		return $this->_data_object_array;
	}

	public function rewind(){
		//		echo "rewinding\n";
		reset($this->_data_object_array);
	}

	public function current(){
		$var = current($this->_data_object_array);
		//		echo "current: $var\n";
		return $var;
	}

	public function key(){
		$var = key($this->_data_object_array);
		//		echo "key: $var\n";
		return $var;
	}

	public function next(){
		$var = next($this->_data_object_array);
		//		echo "next: $var\n";
		return $var;
	}

	public function valid(){
		$key = key($this->_data_object_array);
		$var = ($key !== NULL && $key !== FALSE);
		//		echo "valid: $var\n";
		return $var;
	}
}


abstract class Request{
	protected $_command;
	protected $_return_object_type;
	protected $_map;
	protected $_mapped_data_array=array();
	protected $_mapped_data_collection;
	//	protected $_resource; // no need to store this
	protected $_result_data_array=array();
	private $_size_of_map = 0;

	public function __construct($command, $return_object_type='Model', $map=array()){
		$this->_mapped_data_collection = new ModelCollection();
		$this->_command = $command;
		$this->_return_object_type = $return_object_type;
		$this->_map = $map;
		$this->_size_of_map = count($map);
	}

	//	// should define the kind of resource that's used
	//	abstract function runAndReturnId(Array $data); // returns id
	//	abstract function runAndReturnMappedData(Array $data); // returns remapped data records for creating statically typed objects
	//	abstract function runAndReturnRawData(Array $data); // returns raw records
	//	abstract function runAndReturnAffectedRows(Array $data); // returns affected rows
	//	abstract function runAndReturnThis(Array $data); // returns affected rows

	public function rawData(){
		return $this->_result_data_array;
	}

	public function mappedDataCollection(){
		return $this->_mapped_data_collection;
	}

	public function mappedDataArray(){
		return $this->_mapped_data_array;
	}

	protected function mapResult($raw_data){
		//		pr($raw_data);
		// not particularly happy about duplicating data for the sake of returning it in two formats. must find a better way to do this
		$this->_result_data_array[] = $raw_data;
		// this is where the mapping needs to happen
		if($this->_size_of_map > 0){
			$mapped_data = array();
			foreach($raw_data as $old_key => $data){
				if(array_key_exists($old_key,$this->_map)){
					// create the keyed data in a clean array
					$mapped_data[$this->_map[$old_key]] = $data;
					// remove the original data from the results so when merged into the remapped result, it wont have duplicate data in 2 keys
					unset($raw_data[$old_key]);
				}
			}
			$mapped_data = array_merge($mapped_data,$raw_data);
		} else {
			$mapped_data = $raw_data;
		}
		//		pr($mapped_data);
		return new $this->_return_object_type($mapped_data);
	}

	// resets the data in this request handler to null / default, so new data doesn't add to old data for the next query
	public function reconstruct($return_object_type='Model', $map=array()){
		$this->_return_object_type = $return_object_type;
		$this->_map = $map;
		$this->_size_of_map = count($map);
		$this->_mapped_data_array = array();
		$this->_result_data_array = array();
		$this->_mapped_data_collection = new ModelCollection();
	}
}

class SQLiteRequest extends Request{
	public function send(Array $data){

		return $this;
	}
}


class SQLiteConnection extends SQLConnection{

	/**
	 * Open a connection to your MySQL 4.1+ database
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @param bool $autocommit
	 */
	public function __construct($connection_name, $host, $user, $pass, $db='', $port='', $autocommit=true)
	{
		$this->open($host, $user, $pass, $db, $port, $autocommit);
	}

	public function __destruct()
	{
		self::close();
	}

	/**
	 * Opens a new connection to your MySQL 4.1+ database
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @param bool $autocommit
	 * @return resource
	 */
	public function open($host, $user, $pass, $db, $port, $autocommit)
	{
		$this->db_handle = new PDO('sqlite:../db/sqlite.db');
		$this->db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if($this->db_handle === false) { and_die('Error on logon:'. mysqli_connect_error()); }
		return $this->db_handle;
	}

	/**
	 * Close connection to your MySQL 4.1+ database
	 * @return bool
	 */
	public function close()
	{
		//		if(is_resource($this->query_result)) { @mysqli_free_result($this->query_result); }
		//		@mysqli_close($this->db_handle);
		return true;
	}

	/**
	 * Run a query and store the results in an array
	 * @param string $query
	 * @return bool
	 */
	public function query($query, $line, $file)
	{
		$query=trim($query);
		if(empty($query)) { return false; }

		$location = ' on line '.$line.' of file '.$file;
		// Grab the first word from a query which is assumed to be the query type (ie select insert delete update etc...)
		$explosion=explode(' ', strtolower(trim(str_replace('(','',$query))));
		$this->last_type=array_shift($explosion);

		$this->row_data=array();
		try {
			$time_start = microtime(true);
			$result=$this->db_handle->query($query);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$this->queries[]=$query.(!empty($location)?"\n# ".$location.' took '.round($time,4).' seconds at '.date(DATE_RFC822).'.':'');
			if(round($time,4) > 3) { $this->slow_queries[]=$query.(!empty($location)?"\n# ".$location.' took '.round($time,4).' seconds at '.date(DATE_RFC822).'.':''); }
			$this->statement=$result;
			$this->rows=$this->statement->rowCount();
			$this->next_row_number=0;
		} catch (PDOException $e) {
			pr($e);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$this->bad_queries[]=$query.'; #'.$this->db_handle->errorCode().(!empty($location)?"\n# ".$location.' took '.round($time,4).' seconds at '.date(DATE_RFC822).'.':'');
			return false;
		}

		return true;
	}

	/**
	 * Prepare an sql statement for execution
	 * @param string $query
	 */
	public function prepare($query, $line, $file)
	{
		$location = ' on line '.$line.' of file '.$file;
		// Grab the first word from a query which is assumed to be the query type (ie select insert delete update etc...)
		$explosion=explode(' ', strtolower(trim(str_replace('(','',$query))));
		$this->last_type=array_shift($explosion);
		$time_start = microtime(true);
		try {
			$this->statement=$this->db_handle->prepare($query);
		} catch (PDOException $e) {
			$this->bad_queries[]=$query.'; #'.$e->getMessage().(!empty($location)?"\n# ".$location.' at '.date(DATE_RFC822).'.':'');
			//			echo $query;
			return false;
		}

		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->queries[]=$query.(!empty($location)?"\n# ".$location.' took '.round($time,4).' seconds at '.date(DATE_RFC822).'.':'');

		return true;
	}

	/**
	 * Execute a previously prepared sql statement
	 * @param array $values
	 */
	public function execute(Array $values, Array $integers=array()) // the integers part is a compatiblity fix for this bug: http://bugs.php.net/bug.php?id=40740
	{
		// loop must iterate this way because bind creates a pointer to the memory and a foreach that creates key and value vars reuses the same memory space for the value
		$keys = array_keys($values);
		try {
			foreach($keys as $key){
				if(lower($values[$key]) === 'null' || !isset($values[$key])){
					//				echo 'Null: '.$key.'='.$values[$key]."\n<br>";
					$this->statement->bindParam($key, $values[$key], PDO::PARAM_NULL);
				} else if(in_array($key, $integers)){
					//				echo 'Int: '.$key.'='.$values[$key]."\n<br>";
					$values[$key] = intval($values[$key]);
					$this->statement->bindParam($key, $values[$key], PDO::PARAM_INT);
				} else {
					//				echo 'String: '.$key.'='.$values[$key]."\n<br>";
					$this->statement->bindParam($key, $values[$key]);
				}
			}
		} catch (PDOException $e) {
			pr($e);
			$location = null;
			$this->bad_queries[]=current($this->queries).'; #'.$e->getMessage().(!empty($location)?"\n# ".$location.' at '.date(DATE_RFC822).'.':'');
			//			echo $query;
			return false;
		}

		$time_start = microtime(true);
		try {
			// run it
			$this->statement->execute(); // $values
		} catch (PDOException $e) {
			pr($e);
			$location = null;
			$this->bad_queries[]=current($this->queries).'; #'.$e->getMessage().(!empty($location)?"\n# ".$location.' at '.date(DATE_RFC822).'.':'');
			//			echo $query;
			return false;
		}
		// clock it
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$location = null;
		if(round($time,4) > 3) { $this->slow_queries[]=current($this->queries).(!empty($location)?"\n# ".$location.' took '.round($time,4).' seconds at '.date(DATE_RFC822).'.':''); }

		$this->row_data=array();
		$this->next_row_number=0;
		$this->rows=$this->statement->rowCount();
		return true;
	}

	/**
	 * Terminate the last prepared sql statement
	 * @param array $values
	 */
	public function terminate() { /*mysqli_stmt_close($this->statement);*/ }

	/**
	 * Read one row of data from the results of the last query
	 * @return bool
	 */
	public function readRow($return_type='ASSOC')
	{
		// predicts if there will be a row here or not.
		// if not it wont run the function which will throw the error which will piss off php
		if($this->next_row_number < $this->rows)
		{
			//			echo 'fetch row';
			switch($return_type)
			{
				case 'BOTH' : $this->row_data=$this->statement->fetch(PDO::FETCH_BOTH); break;
				case 'NUM' : $this->row_data=$this->statement->fetch(PDO::FETCH_NUM); break;
				default : $this->row_data=$this->statement->fetch(PDO::FETCH_ASSOC); break;
			}
			$this->next_row_number = $this->next_row_number+1;
			return true;
		}
		else { return false; }
	}

	public function read($return_type='ASSOC')
	{
		//		echo 'fetch all';
		switch($return_type)
		{
			case 'BOTH' : $this->row_data=$this->statement->fetchAll(PDO::FETCH_BOTH); break;
			case 'NUM' : $this->row_data=$this->statement->fetchAll(PDO::FETCH_NUM); break;
			default : $this->row_data=$this->statement->fetchAll(PDO::FETCH_ASSOC); break;
		}
		$this->rows = $this->next_row_number = count($this->row_data);
		return $this->row_data;
	}

	/**
	 * Commit queries on current transaction
	 * @return bool
	 */
	public function commit() { return $this->db_handle->commit($this->db_handle); }

	/**
	 * Undo the queries in the transaction
	 * @return bool
	 */
	public function rollback() { return $this->db_handle->rollback($this->db_handle); }

	/**
	 * Escape strings to prepare them for insert.
	 */
	final public function escape($data) { return $this->db_handle->quote($data); }

	/**
	 * Return the number of affected rows
	 */
	final public function affectedRows() { return $this->statement->rowCount(); }

	/**
	 * Return the last inserted id
	 */
	final public function insertId() { return $this->db_handle->lastInsertId(); }

}


/**
 * This library filters data for safe sql execution
 * @package Sanitize
 */
final class Filter
{
	/**
	 * Force the string entered into a var of type bool
	 * @param int $str
	 * @return int
	 */
	public static function boolean($str)
	{
		if(!isset($str) || empty($str)) { return 'NULL'; }
		return '1';
	}

	/**
	 * Force the string entered into a var of type int within the span declared
	 * also can return NULL if $str is not set
	 * @param int $str
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function integer($str, $a_min=null, $a_max=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		// doesn't this defeat the point of an int filter?
		// it's an int filter, not an int typecasting. (int) or intval() will typecast. db fields need to be able to be null so they can be unset and filters are for db interaction
		if(!isset($str))	{ return null; }

		if(isset($a_min))	{ $min = $a_min; }
		else				{ $min = $SetReg->getCurrent('min'); }

		if(isset($a_max))	{ $max = $a_max; }
		else				{ $max = $SetReg->getCurrent('max'); }

		$int = (int)$str;
		if(isset($min) && $int < $min)		{ return (int)$min; }
		else if(isset($max) && $int > $max)	{ return (int)$max; }
		return $int;
	}

	/**
	 * Force the string entered into a var of type int within the span declared
	 * Like Filter::integer, but always returns type int, returns default if set and $str is not
	 * @param int $str
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function range($str, $a_min=-2147483648, $a_max=2147483647, $default=null) //32 bit values
	{
		$SetReg = RuntimeInfo::instance()->settings();

		if(isset($a_min))	{ $min = $a_min; }
		else				{ $min = -2147483648; }

		if(isset($a_max))	{ $max = $a_max; }
		else				{ $max = 2147483647; }

		if(isset($default) && !isset($str))	{ $int= (int)$default; }
		else 									{ $int= (int)$str; }

		$min= (int)$min;
		$max= (int)$max;
		if($int < $min)		{ return $min; }
		elseif($int > $max)	{ return $max; }
		return $int;
	}

	/**
	 * Force the string entered into a string of a certain length
	 * @param string $str
	 * @param int $max
	 * @return string
	 */
	public static function string($str, $a_max=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		$db = RuntimeInfo::instance()->mysql();

		if(empty($str)) { return ''; }
		$str=strval($str);
		$max = $SetReg->getCurrent('max');
		if(isset($a_max)) { $max = $a_max; }

		if(isset($max)) { $str = substr($str,0,$max); }

		// seems_utf8 checks to see if its already utf8 encoded, and if it is dont reencode it
		// utf8_encode takes all non utf8 characters and encodes them to utf8 for storage in the db
		//		return $db->escape(seems_utf8($str)?$str:utf8_encode($str));
		return $db->escape($str);
	}

	/**
	 * Filter strings to zipcode format
	 * @param string $code
	 * @param string $country
	 * @return string
	 */
	public static function zipcode($code, $a_country=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		if(!isset($code)) { return ''; }
		$country = $SetReg->getCurrent('country');
		if(isset($a_country)) { $country = $a_country; }

		$original_code = $code;
		$numeric_code = (int)$code;

		if(strtolower($country) == 'us' && $numeric_code==substr($original_code, 0, 6)) { $code = substr(preg_replace('/\D/','', $original_code),0,5); }
		else if(strtolower($country) == 'ca') { $code = preg_replace('/[^A-z0-9 ]/','', strtoupper(substr($original_code, 0, 3).' '.substr($original_code, -3))); }
		return $code;
	}

	/**
	 * Filter strings to country abbreviations and returns them in all caps
	 * @param string $str
	 * @return string
	 */
	public static function abbreviation($str)
	{
		if(empty($str)) { return ''; }
		return strtoupper(substr(preg_replace('/[^a-zA-Z]+/','',$str),0,2));
	}

	/**
	 * Filters the raw text path to an image or file for articles and such.
	 * @param string $str
	 * @return string
	 */
	public static function filename($str)
	{
		if(!isset($str)) { return ''; }
		$str = preg_replace('/[?*<>|]/','', $str);
		return $str;
	}

	/**
	 * Filter text inclusive of tags into a plain text single line format of readable length using an
	 * alternate text body incase its too short
	 * @param string $str
	 * @param string $body
	 * @return string
	 */
	public static function title($str,$body=null,$max_length=200)
	{
		// Strip whitespace
		$search=array("\t","\n","\r","\x0B","\0", '  ');
		$plain_string=str_replace($search,' ',$str);

		if(isset($body) && strlen($plain_string) < 3)	{ $plain_string=str_replace($search,' ',trim($body)); }
		elseif(strlen($plain_string) < 3) 				{ return 'Titles must be at least 3 characters long'; }

		if(strlen($plain_string) > $max_length) { $str=substr($plain_string,0,$max_length); }
		// Clean out all urls and tags and return only escaped text
		$str = preg_replace(array('/\[.*?\]/','/http:\/\/.*? /','/  +/'),array(' ',' ',' '), $plain_string);
		return trim(addslashes($str));
	}

	/**
	 * Filter text inclusive of tags into a plain text single line format of readable length
	 * @param string $str
	 * @return string
	 */
	public static function text($str,$max_length=200)
	{
		// Strip whitespace
		$search=array("\t","\n","\r","\x0B","\0", '  ');
		$plain_string=str_replace($search,' ',$str);

		// If the text is too damn small, return nothing
		//		if(strlen($plain_string) < 5) { return ''; }		//why not?

		if(strlen($plain_string) > $max_length) { $str=substr($str,0,$max_length); }
		// Clean out all urls and tags and return only escaped text
		$str = preg_replace(array('/\[.*?\]/','/http:\/\/.*? /','/  +/'),array(' ',' ',' '), $plain_string);
		return addslashes(trim($str));
	}

	/**
	 * Filter text into only letters, numbers, and underscores to prevent sql injection attacks
	 * @param array $string
	 * @return string
	 */
	public static function column($str)
	{
		if(!isset($str)) { return ''; }
		return preg_replace('/([^a-zA-Z0-9_\-])/s','',$str);
	}

	/**
	 * Filter an array of strings into a WHERE blah IN ready format
	 * @param array $array
	 * @return string
	 */
	public static function strings($array)
	{
		$array=array_unique($array);
		sort($array);
		$array=array_filter($array,'Filter::string');
		$string=implode('","',$array);
		return '("'.$string.'")';
	}

	/**
	 * Filter an array of ints into a WHERE blah IN ready format
	 * @param array $array
	 * @return string
	 */
	public static function integers($array)
	{
		if(!is_array($array)) { $array=array($array); }

		$array=array_unique($array);
		sort($array);
		$array=array_filter($array,'intval');
		$string=implode('","',$array);
		return '("'.$string.'")';
	}
}

function table_exists(SQLiteConnection $db, $table_name){
	$db->prepare('SELECT name FROM sqlite_master WHERE type="table" AND name=:table_name;',__LINE__, __FILE__);
	$db->execute(array(':table_name'=>$table_name));
	
	$db->read();
	
	return (count($db->row_data) > 0);
}

class DataObject extends Model{}
class DataCollection extends ModelCollection{}

function record_visit(SQLiteConnection $db){
	
// 	echo 'Record the visit?<br>'; 
	
	if( !(table_exists($db, 'visitor') && table_exists($db, 'visit')) ){ return 0; }
	
// 	echo 'Record the visit!<br>';
// 	echo 'c:'._c('visitor_id').'<br>';
	
	$db->prepare('SELECT id FROM visitor WHERE id=:id;', __LINE__, __FILE__);
	$db->execute(array(':id'=>_c('visitor_id')));
	$db->read();
	
// 	echo 'Cookie, why are you misbehaving?';
// 	pr($_COOKIE);
// 	pr($db->row_data);
	
	if(!isset($db->row_data[0]['id'])){
		
		//echo 'insert at hit 1';
		
		$db->prepare('
		INSERT OR IGNORE INTO visitor
		(
			ip_guess,
			HTTP_CLIENT_IP,
			HTTP_X_FORWARDED_FOR,
			REMOTE_ADDR,
			user_agent,
			visits
		) VALUES (
			:ip_guess,
			:HTTP_CLIENT_IP,
			:HTTP_X_FORWARDED_FOR,
			:REMOTE_ADDR,
			:user_agent,
			1
		);', __LINE__, __FILE__);
		$db->execute(array(
			':ip_guess' => guess_ip(),
			':HTTP_CLIENT_IP' => _s('HTTP_CLIENT_IP'),
			':HTTP_X_FORWARDED_FOR' => _s('HTTP_X_FORWARDED_FOR'),
			':REMOTE_ADDR' => _s('REMOTE_ADDR'),
			':user_agent' => _s('HTTP_USER_AGENT')
		));
		
		$visitor_id = $db->insertId();
		
		//echo 'visitor id is: '.$visitor_id.'<br>';
		
		if($visitor_id > 0 && intval(_c('visitor_id')) == 0){
			setcookie('visitor_id', $visitor_id, time()+60*60*24*30, '/');
		} else if(intval(_c('visitor_id')) == 0) {
			
// 			echo 'Cookie, why are you empty?';
// 			pr($_COOKIE);
			
			// probably a bot. make a note of it
			$db->prepare('SELECT id FROM visitor WHERE ip_guess=:ip_guess AND created_date = CURRENT_DATE;', __LINE__, __FILE__);
			$db->execute(array(':ip_guess'=>guess_ip()));
			$db->read();
			
			$visitor_id = isset($db->row_data[0]['id'])?$db->row_data[0]['id']:0;
			if($visitor_id > 0){
				// try setting it again
				setcookie('visitor_id', $visitor_id, time()+60*60*24*30, '/');
				
				$db->prepare('
				UPDATE visitor SET
					ip_guess = :ip_guess,
					HTTP_CLIENT_IP = :HTTP_CLIENT_IP,
					HTTP_X_FORWARDED_FOR = :HTTP_X_FORWARDED_FOR,
					REMOTE_ADDR = :REMOTE_ADDR,
					user_agent = :user_agent,
					visits = visits + 1
				WHERE
					id = :id
				;', __LINE__, __FILE__);
				
				$db->execute(array(
					':id' => _c('visitor_id'),
					':ip_guess' => guess_ip(),
					':HTTP_CLIENT_IP' => _s('HTTP_CLIENT_IP'),
					':HTTP_X_FORWARDED_FOR' => _s('HTTP_X_FORWARDED_FOR'),
					':user_agent' => _s('HTTP_USER_AGENT'),
					':REMOTE_ADDR' => _s('REMOTE_ADDR')
				));
			} else {
				// probably a bot
				return 0;
			}
		}
	} else {
		// keeping the db up to date with the users cookie. What could possibly go wrong, right? everything, but how is that relevant to this make believe site.
// 		echo 'Cookie, why are you misbehaving?';
// 		pr($_COOKIE);
		
		$db->prepare('
		UPDATE visitor SET
			ip_guess = :ip_guess,
			HTTP_CLIENT_IP = :HTTP_CLIENT_IP,
			HTTP_X_FORWARDED_FOR = :HTTP_X_FORWARDED_FOR,
			REMOTE_ADDR = :REMOTE_ADDR,
			user_agent = :user_agent,
			visits = visits + 1
		WHERE
			id = :id
		;', __LINE__, __FILE__);
		
		$db->execute(array(
			':id' => _c('visitor_id'),
			':ip_guess' => guess_ip(),
			':HTTP_CLIENT_IP' => _s('HTTP_CLIENT_IP'),
			':HTTP_X_FORWARDED_FOR' => _s('HTTP_X_FORWARDED_FOR'),
			':user_agent' => _s('HTTP_USER_AGENT'),
			':REMOTE_ADDR' => _s('REMOTE_ADDR')
		));
		$visitor_id = _c('visitor_id');
	}
	
	$db->query('SELECT * FROM visitor', __LINE__, __FILE__);
	$db->read();
// 	pr($db->row_data);
	
// 	pr($visitor_id);
	
	$db->prepare('
	INSERT INTO visit (
		visitor_id,
		ip_guess,
		HTTP_CLIENT_IP,
		HTTP_X_FORWARDED_FOR,
		REMOTE_ADDR,
		user_agent
	) VALUES (
		:visitor_id,
		:ip_guess,
		:HTTP_CLIENT_IP,
		:HTTP_X_FORWARDED_FOR,
		:REMOTE_ADDR,
		:user_agent
	)', __LINE__, __FILE__);
	
	$db->execute(array(
		':visitor_id' => $visitor_id,
		':ip_guess' => guess_ip(),
		':HTTP_CLIENT_IP' => _s('HTTP_CLIENT_IP'),
		':HTTP_X_FORWARDED_FOR' => _s('HTTP_X_FORWARDED_FOR'),
		':user_agent' => _s('HTTP_USER_AGENT'),
		':REMOTE_ADDR' => _s('REMOTE_ADDR')
	));
	
	$db->query('SELECT * FROM visit', __LINE__, __FILE__);
	$db->read();
// 	pr($db->row_data);
	
	return $visitor_id;
}

function get_db_instance(){
	$db = new SQLiteConnection('default', null, null, null);
	
	if(!table_exists($db, 'visitor')){
		$results = $db->query(
			'CREATE TABLE "visitor" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT NOT NULL, "visits" INTEGER NOT NULL  DEFAULT 0, "ip_guess" VARCHAR UNIQUE, "HTTP_CLIENT_IP" VARCHAR, "HTTP_X_FORWARDED_FOR" VARCHAR, "REMOTE_ADDR" VARCHAR, "user_agent" VARCHAR, "created_date" DATETIME DEFAULT CURRENT_DATE);',
			__LINE__,
			__FILE__
		);
	}
	
	if(!table_exists($db, 'visit')){
		$results = $db->query(
			'CREATE TABLE "visit" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "visitor_id" INTEGER NOT NULL , "ip_guess" VARCHAR, "HTTP_CLIENT_IP" VARCHAR, "HTTP_X_FORWARDED_FOR" VARCHAR, "REMOTE_ADDR" VARCHAR, "user_agent" VARCHAR, "created_datetime" DATETIME DEFAULT CURRENT_TIMESTAMP);',
			__LINE__,
			__FILE__
		);
	}
	
	if(!table_exists($db, 'content_hit')){
		$results = $db->query(
			'CREATE TABLE "content_hit" ("hit_date" DATETIME DEFAULT CURRENT_DATE , "content_id" INTEGER NOT NULL , "hits" INTEGER, PRIMARY KEY ("hit_date", "content_id"));',
			__LINE__,
			__FILE__
		);
	}
	
	if(!table_exists($db, 'content')){
		$results = $db->query(
			'CREATE TABLE "content" ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , "content_type_id" INTEGER NOT NULL DEFAULT 1, "title" VARCHAR NOT NULL, "content_path" VARCHAR , "out_link" VARCHAR , "hits" INTEGER NOT NULL DEFAULT 0, "is_affiliate_link" BOOL NOT NULL DEFAULT FALSE, "is_active" BOOL NOT NULL DEFAULT TRUE, "created_date" DATETIME NOT NULL  DEFAULT CURRENT_DATE);',
			__LINE__,
			__FILE__
		);
	}
	
	if(!table_exists($db, 'content_type')){
		$results = $db->query(
			'CREATE TABLE "content_type" ("id" INTEGER PRIMARY KEY  NOT NULL, "title" VARCHAR NOT NULL, "created_date" DATETIME NOT NULL  DEFAULT CURRENT_DATE);',
			__LINE__,
			__FILE__
		);
	}
	return $db;
}