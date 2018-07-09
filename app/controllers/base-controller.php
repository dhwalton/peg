<?php
require_once("/volume1/web/peg/app/config/paths.php");
// base controller class that handles database I/O
abstract class Controller {
    private $pdo;
    public function __construct() 
    {
        // set PDO connection
        $this->pdo = $this->InitPDO();
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

    // perform an Execute type query (insert, update, delete)
    public function Execute($query) 
    {
        try {
            $this->pdo->exec($query);
        }
        catch(PDOException $e) {
            echo "<p>" . $e->getMessage() . "</p>";
        }
    }

    // creates a PDO connection to the database
    private function InitPDO() 
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

    public function __destruct() {
        // close PDO connection
        $this->pdo = null;
    }
}
?>