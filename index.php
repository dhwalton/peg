<?php
require("app/config/paths.php");
require("app/controllers/product-controller.php");
require("app/config/seed.php");
?>

<!doctype html>
<html>
<head>
<!--
<style type="text/css">
body {
    font-family: "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", Verdana, "sans-serif"
}

.header {
    background-color: #C0DB97;
    margin: 0.5em;
    padding: 1em;
    border-radius: 10px;
    border: 1px solid silver;
}

.section {
    background-color: #EEEEEE;
    margin: 0.5em;
    padding: 1em;
    border-radius: 10px;
    border: 1px solid silver;
}

.section div {
    background-color: #fff;
    padding: 0.25em;
    margin: 0.5em;
    border-radius: 10px;
}

.section span {
    color: indianred;
    font-weight: 700;
}
tr:nth-child(odd) {
    background-color: #eadefc;
}
td {
    width: 20em;
    padding: 4px;
}
th {
    text-align:left;
}
table {
    background-color: #fff;
}
</style>
-->
</head>
<body>
<h1>INDEX</h1>
<?php

//$s = new DatabaseSeed;
/*
echo "\n" . '<div class="section">' . "\n\t<h2>Seeding Products...</h2>";
$s->SeedProducts();
echo "\n</div>\n" . '<div class="section">' . "\n\t<h2>Seeding Stamp Types...</h2>";
$s->SeedStampTypes();
echo "\n</div>";

$s->SeedPrefixes();
$s->LinkProductsToPrefixes();
$s->LinkStampsToStampTypes();
*/

$p = new ProductController;
echo $p->Index();
?>

</body>
</html>