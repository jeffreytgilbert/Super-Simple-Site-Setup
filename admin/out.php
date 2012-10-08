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

header('Location: '.$post['out_link']); exit;