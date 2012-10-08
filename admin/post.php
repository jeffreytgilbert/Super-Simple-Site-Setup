<?php

ini_set('memory_limit', '1024M');//
ini_set('max_execution_time', '6000');//
set_time_limit(6000);

include('../source/common.php');

$db = get_db_instance();

// record_visit($db);

if(isset($_POST['Content']) && is_array($_POST['Content'])){
	$Content = new Model(_p('Content'));
	if($Content->length() > 0){

		$db->prepare('
		INSERT INTO content (
			title,
			content_type_id,
			is_affiliate_link
		) VALUES (
			:title,
			:content_type_id,
			:is_affiliate_link
		)', __LINE__, __FILE__);
		
		$db->execute(array(
				':title' => $Content->get('title'),
				':content_type_id' => $Content->get('content_type_id'),
				':is_affiliate_link' => (bool)$Content->get('is_affiliate_link')
		));
		
		$item_id = $db->insertId();
		
		$db->prepare('
			UPDATE content 
			SET content_path = :content_path
			WHERE id=:id
		', __LINE__, __FILE__);
		
		switch($Content->get('content_type_id')){
			
			// image uploads
			case '1':
				foreach($_FILES as $file){
					
					$ImageGD = ImageFile::create($file['tmp_name']['file_upload']);
					if($ImageGD !== false){
					
						$image_type = exif_imagetype($file['tmp_name']['file_upload']);
						if(in($image_type,array(1,2,3))){
							
							switch($image_type){
								case 1: $image_ext = 'gif';
								case 2: $image_ext = 'jpg';
								case 3: $image_ext = 'png';
							}
							
							$img_info = getimagesize($file['tmp_name']['file_upload']);
							
							$destination = os_path('../img/content/pictures/'.$item_id.'.'.$image_ext);
							
							move_uploaded_file($file['tmp_name']['file_upload'], $destination);
						}
					}
					
				}
				
				$db->execute(array(
					':id' => $item_id,
					':content_path' => $item_id.'.'.$image_ext
				));
				
				break;
				
			// animated gifs 
			case '2':
				foreach($_FILES as $file){
					$img_info = getimagesize($file['tmp_name']['file_upload']);
					$destination = os_path('../img/content/gifs/'.$item_id.'.gif');
					move_uploaded_file($file['tmp_name']['file_upload'], $destination);
				}

				$db->execute(array(
					':id' => $item_id,
					':content_path' => $item_id.'.gif'
				));
				break;
				
			// videos
			case '3':
// 				if (!extension_loaded('ffmpeg') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'ffmpeg.so' : 'php_ffmpeg.dll'))
// 					exit("\nERROR: FFMPEG extension not loaded\n\n");
				
				foreach($_FILES as $file){
						
					$movie = new ffmpeg_movie($file['tmp_name']['file_upload']);
					$movie->getDuration(); // Gets the duration in secs.
					$movie->getVideoCodec(); // What type of compression/codec used
					
					// movie width/height
					$width = $movie->getFrameWidth();
					$height = $movie->getFrameHeight();
					// correction calculations
					$width = gettype($width/2) == "integer"?$width:($width-1);
					$height = gettype($height/2) == "integer"?$height:($height-1);

					$fps = $movie->getFrameRate();
					$audio_bit_rate = intval($movie->getAudioBitRate()/1000);
					$video_bit_rate = $movie->getBitRate();
					$sample_rate = $movie->getAudioSampleRate();
					
					$here = dirname(__FILE__);
					$destination = os_path($here.'/../img/content/videos/'.$item_id.'.mp4');
					$original = os_path($here.'/../img/content/videos/'.$file['name']['file_upload']);
					
					move_uploaded_file($file['tmp_name']['file_upload'], $original);
					
					//echo "ffmpeg -i ".$original." -deinterlace -an -ss 1 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg ".$here."/../img/content/videos/thumbs/".$item_id.".jpg 2>&1<br>";
					shell_exec('ffmpeg -i '.$original.' -deinterlace -an -ss 1 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg '.$here.'/../img/content/videos/thumbs/'.$item_id.'.jpg 2>&1');
					//echo '/usr/bin/ffmpeg -i '.$original.' -acodec libfaac -ab '.$audio_bit_rate.'k -vcodec libx264 -vpre slow -b '.$video_bit_rate.'k -r '.$fps.' -threads 0 '.$destination.' 2>&1<br>';
					shell_exec('/usr/bin/ffmpeg -i '.$original.' -acodec libfaac -ab '.$audio_bit_rate.'k -vcodec libx264 -vpre slow -b '.$video_bit_rate.'k -r '.$fps.' -threads 0 '.$destination.' 2>&1');

					// dunno if this is done running here or if its a bg process
				}

				$db->execute(array(
					':id' => $item_id,
					':content_path' => $item_id.'.mp4'
				));
				
				unlink($original);
				
				break;
				
// 			// video streams
// 			case '4':

// 				$db->execute(array(
// 					':id' => $item_id,
// 					':content_path' => $Content->get('content_path')
// 				));
				
// 				break;
				
			// url
			case '5': 
				
				$db->prepare('
					UPDATE content
					SET content_path = :content_path, out_link = :out_link
					WHERE id=:id
				', __LINE__, __FILE__);
				
				foreach($_FILES as $file){
						
					$ImageGD = ImageFile::create($file['tmp_name']['file_upload']);
					if($ImageGD !== false){
							
						$image_type = exif_imagetype($file['tmp_name']['file_upload']);
						if(in($image_type,array(1,2,3))){
								
							switch($image_type){
								case 1: $image_ext = 'gif';
								case 2: $image_ext = 'jpg';
								case 3: $image_ext = 'png';
							}
								
							$img_info = getimagesize($file['tmp_name']['file_upload']);
								
							$destination = os_path('../img/content/pictures/'.$item_id.'.'.$image_ext);
								
							move_uploaded_file($file['tmp_name']['file_upload'], $destination);
						}
					}
						
				}
				
				$db->execute(array(
					':id' => $item_id,
					':content_path' => $item_id.'.'.$image_ext,
					':out_link' => $Content->get('out_link')
				));
				
				break;
				
			// embeds
			case '6':
				$destination = os_path('../img/content/embeds/'.$item_id.'.htm');
				
				$data = $Content->get('embed');
				$data = str_replace('<?', '&lt;?', $data);
				$data = str_replace('<%', '&lt;%', $data);
				$data = str_replace('?>', '?&gt;', $data);
				$data = str_replace('%>', '%&gt;', $data);
				
				file_put_contents($destination, $data);
				
				$db->execute(array(
					':id' => $item_id,
					':content_path' => $item_id.'.htm'
				));
				
				break;
		}
		
		header('Location: /post.php');
		
	}
	
} else {
	$Content = new Model();
}

$db->query('
	SELECT
		*
	FROM content
	ORDER BY hits DESC
	LIMIT 100',
		__LINE__,
		__FILE__
);

$db->read();
$posts = $db->row_data;

?>

<html>

<head>
<title>Super Simple Site Setup</title>
<link href='/css/common.css' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Allura' rel='stylesheet' type='text/css'>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>
function show_inputs(){
	switch($('#type_selector').val()){
		case '1':
			$('#content_file_upload').show();
		break;
		case '2':
			$('#content_file_upload').show();
		break;
		case '3':
			$('#content_file_upload').show();
		break;
//		case '4':
//			$('#content_url').show();
//		break;
		case '5':
			$('#content_file_upload').show();
			$('#content_url').show();
		break;
		case '6':
			$('#content_embed').show();
		break;
	}
}

$(document).ready(function(){
	$('#content_url').hide();
	$('#content_file_upload').hide();
	$('#content_embed').hide();

	$('#type_selector').change(function(){
		$('#content_url').hide();
		$('#content_file_upload').hide();
		$('#content_embed').hide();
		show_inputs();
	});
	show_inputs();
});
</script>
</head>
<body>

<div>
	<div id="header">
		<h1 data-title="Super Simple Site Setup&nbsp;"><a href="/">Super Simple Site Setup&nbsp;</a></h1>
	</div>

	<div id="frame">
		<div id="nav">
		
<?php include('../source/menus/admin.php'); ?>
			
		</div>
		
		<div><br clear="all"/></div>
		
		<div id="contents">
			<div id="data_form">
				<form action="?" method="post" enctype="multipart/form-data" name="ContentForm">
				  <table style="width:400px;">
				    <tr>
				      <td>Content Type</td>
				      <td><select name="Content[content_type_id]" id="type_selector">
				      	<option value="1"<?php echo ($Content->get('content_type_id') == 1?' selected="selected"':'') ?>>Picture</option>
		      	      	<option value="2"<?php echo ($Content->get('content_type_id') == 2?' selected="selected"':'') ?>>Animated GIF</option>
				      	<option value="3"<?php echo ($Content->get('content_type_id') == 3?' selected="selected"':'') ?>>Video File</option>
<!--			      	<option value="4"<?php echo ($Content->get('content_type_id') == 4?' selected="selected"':'') ?>>Video Stream</option> -->
				      	<option value="5"<?php echo ($Content->get('content_type_id') == 5?' selected="selected"':'') ?>>Ad Link</option>
				      	<option value="6"<?php echo ($Content->get('content_type_id') == 6?' selected="selected"':'') ?>>Embeds</option>
				      	</select></td>
				    </tr>
				    <tr>
				      <td>Title</td>
				      <td><input type="text" name="Content[title]" value="<?php echo $Content->getData('title') ?>"></td>
				    </tr>
				    <tr>
				      <td>Is this a paid ad?</td>
				      <td><input type="checkbox" name="Content[is_affiliate_link]"<?php echo ($Content->boolean('is_affiliate_link') == true?' checked="checked"':'') ?>></td>
				    </tr>
				    <tr id="content_file_upload">
				      <td>Image</td>
				      <td><input type="file" name="Content[file_upload]"></td>
				    </tr>
				    <tr id="content_url">
				      <td>Ad Link</td>
				      <td><input type="text" name="Content[out_link]" value="<?php echo $Content->getData('out_link') ?>"></td>
				    </tr>
				    <tr id="content_embed">
				      <td>&nbsp;</td>
				      <td><textarea name="Content[embed]"><?php echo $Content->getData('embed') ?></textarea></td>
				    </tr>
				    <tr>
				      <td>&nbsp;</td>
				      <td><input type="submit" name="Content[submit]" value="Submit"></td>
				    </tr>
				  </table>
				</form>
			</div>
			
			<div><br clear="all"/></div>

			<div id="recent_posts">
				<h2>Recent Posts</h2>
				<div class="other_thumbs">
					<?php 
					
					foreach($posts as $post){
					
						switch($post['content_type_id']){
							case 1: // photos
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/thumbnail.php?path=<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
									</a>
									<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
								</div>
								<?php 
								break;
							case 2: // gifs
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/img/content/gifs/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
									</a>
									<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
								</div>
								<?php 
								break;
							case 3: // video
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/img/content/videos/thumbs/<?php echo $post['id'] ?>.jpg" alt="<?php echo $post['title'] ?>">
									</a>
									<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
								</div>
								<?php 
								break;
/*							case 4: // video stream
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
									</a>
									<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
								</div>
								<?php 
								break; */
							case 5: // url
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/out.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/thumbnail.php?path=<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
									</a>
									<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
								</div>
								<?php 
								break;
							case 6: // embed
								?>
								<div class="thumb_container">
									<?php include('../img/content/embeds/'.$post['content_path']) ?>
									<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
								</div>
								<?php 
								break;
								
						}
					
					}
					
					?>
				</div>
				<div><br clear="all"/></div>
			</div>
			<div><br clear="all"/></div>
		</div>
		<div><br clear="all"/></div>
	</div>
	<div><br clear="all"/></div>
</div>
<div><br clear="all"/></div>		
</body>
</html>
