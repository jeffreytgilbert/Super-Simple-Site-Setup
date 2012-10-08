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

header('Location: '.$post['out_link']); exit;