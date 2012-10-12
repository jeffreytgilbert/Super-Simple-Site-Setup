<?php

include('../source/common.php');

$db = get_db_instance();

$visitor_id = record_visit($db);

$start = (int)_g('start');

/////// get the contents

$db->query('
	SELECT
		*
	FROM content
	ORDER BY content_category_id DESC
	',
	__LINE__,
	__FILE__
);

$Content = new ModelCollection();
while($db->readRow()){
	$Content->addItem(new Model($db->row_data));
}

/////// get the categories

$db->query('
	SELECT
		*
	FROM content_category',
	__LINE__,
	__FILE__
);

// create a data collection
$ContentCategories = new ModelCollection();
while($db->readRow()){
	// create the category object
	$ContentCategory = new Model($db->row_data);
	// populate a collection object of all the contents for this category
	$ContentCollection = $Content->getItemsBy('content_category_id', $ContentCategory->get('id')); // for some reason the type hinting says its returning a collection, but its returning an array
	// store it in the content category
	$ContentCategory->set('ContentCollection',$ContentCollection);
	// store this category in the category collection
	$ContentCategories->addItem($ContentCategory);
}

header('Content-Type:text/xml');
echo '<'.'?xml version="1.0" encoding="UTF-8"'.'?'.'>';

?>

<albums>

	<?php 
		foreach($ContentCategories as $ContentCategory):
	?>
	
	<album title="<?php echo htmlentities($ContentCategory->getData('title')) ?>">
	
		<?php 
			foreach($ContentCategory->get('ContentCollection') as $Content):
				if($Content->get('content_type_id') == 1): // if this is a picture
		?>
	
		<photo src="/img/content/pictures/<?php echo htmlentities($Content->getData('content_path')) ?>" showEveryPixel="false"></photo>
		
		<?php 
				endif;
			endforeach;
		?>
		
	</album>
	
	<?php 
		endforeach; 
	?>
	
</albums>
