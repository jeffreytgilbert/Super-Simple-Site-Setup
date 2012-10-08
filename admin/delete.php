<?php

include('../source/common.php');

$db = get_db_instance();

if(_p('id')){
	
	$db->query('
		SELECT
			*
		FROM content
		WHERE id='.(int)_p('id'),
		__LINE__,
		__FILE__
	);
	
	$db->read();
	$posts = $db->row_data;
	if(count($posts) == 0){ header('Location: 404'); }
	$post = array_pop($posts);
	
	switch($post['content_type_id']){
		case '1':
			@unlink('../img/content/pictures/'.$post['content_path']);
			@unlink('../cache/220x160/'.$post['content_path']);
			break;
		case '2':
			@unlink('../img/content/gifs/'.$post['content_path']);
			break;
		case '3':
			@unlink('../img/content/videos/thumbs/'.$post['id'].'.jpg');
			@unlink('../img/content/videos/'.$post['id'].'.m4a');
			break;
		/* case '4':
			@unlink('../img/content/pictures/'.$post['content_path']);
			break; */
		case '5':
			@unlink('../img/content/pictures/'.$post['content_path']);
			@unlink('../cache/220x160/'.$post['content_path']);
			break;
		case '6':
			@unlink('../img/content/embeds/'.$post['content_path']);
			break;
	}
	
	$db->query('
		DELETE
		FROM content
		WHERE id='.(int)_p('id'),
		__LINE__,
		__FILE__
	);	
	
} else {
	header('Location: /find.php');
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
			Content Deleted
		</div>
	</div>
</div>
		
</body>
</html>
