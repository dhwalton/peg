<?php
require_once(ROOT_PATH . VIEW_PATH . "view.php");
require_once("base-controller.php");

class StampTypeController extends Controller {
	public function StampTypesJSON() {
		$sql = "SELECT * FROM StampTypes WHERE 1";
		$result = parent::Select($sql);
		
		return json_encode($result);
	}
	public function AllStampTypes() {
		$sql = "SELECT Id, Name, Suffix FROM StampTypes WHERE 1";
		$result = parent::Select($sql);
		return $result;
	}
	
	// Need C R U D actions for this model!
}

?>