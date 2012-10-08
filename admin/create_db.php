<?php

include('../source/common.php');

$db = get_db_instance();

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
			Database recreated. 
			
			
		</div>
	</div>
</div>
		
</body>
</html>
