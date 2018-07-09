<?php
echo "<p><h2>Uh oh!</h2>No view exists for this product type!</p>";
print_r($data);
die("");

// am I lazy? YES!
$model = $data[0];
if ($model["Id"] == "") {
	die("This view shouldn't exist!");
}

$imgUrl = PHP_IMG_PATH . "products/" . $model["Sku"] . "_th.jpg";

if (!file_exists($imgUrl)) {
	$imgUrl = NOT_FOUND_IMG;
}
else {
	$imgUrl = str_replace(PHP_IMG_PATH, HTML_IMG_PATH, $imgUrl);
}

?>

<h4>Edit - Product Id <?php echo $model["Id"]; ?></h4>
<div class="container">
	<div class="row">
		<div class="col-md-4">
			<img src="<?php echo $imgUrl; ?>" height="200px" width="200px" />
		</div>
		<div class="col-md-6">
			<!--<form action="index.php?edit_post" method="post">-->
				<input type="hidden" name="Id" id="Id" value="<?php echo $model["Id"]; ?>" />
				<div class="row input-row">
					<div class="col-md-4 right">Sku:</div>
					<div class="col-md-1"><?php echo $this->TextField("Sku",$model["Sku"]); ?></div>
				</div>
				
				<div class="row input-row">
					<div class="col-md-4 right">Name:</div>
					<div class="col-md-1"><?php echo $this->TextField("Name",$model["Name"]); ?></div>
				</div>
				
				<div class="row input-row">
					<div class="col-md-4 right">LegacySku:</div>
					<div class="col-md-1"><?php echo $this->TextField("LegacySku",$model["LegacySku"]); ?></div>
				</div>
				<div class="row">
					<div class="col-md-4 pad-half-em center"><button id="update">Update</button></div>
				</div>
			<!--</form>-->
		</div>
		
	</div>
</div>
<script>
	// something tells me I may regret sending form input this way
	$("#update").click(function() {
		var id = $("#Id").val();
		var sku = $("#Sku").val();
		var legacySku = $("#LegacySku").val();
		var name = $("#Name").val();
		var product = { Id: id, Sku: sku, LegacySku: legacySku, Name: name };
		
		// maybe I need to make this pass a JSON string of the product object?
		$.post( "index.php?action=applyedit", product)
			  .done(function( data ) {
				console.log(data);
			  });
	});
</script>