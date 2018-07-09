<?
class Prefix
{
    public $Id;
    public $Prefix;
    public $Name;
    public $Enabled;
    public $SearchTerms;
    public $CategoryStr;

    // generate insert sql statement based on the model
    public function InsertSQL()
    {
        return "INSERT INTO Prefixes (Prefix, Name, Enabled, SearchTerms, CategoryStr) VALUES ('$this->Prefix', '$this->Name', $this->Enabled, '$this->SearchTerms', '$this->CategoryStr')";
    }

    // generate update sql statement based on the model
    public function UpdateSQLByPrefix()
    {
        $sql =  "UPDATE Prefixes SET Prefix = '$this->Prefix', Name = '$this->Name', Enabled = $this->Enabled, SearchTerms = '$this->SearchTerms', CategoryStr = '$this->CategoryStr' WHERE Prefix = '$this->Prefix'";
        //echo "<p>$sql</p>";
        return $sql;
    }
}

?>