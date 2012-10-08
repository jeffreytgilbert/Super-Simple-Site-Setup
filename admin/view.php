<?php

include('../source/common.php');

$db = get_db_instance();

$visitor_id = record_visit($db);

$db->query('
	SELECT
		*
	FROM content
	WHERE id='.(int)_g('id'),
		__LINE__,
		__FILE__
);

$db->read();
$posts = $db->row_data;
if(count($posts) == 0){ header('Location: 404'); }
$post = array_pop($posts);

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
$footer_posts = $db->row_data;

$pages = array_chunk($footer_posts, 4, true);

$footer_posts = $pages[rand(0, count($pages)-1)];

?>

<html>

<head>
<title>Super Simple Site Setup</title>
<link href='/css/common.css' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Allura' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="/img/swf/flowplayer-3.2.11.min.js"></script>
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
		
			<?php 
			
			switch($post['content_type_id']){
				case 1: // photos
					?>
					<div class="content_container">
						<img width="940" class="content" src="/img/content/pictures/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>"><br>
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
						<span>Photo</span>
					</div>
					<?php 
					break;
				case 2: // gifs
					?>
					<div class="content_container">
						<img class="content" src="/img/content/gifs/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>"><br>
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
						<span>Animated GIF</span>
					</div>
					<?php 
					break;
				case 3: // video
					?>
					<div class="content_container">
						
						<a href="/img/content/videos/<?php echo $post['content_path'] ?>" style="display:block;width:850px;height:480px" id="player"></a> 
					
						<script>
							flowplayer("player", "/img/swf/flowplayer-3.2.15.swf");
						</script>
						<span>Video</span>
						
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
					</div>
					<?php 
					break;
				case 4: // video stream
					?>
					<div class="content_container">
					
						<a href="<?php echo $post['content_path'] ?>" style="display:block;width:850px;height:480px" id="player"></a> 
					
						<script>
							flowplayer("player", "/img/swf/flowplayer-3.2.15.swf");
						</script>
						<span>Hijacked stream</span>
						
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
					</div>
					<?php 
					break;
				case 5: // url
					?>
					<div class="content_container">
						<a class="content_link" href="<?php echo $post['content_path'] ?>">
							<?php echo $post['title'] ?>
						</a>
						<span>Link</span>
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
					</div>
					<?php 
					break;
				case 6: // embed
					?>
					<div class="content_container">
						<?php include('../img/content/embeds/'.$post['content_path']) ?>
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
						<span>Embed</span>
					</div>
					<?php 
					break;
					
					
			}
			
			?>
			
		</div>
		
		<div><br clear="all"/></div>

		<div>
			
		</div>
		
		<div><br clear="all"/></div>
		
	</div>

	<div><br clear="all"/></div>
</div>

</body>
</html>
