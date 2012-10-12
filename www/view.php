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
$posts = $db->all_data;
if(count($posts) == 0){ header('Location: 404'); }
$post = array_pop($posts);

// create initial hit for today if it doesnt exist
$db->prepare('
	INSERT OR IGNORE INTO content_hit
	(
		content_id,
		hits
	) VALUES (
		:content_id,
		0
	);', 
	__LINE__, 
	__FILE__
);
$db->execute(array(
	':content_id' => (int)_g('id')
));

// update the record to add +1 hit
$db->prepare('
	UPDATE content_hit 
	SET hits = hits+1
	WHERE content_id=:content_id AND hit_date=CURRENT_DATE;', 
	__LINE__, 
	__FILE__
);
$db->execute(array(
	':content_id' => (int)_g('id')
));

// update the content itself to show total number of hits
$db->prepare('
	UPDATE content
	SET hits = hits+1
	WHERE id=:id;',
	__LINE__,
	__FILE__
);
$db->execute(array(
	':id' => (int)_g('id')
));

// correct hit count for main post record
$post['hits'] = (int)$post['hits'] + 1;

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
$footer_posts = $db->all_data;

$pages = array_chunk($footer_posts, 4, true);

$footer_posts = $pages[rand(0, count($pages)-1)];

?>

<html>

<head>
<title>Super Simple Site Setup</title>
<link href='/css/common.css' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Allura' rel='stylesheet' type='text/css'>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-G00GL3-1']);
  _gaq.push(['_trackPageview']);

  (function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript" src="/img/swf/flowplayer-3.2.11.min.js"></script>
</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div>

	<div id="header">
		<h1 data-title="Super Simple Site Setup&nbsp;"><a href="/">Super Simple Site Setup&nbsp;</a></h1>
	</div>

	<div id="frame">
		<div id="nav">

<?php include('../source/menus/main.php'); ?>
		
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
/*				case 4: // video stream
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
						<img width="940" class="content" src="/img/content/pictures/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>"><br>
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
						<span>Photo Link</span>
					</div>
					<?php 
					break; */
				case 6: // embed
					?>
					<div class="content_container">
						<?php include('../img/content/embeds/'.$post['content_path']) ?>
						<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
						<span>Embed</span>
					</div>
					<?php 
					break;
				default:
					?>
					<script> location.href='/'; </script>
					<?php 
					break;	
					
			}
			
			?>
			
		</div>
		
		<div><br clear="all"/></div>
		
		<div id="footer">

				<div class="other_thumbs">
					<?php 
					
					foreach($footer_posts as $post){
					
						switch($post['content_type_id']){
							case 1: // photos
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/thumbnail.php?path=<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
									</a>
									<!--<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
								</div>
								<?php 
								break;
							case 2: // gifs
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/img/content/gifs/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
									</a>
									<!--<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
								</div>
								<?php 
								break;
							case 3: // video
								?>
								<div class="thumb_container">
									<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
										<img class="thumb" src="/img/content/videos/thumbs/<?php echo $post['id'] ?>.jpg" alt="<?php echo $post['title'] ?>">
									</a>
									<!--<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
								</div>
								<?php 
								break;
							/* case 4: // video stream
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
									<!--<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
								</div>
								<?php 
								break;
							case 6: // embed
								?>
								<div class="thumb_container">
									<?php include('../img/content/embeds/'.$post['content_path']) ?>
									<!--<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
								</div>
								<?php 
								break;
								
						}
					
					}
					
					?>
				</div>
	
			<div><br clear="all"/></div>
	
			<div class="fb-comments" data-href="http://sitename.com/view.php?id=<?php echo (int)_g('id') ?>" data-num-posts="2" data-width="940"></div>
			
		</div>
	
		<div><br clear="all"/></div>
	
	</div>

	<div><br clear="all"/></div>
</div>

</body>
</html>
