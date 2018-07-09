<?php
    class Product {
        public $Id;
        public $Sku;
        public $LegacySku;
        public $Name;
        public $Discriminator;
        public $Discontinued;
        public $CreatedDate;
        public $ModifiedDate;
        public $BeginDate;
        public $EndDate;
        public $HideProduct;
        public $StockStatus;
        public $MetatagTitle;
        public $MetatagDescription;
        public $MetatagKeywords;
        public $SearchTerms;
        
        // relational fields
        public $StampSetId;
        public $StampTypeId;
        public $PrefixId;
		
		// sets model's fields from a provided array
		public function arrayToModel($arr) {
						
			$this->Id = $arr["Id"];
			$this->Sku = $arr["Sku"];
			$this->LegacySku = $arr["LegacySku"];
			$this->Name = $arr["Name"];
			$this->Discriminator = $arr["Discriminator"];
			$this->Discontinued = $arr["Discontinued"];
			$this->CreatedDate = $arr["CreatedDate"];
			$this->ModifiedDate = $arr["ModifiedDate"];
			$this->BeginDate = $arr["BeginDate"];
			$this->EndDate = $arr["EndDate"];
			$this->HideProduct = $arr["HideProduct"];
			$this->StockStatus = $arr["StockStatus"];
			$this->MetatagTitle = $arr["MetatagTitle"];
			$this->MetatagKeywords = $arr["MetatagKeywords"];
			$this->SearchTerms = $arr["SearchTerms"];
			$this->StampSetId = $arr["StampSetId"];
			$this->StampTypeId = $arr["StampTypeId"];
			$this->PrefixId = $arr["PrefixId"];			
		}
		
		// sets model's fields from a provided JSON string
		public function jsonToModel($jsonStr) {
			$obj = json_decode($jsonStr);
			
			$this->Id = $obj->Id;
			$this->Sku = $obj->Sku;
			$this->LegacySku = $obj->LegacySku;
			$this->Name = $obj->Name;
			$this->Discriminator = $obj->Discriminator;
			$this->Discontinued = $obj->Discontinued;
			$this->CreatedDate = $obj->CreatedDate;
			$this->ModifiedDate = $obj->ModifiedDate;
			$this->BeginDate = $obj->BeginDate;
			$this->EndDate = $obj->EndDate;
			$this->HideProduct = $obj->HideProduct;
			$this->StockStatus = $obj->StockStatus;
			$this->MetatagTitle = $obj->MetatagTitle;
			$this->MetatagKeywords = $obj->MetatagKeywords;
			$this->SearchTerms = $obj->SearchTerms;
			$this->StampSetId = $obj->StampSetId;
			$this->StampTypeId = $obj->StampTypeId;
			$this->PrefixId = $obj->PrefixId;	
		}
    }
?>