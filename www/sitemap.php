<?php

include('../source/common.php');

$db = get_db_instance();

$visitor_id = record_visit($db);

$start = (int)_g('start');

$db->query('
	SELECT
		*
	FROM content
	ORDER BY hits DESC',
	__LINE__,
	__FILE__
);

$db->read();
$posts = $db->all_data;

header('Content-Type:text/xml');
echo '<'.'?xml version="1.0" encoding="UTF-8"'.'?'.'>'; 
?>

<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

<url>
  <loc>http://sitename.com/index.php</loc>
  <changefreq>weekly</changefreq>
</url>
<?php
foreach($posts as $post){
?>
<url>
  <loc>http://sitename.com/view.php?id=<?php echo $post['id'] ?>&amp;title=<?php echo htmlentities($post['title']) ?></loc>
  <changefreq>weekly</changefreq>
</url>
<?php
}
?>
</urlset>