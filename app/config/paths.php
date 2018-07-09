<?php
    const ROOT_PATH = "/volume1/web/peg/app/";
    const MODEL_PATH = "models/";
    const CONFIG_PATH =  "config/";
    const CONTROLLER_PATH = "controllers/";
    const VIEW_PATH =  "views/";
	const HTML_IMG_PATH = "http://192.168.1.17/peg/images/";
	const PHP_IMG_PATH = "/volume1/web/peg/images/";
	const NOT_FOUND_IMG = "http://192.168.1.17/peg/images/notfound.jpg";
	const THUMB_DIMENSION = "149px";
	
	// controllers
	require_once(ROOT_PATH . CONTROLLER_PATH . "product-controller.php");
	require_once(ROOT_PATH . CONTROLLER_PATH . "stamptype-controller.php");

	// models
    require_once(ROOT_PATH . MODEL_PATH . "product.php");
    require_once(ROOT_PATH . MODEL_PATH . "prefix.php");
    require_once(ROOT_PATH . MODEL_PATH . "stamptype.php");
	
	// views
    require_once(ROOT_PATH . VIEW_PATH . "view.php");
    
?>