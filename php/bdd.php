<?php 

class Bdd
{

	public $clientSQL;
	
	function __construct()
	{
			switch(TYPEBASE)
			{
				case "mysql" :
					$this->mysqlConnect();
					break;
				case "oracle" :
					$this->oracleConnect();
					break;
			}
	}

	private function mysqlConnect(){
		try{
			$type = 'mysql:host='.HOST.';dbname='.DATABASE;
			$this->clientSQL = new PDO($type.';charset=UTF8', USER, PASS);
		} catch (Exception $e) {

			die("error connexion, verifier les parametres de connexion.");
		}
	}

	private function oracleConnect(){

		$lien_base =
		"oci:dbname=(DESCRIPTION =
		(ADDRESS_LIST =
			(ADDRESS =
				(PROTOCOL = TCP)
				(Host = ".HOST .")
				(Port = 1521))
		)
		(CONNECT_DATA =
			(SID = ".SID.")
		)
		)";

		try
		{
			// connexion à la base Oracle et création de l'objet
			$this->clientSQL =  new PDO($lien_base, USER, PASS);
		}
		catch (PDOException $erreur)
		{
			echo $erreur->getMessage();
		}
	}

	public function execute($stmt, $datas=null) {
		if(!$stmt->execute($datas))
		{
			die('echec sql');
		}
		return true;
	}


}
