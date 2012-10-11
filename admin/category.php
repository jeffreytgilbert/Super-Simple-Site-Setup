<?php

ini_set('memory_limit', '1024M');//
ini_set('max_execution_time', '6000');//
set_time_limit(6000);

include('../source/common.php');

$db = get_db_instance();

// standard message handling objects from MVC framework code
class Report{
	public $Notices;
	public $Errors;
	public $Confirmations;
	
	public function __construct(){
		$this->Notices = new Model();
		$this->Errors = new Model();
		$this->Confirmations = new Model();
	}
}

$Report = new Report();

if(isset($_POST['ContentCategory']['title']) && trim($_POST['ContentCategory']['title']) != ''){
	$db->prepare('
	SELECT
		*
	FROM content_category
	WHERE title LIKE :title',
	__LINE__,
	__FILE__
	);
	
	$db->execute(array(':title'=>$_POST['ContentCategory']['title']));
	$db->readRow();
	
	if(!isset($db->row_data['title'])){

		$db->prepare('
		INSERT INTO content_category (
			title
		) VALUES (
			:title
		)', __LINE__, __FILE__);
		
		$db->execute(array(
			':title' => $_POST['ContentCategory']['title'],
		));
		
		$content_category_id = $db->insertId();
		
		$Report->Confirmations->set('Success',$content_category_id);
		
	} else {
		$Report->Notices->set('Notice',"Duplicate category found. Request to create new category ingored.");
	}
	
} else {
	$Report->Errors->set('Error',"Required field \"title\" missing.");
}

echo json_encode(array(
	'Notices'=>$Report->Notices->toArray(),
	'Errors'=>$Report->Errors->toArray(),
	'Confirmations'=>$Report->Confirmations->toArray(),
));
