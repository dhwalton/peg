<?php
    require "/volume1/web/peg/app/config/paths.php";

    // catch-all object that handles seeding the database and fixing other things that might pop up
    class DatabaseSeed {
        private $pdo;
        private $log;
        // creates a PDO connection to the database
        function InitPDO() 
        {
            $host = 'localhost';
            $db   = 'peg';
            $user = 'root';
            $pass = '';
            //$charset = 'utf8mb4';

            //$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $dsn = "mysql:host=$host;dbname=$db";
            $opt = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            );
            return new PDO($dsn, $user, $pass, $opt);
        }

        function LogIt($str)
        {
            if ($this->log == true)
            {
                echo $str;
            }
        }

        function __construct() 
        {
            $this->pdo = $this->InitPDO();
            $this->log = true;
        }
        
        function __destruct() 
        {
            $this->pdo = null;
        }

        function TableExists($tableName) 
        {
            $sql = 'SHOW TABLES';
            $result = $this->pdo->query($sql);
            while ($row = $result->fetch(PDO::FETCH_NUM)) 
            {
                if ($tableName == $row[0]) 
                {
                    return TRUE;
                }
            }
            return FALSE;
        }

        // perform an Execute type query (insert, update, delete)
        public function Execute($query) 
        {
            try {
                $this->pdo->exec($query);
            }
            catch(PDOException $e) {
                $this->LogIt("<p>" . $e->getMessage() . "</p>");
            }
        }

        // perform a select query, return results in array
        public function Select($query) 
        {
            $result = array();
            $stmt = $this->pdo->query($query);
            while ($row = $stmt->fetch())
            {
                array_push($result, $row);
            }
            return $result;
        }
        
        // cheesy, but it makes life easier
        function RecordExists($table, $key, $value)
        {
            $sql = "SELECT COUNT(*) FROM $table WHERE $key LIKE '$value'";
            //$this->LogIt("\n\t" . $sql . "<br />");
            if ($stmt = $this->pdo->query($sql)) 
            {
                if ($stmt->fetchColumn() > 0)
                {
                    //$this->LogIt("Found $key '$value' in $table!<br />");
                    return TRUE;
                }
            }
            //$this->LogIt("Didn't find $key '$value' in $table!<br />");
            return FALSE;
        }

        // very inefficient function that checks to see if a sku exists, then updates the record if it does;
        // or adds the record if it doesn't.
        function AddOrUpdateProduct($sku, $legacySku, $name) 
        {
            $this->LogIt("\n\t\t<tr><td>$sku</td><td>$name</td>");
            if ($this->RecordExists("Products", "Sku", $sku))
            {   
                $sql = "UPDATE Products set LegacySku = '$legacySku', Name = '$name' WHERE Sku = '$sku'";
                
            }
            else 
            {
                $sql = "INSERT INTO Products (Sku, LegacySku, Name) VALUES ('$sku','$legacySku','$name')";
                
            }
            $this->LogIt("<td>" . substr($sql,0,6) . "</td></tr>");
            $this->Execute($sql);
        }

        function AddOrUpdateStampType($pressMultiplier, $stampsPerPlate, $suffix, $name, $weight, $price, $brand, $searchTerms, $freeShipping) {
            $this->LogIt("\n\t\t<tr><td>$name</td>");
            if ($this->RecordExists("StampTypes","Suffix", "%".$suffix)) 
            {
                $sql =  "UPDATE StampTypes SET " . 
                        "PressMultiplier = $pressMultiplier, " . 
                        "StampsPerPlate = $stampsPerPlate, " .
                        "Suffix = '$suffix', " . 
                        "Name = '$name', " .
                        "Weight = $weight, " .
                        "Price = $price, " .
                        "Brand = '$brand', " .
                        "SearchTerms = '$searchTerms', " .
                        "FreeShipping = $freeShipping " . 
                        "WHERE Suffix = '$suffix'";
            }
            else 
            {
                $sql =  "INSERT INTO StampTypes " .
                        "(PressMultiplier, StampsPerPlate, Suffix, Name, Weight, Price, Brand, SearchTerms, FreeShipping) " .
                        "VALUES ($pressMultiplier, $stampsPerPlate, '$suffix', '$name', $weight, $price, '$brand', '$searchTerms', $freeShipping)";
            }
            $this->LogIt("<td>" . substr($sql,0,6) . "</td></tr>");
            $this->Execute($sql);
        }

        // figured I'd try an approach using the model.  It makes the seed method less pretty but it works pretty well.
        function AddOrUpdatePrefix($model)
        {
            $this->LogIt("\n\t\t<tr><td>$model->Prefix</td><td>$model->Name</td>");
            if ($this->RecordExists("Prefixes", "Prefix", $model->Prefix))
            {
                $sql = $model->UpdateSQLByPrefix();
            }
            else 
            {
                $sql = $model->InsertSQL();
            }
            $this->LogIt("<td>" . substr($sql,0,6) . "</td></tr>");
            $this->Execute($sql);
        }

        function SeedProducts() 
        {
            //$this->LogIt("\n" . '<div class="header"><h2>Seeding Products</h2></div>');
            //$this->LogIt("\n" . '<div class="section">');
            if ($this->TableExists("Products") == false)
            {
                //$this->LogIt("\n<h3>Creating Products Table...</h3>");
                $sql =  "CREATE TABLE Products (" .
                        "Id int NOT NULL AUTO_INCREMENT, " .
                        "Sku varchar(50), " . 
                        "LegacySku varchar(50), " . 
                        "Name varchar(255), " .
                        "Discriminator varchar(255), " .
                        "Discontinued bit, " .
                        "CreatedDate datetime, " . 
                        "ModifiedDate datetime, " .
                        "BeginDate date, " .
                        "EndDate date, " .
                        "HideProduct bit, " .
                        "Enabled bit, " .
                        "StockStatus int, " . 
                        "MetatagTitle varchar(255), " .
                        "MetatagDescription text, " .
                        "MetatagKeywords text, " .
                        "SearchTerms text, " .
                        "StampSetId int, " . 
                        "StampTypeId int, " .
                        "PrefixId int, " .  
                        "PRIMARY KEY (Id)" . 
                        ")"; 
                $this->Execute($sql);
            }
            else 
            {
                //$this->LogIt("\n<h3>Products Table Exists!</h3>");
            }
            $this->LogIt("\n\t<table cellspacing=0>");
            $this->LogIt("\n\t\t<tr><th>Sku</th><th>Name</th><th>Query Type</th></tr>");
            $this->AddOrUpdateProduct("BUI-TEST-M", "", "Mini Stamp Test");
            $this->AddOrUpdateProduct("FLS-TEST-T", "", "Tiny Stamp Test");
            $this->AddOrUpdateProduct("FLC-TEST-Y", "", "Bitty Stamp Test");
            $this->AddOrUpdateProduct("LEA-TEST-B", "", "Big Stamp Test");
            $this->AddOrUpdateProduct("SPO-TEST-J", "", "Jumbo Stamp Test");
            $this->LogIt("\n\t</table>");
        }

        function SeedStampTypes() 
        {
            //$this->LogIt("\n" . '<div class="header"><h2>Seeding Stamp Types</h2></div>');
            //$this->LogIt("\n" . '<div class="section">');
            if ($this->TableExists("StampTypes") == false)
            {
                //$this->LogIt("\n<h3>Creating StampTypes Table...</h3>");
                $sql =  "CREATE TABLE StampTypes (" .
                        "Id int NOT NULL AUTO_INCREMENT, " .
                        "PressMultiplier int, " .
                        "StampsPerPlate int, " .
                        "Suffix varchar(3), " . 
                        "Name varchar(50), " .
                        "Weight float, " . 
                        "Price float, " .
                        "Brand varchar(50), " .
                        "SearchTerms text, " .
                        "FreeShipping bit, " .
                        "PRIMARY KEY (Id)" . 
                        ")"; 
                $this->Execute($sql);
            }
            else 
            {
                //$this->LogIt("</div>\n" . '<div class="section"><h3>StampTypes Table Exists!</h3>');
            }
            $this->LogIt("\n\t<table>");
            $this->LogIt("\n\t\t<tr><th>Name</th><th>Query Type</th></tr>");
            $this->AddOrUpdateStampType(2, 16, "M", "Mini Stamp",   0.0397, 3.5, "Rubber Stamp Tapestry", "peg stamp, peg stamps, pegs, mini, mini stamp, mini stamps", true);
            $this->AddOrUpdateStampType(2, 18, "T", "Tiny Stamp",   0.0176, 2.5, "Rubber Stamp Tapestry", "peg stamp, peg stamps, pegs, tiny, tiny stamp, tiny stamps", true);
            $this->AddOrUpdateStampType(2, 21, "Y", "Bitty Stamp", 	0.0132,	2,   "Rubber Stamp Tapestry", "peg stamp, peg stamps, pegs, bitty, bitty stamp, bitty stamps", true);
            $this->AddOrUpdateStampType(4, 9,  "J", "Jumbo Stamp", 	0.044,	4.5, "Rubber Stamp Tapestry", "peg stamp, peg stamps, pegs, jumbo, jumbo stamp, jumbo stamps", true);
            $this->AddOrUpdateStampType(2, 16, "B", "Big Stamp",    0.033,	4,	 "Rubber Stamp Tapestry", "block stamp, block stamps, blocks, 1 inch, one inch, 1-inch, one-inch, 1x1, big, big stamp, big stamps", true);
            $this->AddOrUpdateStampType(2, 18, "L", "1.5 Inch Block Stamp",	0.0463,	5.25, "Rubber Stamp Tapestry", "block stamp, block stamps, blocks, 1.5 inch, 1.5 inches, one and a half inch, 1x1.5, 1.5x1", true);
            $this->AddOrUpdateStampType(2, 18, "D", "3 Inch Block Stamp",	0.0705,	6,	"Rubber Stamp Tapestry", "block stamp, block stamps, blocks, 3 inch, three inch, three inches, 1x3, 3x1", true);
            $this->LogIt("\n\t</table>");
        }

        function CreatePrefix($prefix, $name, $enabled, $searchTerms, $categoryStr)
        {
            $model = new Prefix;
            $model->Prefix = $prefix;
            $model->Name = $name;
            $model->Enabled = $enabled;
            $model->SearchTerms = $searchTerms;
            $model->CategoryStr = $categoryStr;
            return $model;
        }

        function SeedPrefixes()
        {
            if ($this->TableExists("Prefixes") == false)
            {
                $sql =  "CREATE TABLE Prefixes (" .
                "Id int NOT NULL AUTO_INCREMENT, " .
                "Prefix varchar(6), " .
                "Name varchar(255), " .
                "Enabled bit, " . 
                "SearchTerms text, " .
                "CategoryStr text, " .
                "PRIMARY KEY (Id)" . 
                ")"; 
                $this->Execute($sql);
            }
            $this->LogIt("\n\t<table>");
            $this->LogIt("\n\t\t<tr><th>Prefix</th><th>Name<th>Query Type</th>");

            // Not sure if I'm happy with the way this works, but it uses the model so meh
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FAB","Fabric Stamp Sets",0,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,fabric stamp set,fabric stamp kit,fabric,cloth","Peg Stamp Sets>Floral;Peg Stamp Sets>Fabric;Peg Stamp Sets>Ceramic Bisque;Peg Stamp Sets;Peg Stamp Sets>Retiring Soon!"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SAN","Animal Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,animal stamp kit,animal stamp set,animal kit,animal,animals","Peg Stamp Sets>Animal & Aquatic;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SAQ","Aquatic Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,aquatic,ocean,sea,fish","Peg Stamp Sets>Animal & Aquatic;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SBE","Berry Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,berry,berries","Peg Stamp Sets>Berry;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SBR","Sprig and Branch Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,sprig,branch,sprigs,branches","Peg Stamp Sets>Sprig & Branch;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SCE","Celestial Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,star,stars,snowflake,snowflakes","Peg Stamp Sets>Celestial;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SCO","Collage Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,collage,miscellaneous","Peg Stamp Sets>Collage;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SFL","Floral Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,floral,flower,flowers,botanical,petals,petal","Peg Stamp Sets>Floral;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SFV","Fruit and Veggie Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,fruit,fruits,vegetables,vegetable,veggie,veggies","Peg Stamp Sets>Fruit & Veggie;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SHO","Special Occasion Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,special occasion,special occasions,holiday,holidays","Peg Stamp Sets>Holiday & Special Occasions;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SLE","Leaf Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,leaf,leaves,leafs,autumn,fall","Peg Stamp Sets>Leaf;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SWD","Word Stamp Sets",1,"kit,peg stamp set,peg stamp sets,stamp kit,peg stamp kits,stamp set,sets,set,peg stamp kit,sentiment,word,words","Peg Stamp Sets>Words & Sentiments;Peg Stamp Sets"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("UNM","Unmounted Stamp Sets",1,"unmounted,un mounted,un-mounted,set,kit","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SBI","Insect Stamp Sets",1,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("ANI","Animal Stamps",1,"animal,animals","Individual Peg Stamps>Animals, Birds & Aquatic;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("AQU","Aquatic Stamps",1,"aquatic,ocean,sea,sealife,fish","Individual Peg Stamps>Animals, Birds & Aquatic>Fish;Individual Peg Stamps>Animals, Birds & Aquatic;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("BER","Berry Stamps",1,"berries,berry","Individual Peg Stamps>Berries;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("BIR","Bird Stamps",1,"bird,birds","Individual Peg Stamps>Animals, Birds & Aquatic>Birds;Individual Peg Stamps>Animals, Birds & Aquatic;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("BOE","Botanical Element Stamps",1,"vine,sprig,sprigs,vines","Individual Peg Stamps>Sprigs, Branches & Vines;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("BUI","Butterfly and Insect Stamps",1,"insect,bug,insects,bugs","Individual Peg Stamps>Animals, Birds & Aquatic>Honeybees;Individual Peg Stamps>Animals, Birds & Aquatic;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("CEL","Celestial Stamps",1,"star,snowflake,stars,snowflakes","Individual Peg Stamps>Collage;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("COL","Collage Stamps",1,"collage,misc,miscellaneous","Individual Peg Stamps>Collage;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FFL","Fabric Floral Stamps",0,"floral,flowers,flower","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FLE","Fabric Leaf Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FSP","Fabric Sprig Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FSO","Fabric Special Occasion Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FBU","Fabric Butterfly & Insect Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FTN","Fabric Trees, Nuts, Seed Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FBE","Fabric Berry Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FFR","Fabric Fruit & Veggie Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FCE","Fabric Celestial Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FAN","Fabric Animal Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FCO","Fabric Collage Stamps",0,"NULL","NULL"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FLC","Floral Cluster Stamps",1,"floral,flower,flowers,botanical","Individual Peg Stamps>Flowers;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FLS","Floral Single Stamps",1,"floral,flower,flowers,botanical","Individual Peg Stamps>Flowers;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("FRV","Fruit and Veggie Stamps",1,"fruit,vegetable,vegetables,fruits","Individual Peg Stamps>Fruits & Veggies;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("LEA","Leaf Stamps",1,"leaf,leaves,leafs","Individual Peg Stamps>Leaves & Ferns;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SPB","Sprig and Branch Stamps",1,"sprig,sprigs,branch,branches","Individual Peg Stamps>Sprigs, Branches & Vines;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("SPO","Special Occasion Stamps",1,"holiday,special occasions","Individual Peg Stamps>Holidays & Special Occasions;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("TIS","Three Inch Single Stamps",1,"NULL","Individual Peg Stamps>Three Inch Block Stamps;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("TNS","Tree, Nut, Seed Stamps",1,"tree,nut,seed,nuts,trees,seeds","Individual Peg Stamps>Trees, Nuts & Seeds;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("VIN","Vine Stamps",1,"vine,vines,sprig,sprigs","Individual Peg Stamps>Sprigs, Branches & Vines;Individual Peg Stamps"));
            $model = $this->AddOrUpdatePrefix($this->CreatePrefix("WOR","Word and Sentiment Stamps",1,"word,sentiment,sentiments,phrase,words,phrases","Individual Peg Stamps>Words & Sentiments;Individual Peg Stamps"));
        }

        function LinkProductsToPrefixes()
        {
            $sql =  "UPDATE Products p " .
                    "JOIN Prefixes pf " .
                    "ON (SUBSTR(p.Sku,1,3) = pf.Prefix) " . 
                    "SET p.PrefixId = pf.Id";
            $this->Execute($sql);
        }

        function LinkStampsToStampTypes() 
        {
            $sql =  "UPDATE Products p " .
                    "JOIN StampTypes st " .
                    "ON (SUBSTR(p.Sku,-1) = st.Suffix) " . // join on the last character of Product SKU = StampType suffix
                    "SET p.StampTypeId = st.Id, p.Discriminator = 'Stamp'";
            $this->Execute($sql);
        }
    }
?>