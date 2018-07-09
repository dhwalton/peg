<?php
require("app/config/paths.php");
require("app/controllers/stamptype-controller.php");
//require("app/config/seed.php");

$c = new StampTypeController;
$c->StampTypesJSON();
?>
