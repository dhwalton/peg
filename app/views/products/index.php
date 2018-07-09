<h2>Products Index</h2>
		<div id="filter">
			Search: <input type="text" name="filter" id="filter-text" /> 
			<button id="filter-button">Search</button>
		</div>
		<div class="content">
			<?php
			for ($i = 0 ; $i < count($data); $i++) {
				echo "\n" . '<div class="product-container" id="'. $data[$i]["Id"] .'">';
				
				$imgUrl = PHP_IMG_PATH . "products/" . $data[$i]["Sku"] . "_th.jpg";
				//echo "\n<!-- Checking $imgUrl - ";
				if (!file_exists($imgUrl)) {
					$imgUrl = NOT_FOUND_IMG;
					//echo "Not found! -->";
				}
				else {
					//echo "Found! -->";
					$imgUrl = str_replace(PHP_IMG_PATH, HTML_IMG_PATH, $imgUrl);
				}
				echo "\n\t";
				echo '<img src="' . $imgUrl . 
							  '" height="' . THUMB_DIMENSION . 
							  '" width="' . THUMB_DIMENSION . 
							  '" alt="' . $data[$i]["Sku"] . " - " . $data[$i]["Name"] . 
					          '" title="' . $data[$i]["Sku"] . " - " . $data[$i]["Name"] . 
							  '" />';
				echo "\n\t" . '<br /><span class="product-sku">' . $data[$i]["Sku"] . "</span>";
				echo "\n\t" . '<br /><span class="product-name">' . $data[$i]["Name"] . "</span>";
				
				echo "\n</div>";
			}
				
			?>
		</div>

		<div id="edit-modal">
			<div class="modal-top">
				<span class="modal-close">X</span>
			</div>
			<div class="modalcontent"></div>
		</div>

<script>
	// triggers search button click on enter keypress
	$("#filter-text").keyup(function(e) {
		if (e.keyCode == 13) {
			$("#filter-button").trigger("click");
		}
	});
	
	// sends the search parameters to the controller
	$("#filter-button").click(function() {
		var url = "index.php?action=index"
		var searchText = $("#filter-text").val().trim();
		if (searchText != "") {
			url += "&filter=" + searchText;
		}
		window.location.href = url;
	});
	
	// open edit modal on product-container click
	$(".product-container").click(function() {
		var id = $(this).attr("id");
		$.get("index.php?action=edit&id=" + id, function(data){
			$("#edit-modal .modalcontent").html(data);
			$("#edit-modal").modal();
		});
	});
	
	$(".modal-close").click(function(){ $("#edit-modal").modal('toggle'); })
</script>