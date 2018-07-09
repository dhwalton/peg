<?php
    require_once(ROOT_PATH . VIEW_PATH . "view.php");
    require_once("base-controller.php");

    class ProductController extends Controller {
        
		
		
		// need to make this pass in a model instead of individual values
		public function ApplyEdit($product) {
			$sql = 	"UPDATE Products ";
			$sql .=	"SET Name = '$product->Name', "; 
			$sql .= "Sku = '$product->Sku', "; 
			$sql .= "LegacySku = '$product->LegacySku', ";
			$sql .= "StampTypeId = $product->StampTypeId, ";
			$sql .= "StampSetId = $product->StampSetId, ";
			$sql .= "PrefixId = $product->PrefixId ";
			$sql .=	"WHERE Id = $product->Id";
			
			parent::Execute($sql);
			
			// need some error checking to make sure the prefix id matches the prefix, ditto for stamp type
			
		}
		
        public function Edit($id) {
			$viewModel = array();
			
            $query = "SELECT * FROM Products WHERE Id = $id";
            $queryResult = parent::Select($query);
			
			// put the query result into a product model
			$p = new Product();
			$p->arrayToModel($queryResult[0]);
			
			// add the product model to the view model
			$viewModel["Product"] = $p;
			
			// select view model and view based on product type (discriminator)
			switch($p->Discriminator) {
				case "Stamp":
					// get a list of the stamp types, add them to the view model
					$st = new StampTypeController;
					$stampTypes = $st->AllStampTypes();
					$st = null;
					$viewModel["StampTypes"] = $stampTypes;
					
					// set template path to edit stamp view
					$templatePath = "products/edit-stamp.php";
					break;
				case "StampSet":
					
				// ideally, this never should be called
				default:
					print_r($result);
					$templatePath = "products/edit.php";
			}
			
			$view = new View($templatePath, false);
            return $view->render($viewModel);
        }

        public function Create($name) {
            $query = "INSERT INTO Names (Name) values ('$name')";
            parent::Execute($query);
        }

        public function Update($id, $name) {
            $query = "UPDATE Products Set Name = '$name' WHERE Id = $id";
            parent::Execute($query);
        }

        public function Delete($id) {
            $query = "DELETE FROM Products WHERE Id = $id";
            parent::Execute($query);
        }

        
        public function Index($filter) 
        {
			if ($filter == "") 
			{
				$filter = "1";
			} 
			else 
			{
				$filter = "Sku LIKE '$filter%' OR Name LIKE '$filter%'";	
			}
			
			$sql = "SELECT * FROM Products WHERE $filter";
			
			$products = parent::Select($sql);
			
			
			
            $templatePath = "products/index.php";
            $view = new View($templatePath, true);
			
            return $view->render($products);
        }
    }
?>