<?php

// this line is a great way to use fewer square brackets
// lazy? yes I am
$product = $data["Product"];

$imgUrl = PHP_IMG_PATH . "products/" . $product->Sku . "_th.jpg";

if (!file_exists($imgUrl)) {
	$imgUrl = NOT_FOUND_IMG;
}
else {
	$imgUrl = str_replace(PHP_IMG_PATH, HTML_IMG_PATH, $imgUrl);
}

$productJSON = json_encode($product);
$stampTypesJSON = json_encode($data["StampTypes"]);
$stampTypes = (object) $data["StampTypes"];

//echo "<p><h4>Stamp Types JSON:</h4>$stampTypesJSON</p>";
?>

<h4>Edit Stamp - Product Id <?php echo $product->Id; ?></h4>
<div class="container">
	<div class="row">
		<div class="col-md-4">
			<img src="<?php echo $imgUrl; ?>" height="200px" width="200px" />
		</div>
		<div class="col-md-6">
			<!--<form action="index.php?edit_post" method="post">-->
				<input type="hidden" name="Id" id="Id" value="<?php echo $product->Id; ?>" />
				<div class="row input-row">
					<div class="col-md-4 right">Sku:</div>
					<div class="col-md-1"><?php echo $this->TextField("Sku",$product->Sku); ?></div>
				</div>
				
				<div class="row input-row">
					<div class="col-md-4 right">Name:</div>
					<div class="col-md-1"><?php echo $this->TextField("Name",$product->Name); ?></div>
				</div>
				
				<div class="row input-row">
					<div class="col-md-4 right">Legacy Sku:</div>
					<div class="col-md-1"><?php echo $this->TextField("LegacySku",$product->LegacySku); ?></div>
				</div>
				<div class="row input-row">
					<div class="col-md-4 right">Stamp Type:</div>
					<div class="col-md-1"><?php echo $this->DropDown($data["StampTypes"], "StampTypes", "Id", "Name", $product->StampTypeId); ?></div>
				</div>
				<div class="row">
					<div class="col-md-4 pad-half-em center"><button id="update">Update</button></div>
				</div>
			<!--</form>-->
		</div>
		
	</div>
</div>
<script>
	var stampTypes = null;
	var product = null;
	
	$(document).ready(function() {
		// parse the stamp types into a JS object for later
		stampTypes = JSON.parse('<?php echo $stampTypesJSON ?>');
		product = JSON.parse('<?php echo $productJSON; ?>');	
	})
	
	// something tells me I may regret sending form input this way
	$("#update").click(function() {
		
		//product.Id = $("#Id").val();
		product.Sku = $("#Sku").val();
		product.LegacySku = $("#LegacySku").val();
		product.Name = $("#Name").val();
		product.StampTypeId = $("#StampTypes").val();
		var productJson = JSON.stringify(product);
		
		// pretty sure I shouldn't put the JSON string into an object, but it works so screw it
		$.post( "index.php?action=applyedit", { product: productJson })
			  .done(function( data ) {
				console.log(data);
			  });
	});
	
	// changes the suffix of the sku if the stamp type is changed
	$("#StampTypes").change(function() {
		var sku = $("#Sku").val();
		var newStampTypeId = $(this).val();
		var newStampType = null;

		for (var i = 0; i < stampTypes.length; i++) 
		{
			if (stampTypes[i].Id == newStampTypeId) 
			{
				newStampType = stampTypes[i];
				sku = sku.slice(0,-1) + newStampType.Suffix;
				$("#Sku").val(sku);
				return;
			}
		}
	});
</script>