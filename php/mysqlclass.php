<?php 

include "bdd.php";

class mysqlClass extends Bdd
{
	
	function __construct()
	{
		parent::__construct();
	}


	public function getTable($bdd) {

		$rand = (RANDOM)? "order by rand()" : "";
		$stmt = $this->clientSQL->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = '".$bdd."' ".$rand);

		if ($this->execute($stmt))
			return  $stmt;
		else 
			return false;
	}

	public function getCollumn($bdd,$table) {

		$stmt = $this->clientSQL->prepare("SELECT COLUMN_NAME, COLUMN_KEY, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$table."' AND table_schema = '".$bdd."'");

		if ($this->execute($stmt))
			return  $stmt;
		else 
			return false;
	}

	public function isFK($bdd,$table, $collumn) {

		$stmt = $this->clientSQL->prepare("select * from information_schema.key_column_usage 
			where table_name = '".$table."' AND table_schema = '".$bdd."' and column_name = '".$collumn."' and constraint_name != 'PRIMARY' and REFERENCED_TABLE_NAME is not null");

		if ($this->execute($stmt))
		{
			if($stmt->rowCount() > 0)
				return true;
			return false;
		}
		else 
			return false;
	}

	public function getDataColumn($bdd,$table, $collumn) {

		$stmt = $this->clientSQL->prepare("select * from information_schema.columns where table_name = '".$table."' AND table_schema = '".$bdd."' and column_name = '".$collumn."' ");

		$data = array();
		if ($this->execute($stmt))
		{
			$row = $stmt->fetch();

			$data["nullable"] = ($row["IS_NULLABLE"] == "YES")? true : false;
			$data["ai"] = ($row["EXTRA"] == 'auto_increment')? true : false;
			$data["defaut"] = (empty($row['COLUMN_DEFAULT']))? "NULL" : $row['COLUMN_DEFAULT'] ;
			$data["pri"] = ($row["COLUMN_KEY"] == "PRI")? true : false;

		}
		return $data;
	}

	public function liaison($bdd) {

		$stmt = $this->clientSQL->prepare("select * FROM information_schema.key_column_usage where table_schema = '".$bdd."' and constraint_name != 'PRIMARY' and REFERENCED_TABLE_NAME is not null");

		if ($this->execute($stmt))
			return  $stmt;
		else 
			return false;
	}



}
