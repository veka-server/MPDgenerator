<?php 

include "bdd.php";

class oracleClass extends Bdd
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function getTable() {

		$rand = (RANDOM)? "order by rand()" : "";
		$data = $this->clientSQL->query("SELECT * FROM ALL_TABLES WHERE OWNER = '".USER."' ");

		return  $data;
	}

	public function getCollumn($table) {

		$data = $this->clientSQL->query("SELECT * FROM user_tab_cols WHERE TABLE_NAME = '".$table."'");
		return  $data;

	}

	public function isFK($table, $column) {

		$q = $this->clientSQL->query("SELECT a.table_name, a.column_name
		  FROM all_cons_columns a
		  LEFT JOIN all_constraints c ON a.owner = c.owner AND a.constraint_name = c.constraint_name
		  WHERE c.constraint_type = 'R' AND a.OWNER = '".USER."' ");

		while($d = $q->fetch())
		{
			if($table == $d['TABLE_NAME'] && $column == $d['COLUMN_NAME'])
				return true;
		}

		return  false;

	}

	public function isPRI($table, $column) {

		$q = $this->clientSQL->query("SELECT a.table_name, a.column_name
		  FROM all_cons_columns a
		  LEFT JOIN all_constraints c ON a.owner = c.owner AND a.constraint_name = c.constraint_name
		  WHERE c.constraint_type = 'P' AND a.OWNER = '".USER."' ");

		while($d = $q->fetch())
		{
			if($table == $d['TABLE_NAME'] && $column == $d['COLUMN_NAME'])
				return true;
		}

		return  false;
	}

	public function liaison() {

		$stmt = $this->clientSQL->prepare("SELECT a.OWNER, a.table_name, a.column_name, a.constraint_name, c.owner, 
       -- referenced pk
       c.r_owner, c_pk.table_name r_table_name, c_pk.constraint_name r_pk
	  FROM all_cons_columns a
	  JOIN all_constraints c ON a.owner = c.owner
	                        AND a.constraint_name = c.constraint_name
	  JOIN all_constraints c_pk ON c.r_owner = c_pk.owner
	                           AND c.r_constraint_name = c_pk.constraint_name
	 WHERE c.constraint_type = 'R'
	   AND a.OWNER = '".USER."'");

		if ($this->execute($stmt))
			return  $stmt;
		else 
			return false;
	}



}
