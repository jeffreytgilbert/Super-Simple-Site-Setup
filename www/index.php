<?php

include('../source/common.php');

$db = get_db_instance();

$visitor_id = record_visit($db);

$start = (int)_g('start');

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

$PageData = new PageData($start, count($posts), 48);

$pages = array_chunk($posts, 48, true);

if(count($pages) > 0){
	$posts = $pages[$start/48];
}else{
	$posts = array();
}
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
</head>
<body>

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
								<!-- <?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
							</div>
							<?php 
							break;
						case 2: // gifs
							?>
							<div class="thumb_container">
								<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="/img/content/gifs/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
								</a>
								<!-- <?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
							</div>
							<?php 
							break;
						case 3: // video
							?>
							<div class="thumb_container">
								<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="/img/content/videos/thumbs/<?php echo $post['id'] ?>.jpg" alt="<?php echo $post['title'] ?>">
								</a>
								<!-- <?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
							</div>
							<?php 
							break;
/*						case 4: // video stream
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
								<!-- <?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
							</div>
							<?php 
							break;
						case 6: // embed
							?>
							<div class="thumb_container">
								<?php include('../img/content/embeds/'.$post['content_path']) ?>
								<!-- <?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>-->
							</div>
							<?php 
							break;
							
					}
				
				}
				
				?>
			</div>
		
		</div>
		
		<div><br clear="all"/></div>
	
		<div id="footer">
		
		<?php 
		echo $PageData->display('/?');
		?>
		
		</div>
	
		<div><br clear="all"/></div>
	
	</div>
	
	<div><br clear="all"/></div>

</div>

<div><br clear="all"/></div>
	
</body>
</html>

