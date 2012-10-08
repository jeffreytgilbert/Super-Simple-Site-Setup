<?php

include('../source/common.php');

$db = get_db_instance();

$visitor_id = record_visit($db);

$start = (int)_g('start');

$db->query('
	SELECT
		*
	FROM content
	WHERE title LIKE '.$db->like(_g('term'), $db::LIKE_WC_FIRST_LAST).'
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
<style>
.search{
	 font-family: Verdana, Geneva, sans-serif;
    font-size: 24px;
    color: #FFF;
    padding: 5px 50px 5px 50px;
    border: 1px solid #999;
 
    text-shadow: 0px 1px 1px #666;
    text-decoration: none;
 
    -moz-box-shadow: 0 1px 3px #111;
    -webkit-box-shadow: 0 1px 3px #111;
    box-shadow: 0 1px 3px #111;
 
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
 
    background: #64a724;
    background: -moz-linear-gradient(top, #64a724 0%, #579727 50%, #58982a 51%, #498c25 100%);
    background: -webkit-gradient(linear, left top, left bottom, from(#64a724), to(#498c25), color-stop(0.4, #579727), color-stop(0.5, #58982a), color-stop(.9, #498c25), color-stop(0.9, #498c25));
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#64a724', endColorstr='#498c25', GradientType=0 );
 
    cursor: pointer;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>

$(document).ready(function(){
	$('.thumb_container').each(function(){
		$(this).append('<form class="delete_button" action="/delete.php" method="post"><input type="hidden" name="id" value="'+$(this).data('id')+'"><input type="submit" value="X"></form>');
	});
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
			
			<form action="" method="get">
				<fieldset>
					<legend>Find it</legend>
					<input type="text" name="term" value="<?php echo htmlentities(_g('term')); ?>" style="float:left;margin-right:20px; width:700px; padding:10px; font-size:18px; line-height:18px;">
					<input type="submit" value="Search" class="search">
				</fieldset>
			</form>
			
			<div><br clear="all"/></div>
			
			<h2>Matching Posts</h2>
			<div class="other_thumbs">
				<?php 
				
				foreach($posts as $post){
				
					switch($post['content_type_id']){
						case 1: // photos
							?>
							<div class="thumb_container" data-id="<?php echo $post['id'] ?>">
								<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="/thumbnail.php?path=<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
								</a>
								<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
							</div>
							<?php 
							break;
						case 2: // gifs
							?>
							<div class="thumb_container" data-id="<?php echo $post['id'] ?>">
								<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="/img/content/gifs/<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
								</a>
								<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
							</div>
							<?php 
							break;
						case 3: // video
							?>
							<div class="thumb_container" data-id="<?php echo $post['id'] ?>">
								<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="/img/content/videos/thumbs/<?php echo $post['id'] ?>.jpg" alt="<?php echo $post['title'] ?>">
								</a>
								<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
							</div>
							<?php 
							break;
						/* case 4: // video stream
							?>
							<div class="thumb_container" data-id="<?php echo $post['id'] ?>">
								<a class="thumb_link" href="/view.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
								</a>
								<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
							</div>
							<?php 
							break; */
						case 5: // url
							?>
							<div class="thumb_container" data-id="<?php echo $post['id'] ?>">
								<a class="thumb_link" href="/out.php?id=<?php echo $post['id'] ?>">
									<img class="thumb" src="/thumbnail.php?path=<?php echo $post['content_path'] ?>" alt="<?php echo $post['title'] ?>">
								</a>
								<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
							</div>
							<?php 
							break;
						case 6: // embed
							?>
							<div class="thumb_container" data-id="<?php echo $post['id'] ?>">
								<?php include('../img/content/embeds/'.$post['content_path']) ?>
								<?php echo $post['hits'] ?> visits since <?php echo date("F d Y", strtotime($post['created_date'])) ?>
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

