<?php

include('../source/common.php');

$db = get_db_instance();

// record_visit($db);

if(table_exists($db, 'visitor') && table_exists($db, 'visit') && table_exists($db, 'content_hit') && table_exists($db, 'content') && table_exists($db, 'content_type')){
	$db_ok = true;
} else {
	$db_ok = false;
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
			<p>
			<?php 
				if($db_ok){
			?>
				Your DB is installed and working.
			<?php 
				}else{
			?>
				Your DB isn't setup yet. <a href="create_db.php">Create it?</a>
			<?php 
				}
			?>
			</p>
			<ul>
				<li><a href="/post.php">Wanna post some content?</a></li>
				<li><a href="/find.php">Wanna find some content?</a></li>
				<li><a href="/visitors.php">Wanna see some stats?</a></li>
			</ul>
		</div>
	</div>
</div>
		
</body>
</html>
