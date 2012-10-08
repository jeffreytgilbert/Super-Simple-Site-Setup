<?php

include('../source/common.php');

$db = get_db_instance();

// record_visit($db);

$db->query('
	SELECT 
		visitor.ip_guess AS ip_guess, 
		visitor.visits AS visits, 
		visit.created_datetime AS last_visited,
		visit.user_agent AS user_agent
	FROM visitor INNER JOIN visit ON (visitor.id=visit.visitor_id)
	WHERE visitor.ip_guess NOT NULL
	GROUP BY visit.visitor_id
	ORDER BY MAX(visit.created_datetime) DESC
	LIMIT 100',
	__LINE__,
	__FILE__
);

$db->read();
$visitors = count($db->row_data)>0?$db->row_data:array();


$db->query('
	SELECT 
		COUNT(ip_guess) AS visits,
		ip_guess,
		user_agent,
		created_datetime
	FROM visit
	ORDER BY visits DESC
	LIMIT 100',
	__LINE__,
	__FILE__
);

$db->read();
$repeat_visitors = count($db->row_data)>0?$db->row_data:array();

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
			<div id="recent_visitors">
				<h2>Recent Visitors</h2>
				<ul>
				
					<?php foreach ($visitors as $visitor): ?>
					
					<li title="<?php echo htmlentities($visitor['user_agent']); ?>"><?php echo $visitor['ip_guess']; ?> # <?php echo $visitor['visits'] ?> @ <?php echo date("F d Y H:i", strtotime($visitor['last_visited'])) ?></li>
					
					<?php endforeach; ?>
					
				</ul>
			</div>
			<div id="repeat_visitors">
				<h2>Most Active Visitors</h2>
				<ul>
				
					<?php foreach ($repeat_visitors as $visitor): ?>
					
					<li title="<?php echo htmlentities($visitor['user_agent']); ?>"><?php echo $visitor['ip_guess']; ?> # <?php echo $visitor['visits'] ?> @ <?php echo date("F d Y H:i", strtotime($visitor['created_datetime'])) ?></li>
					
					<?php endforeach; ?>
					
				</ul>
			</div>
		</div>
	</div>
</div>
		
</body>
</html>
