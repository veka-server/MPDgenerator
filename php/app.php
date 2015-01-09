<?php

class App
{

	public $data = "";
	public $dataG = "";
	public $dataL = "";
	public $dataLy = "";
	public $dataDico = array();

	private $fullData = array();
	private $fullDataLiaison =  array();
	
	function __construct($argument)
	{
		switch($argument)
		{
			case "mysql" :
				include "php/mysqlclass.php";
				$this->mysqlGetData();
				break;
			case "oracle" :
				include "php/oracleclass.php";
				$this->oracleGetData();
				break;
			default :
				die("Le type de base de données : \"".TYPEBASE."\" non-compatible.");
		}
		$this->generateData();
	}

	private function mysqlGetData() {

		$test = new mysqlClass();
		$stmt = $test->getTable(DATABASE);

		while ($row = $stmt->fetch())
		{
			$stmt2 = $test->getCollumn(DATABASE, $row["TABLE_NAME"]);
			while ($row2 = $stmt2->fetch())
			{
				$dataColumn = $test->getDataColumn(DATABASE, $row["TABLE_NAME"],$row2["COLUMN_NAME"]);

				$this->fullData[$row["TABLE_NAME"]][$row2["COLUMN_NAME"]] = array( 
					"type"=>$row2["COLUMN_TYPE"], 
					"nullable"=>$dataColumn['nullable'], 
					"pri"=>$dataColumn['pri'], 
					"fk"=>($test->isFK(DATABASE, $row["TABLE_NAME"],$row2["COLUMN_NAME"]))? true : false, 
					"default"=>$dataColumn['defaut'], 
					"ai"=>$dataColumn['ai']);
			}
		}

		$stmt3 = $test->liaison(DATABASE);
		while ($row3 = $stmt3->fetch())
			$this->fullDataLiaison[]= array($row3['TABLE_NAME'], $row3['REFERENCED_TABLE_NAME']);
	}

	private function oracleGetData() {

		$test = new oracleClass();
		$stmt = $test->getTable();
		while ($row = $stmt->fetch())
		{
			$stmt2 = $test->getCollumn( $row["TABLE_NAME"]);
			while ($row2 = $stmt2->fetch())
			{
				$this->fullData[$row["TABLE_NAME"]][$row2["COLUMN_NAME"]] = array( 
					"type"=>$row2["DATA_TYPE"].'('.$row2["DATA_LENGTH"].')', 
					"nullable"=>($row2['NULLABLE']=='N')? false : true, 
					"pri"=>$test->isPRI($row["TABLE_NAME"], $row2["COLUMN_NAME"]), 
					"fk"=>$test->isFK($row["TABLE_NAME"], $row2["COLUMN_NAME"]), 
					"default"=>(empty($row2['DATA_DEFAULT']))? "NULL" : $row2['DATA_DEFAULT'], 
					"ai"=>false);
			}
		}

		$stmt3 = $test->liaison();
		while ($row3 = $stmt3->fetch())
			$this->fullDataLiaison[]= array($row3['TABLE_NAME'], $row3['R_TABLE_NAME']);
	}

	private function generateData() {

		foreach ($this->fullData as $tableName => $value) {

			$this->data .= $tableName." (";
			$this->dataG .= "[".$tableName."| ";

			foreach ($value as $columnName => $tabDataColumn) {

					if($tabDataColumn["pri"])
					{
						$this->data .=  "<u>";
//						$this->dataG .=  "[ <underline>";
					}

					$this->data .=  $columnName;
					$this->dataG .=  $columnName;

					if($tabDataColumn["fk"])
					{
						$this->data .=  "*";
					}
					else
					{
						$contrainte = "";

						if(!$tabDataColumn['nullable'])
							$contrainte .=  ' not null';

						if($tabDataColumn['ai'])
							$contrainte .=  ' auto_increment';

						if($tabDataColumn['pri'])
							$contrainte .=  ' primary key';

						$this->dataDico[] = array("nom"=>$tableName.'.'.$columnName,
							"type"=>$tabDataColumn["type"],
							"default"=>$tabDataColumn['default'],
							"contrainte"=>$contrainte);
					}

					$this->dataG .= " : ".$tabDataColumn["type"];

					if($tabDataColumn["pri"]){
						$this->data .=  "</u>";
//						$this->dataG .=  "]";
						$this->dataG .=  " PK";
					}

					if($tabDataColumn["fk"])
					{
						$this->dataG .=  " FK";
					}
					$this->dataG .= ";";				

					$this->data .=  ", ";
				}

				$this->data = substr($this->data, 0,-2);

				if(substr($this->dataG, -2) == " ;")
					$this->dataG = substr($this->dataG, 0,-2);

				$this->data .=  ")<br/>";
				$this->dataG .=  "]";

				if(substr($this->dataG, -2) == ";]")
				{
					$this->dataG = substr($this->dataG, 0,-2).']';
				}
				$this->dataG .=  "\n\r";

		}

		foreach ($this->fullDataLiaison as $value) {
			$this->dataL .= "[".$value[0]."]o->[".$value[1]."]\n";
			$this->dataLy .= "[".$value[0]."]<>->[".$value[1]."]\n";
		}

	}

}
