<?php

include('../source/common.php');

$db = get_db_instance();

if(isset($_POST['Yes'])){
	
	$embeds = File::filesInFolderToArray('../img/content/embeds');
	$gifs = File::filesInFolderToArray('../img/content/gifs');
	$pictures = File::filesInFolderToArray('../img/content/pictures');
	$tmp = File::filesInFolderToArray('../img/content/tmp');
	$videos = File::filesInFolderToArray('../img/content/videos');
	$video_thumbs = File::filesInFolderToArray('../img/content/videos/thumbs');
	$cache = File::filesInFolderToArray('../cache/220x160');
	
	$files_to_delete = $embeds + $gifs + $pictures + $tmp + $videos + $video_thumbs + $cache;
	foreach($files_to_delete as $path => $name){
		// echo 'Deleting '.$path.'<br>';
		unlink($path);
	}
	
	$results = $db->query(
		'DROP TABLE "visitor";',
		__LINE__,
		__FILE__
	);
	$results = $db->query(
		'DROP TABLE "visit";',
		__LINE__,
		__FILE__
	);
	$results = $db->query(
		'DROP TABLE "content_hit";',
		__LINE__,
		__FILE__
	);
	$results = $db->query(
		'DROP TABLE "content";',
		__LINE__,
		__FILE__
	);
	$results = $db->query(
		'DROP TABLE "content_type";',
		__LINE__,
		__FILE__
	);
	header('Location: /create_db.php');
	
}

?>

<html>

<head>
<title>Super Simple Site Setup</title>
<link href='/css/common.css' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Allura' rel='stylesheet' type='text/css'>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

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
			This form will delete the db file that contains all the accumulated data from the site. This is not recommended if you already have production content on the site.
			<div id="data_form">
				Are you sure?
				<form action="?" method="post">
				      <input type="submit" name="Yes" value="Submit">
				</form>
			</div>
		</div>
	</div>
</div>
		
</body>
</html>
