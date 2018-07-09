<?php
// "router" for the controller, this could probably be a lot better
require_once("../app/config/paths.php");

$controller = new ProductController;

$action = strtolower($_GET['action']);

switch($action) {
	case 'applyedit':
		$product = new Product;
		$product->jsonToModel($_POST["product"]);
		print_r($product);
		
		if ($product->Id != null) {
			return $controller->ApplyEdit($product);
			break;
		}
		else {
			die("NO PRODUCT PASSED!!!");
		}
	case 'edit':
		$id = $_GET['id'];
		if ($id != "") 
		{
			echo $controller->Edit($id);
			break;
		}
	case 'index':
	default:
		$filter = $_GET['filter'];
		echo $controller->Index($filter);
}

?>