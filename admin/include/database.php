<?php

class Database{

		public $isConn;
		protected $datab;

	//connect to db
	//connect to db
	public function __construct($username = "root" , $password = "" , $host = "localhost" , $dbname = "beakryuat_live" , $options =[ ]){
		$this->isConn = TRUE;
		try{

			$this->datab = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8",$username , $password ,$options);
			$this->datab->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		} catch ( PDOException $e ){

			throw new Exception($e->getMessage());


		}


	}


	//disconnect from db
	public function Disconnect(){

		$this->datab = NULL;
		$this->isConn = FALSE;


	}

	public function getConnection(){

		return $this->datab;

	}

	//get row
	public function getRow($query , $params = []){

		try{

			$stmt = $this->datab->prepare($query);
			$stmt->execute($params);
			return $stmt->fetch();

		} catch(PDOException $e){

			throw new Exception ($e->getMessage());

		}

	}

	//get rows
	public function getRows($query , $params = [] ){

		try{

			$stmt = $this->datab->prepare($query);
			$stmt->execute($params);
			return $stmt->fetchAll();

		} catch(PDOException $e){

			throw new Exception ($e->getMessage());

		}

	}



	//insert row
	public function insertRow($query , $params = []){


		try{

			$stmt = $this->datab->prepare($query);
			$stmt->execute($params);
			return TRUE;

		} catch(PDOException $e){

			throw new Exception ($e->getMessage());

		}


	}

	//insert row
	public function updateRow($query , $params = []){


		try{

			$stmt = $this->datab->prepare($query);
			$stmt->execute($params);
			return TRUE;

		} catch(PDOException $e){

			throw new Exception ($e->getMessage());

		}


	}

	/* update row
	public function updateRow($query , $params = []){
		$this->insertRow($query,$params);

	} */

	//Delete row
	public function deleteRow($query , $params = []){
		$this->insertRow($query,$params);

	}

}

function site_url()
{
    $db = new Database();
    $query = $db->getRow('SELECT * FROM url WHERE type = ? LIMIT 1', [2]);
    if (!$query) {
        $query = $db->getRow('SELECT * FROM url WHERE id = 1');
    }
    return $query['url'];
}
function currency($price)
{
    $db = new database();
    $query = $db->getRow('SELECT * FROM currency WHERE activated = ?', ["Y"]);

    $rate = $query['rate'];
    $price = $rate * $price;
    $price = number_format($price, 2);
    $pricewithCurrency = $query['currency'] . " " . $price;

    return $pricewithCurrency;
}

function ensureMasterWebsiteStatusColumns(Database $db)
{
	static $ensured = false;

	if ($ensured) {
		return;
	}

	$masterTables = array(
		'gorup_master',
		'type_master',
		'category_master',
	);

	foreach ($masterTables as $tableName) {
		try {
			$columnCheck = $db->getRow("SHOW COLUMNS FROM {$tableName} LIKE 'website_status'");
			if (!$columnCheck) {
				$db->insertRow("ALTER TABLE {$tableName} ADD COLUMN website_status ENUM('N','Y') NOT NULL DEFAULT 'Y'");
			}
		} catch (Exception $e) {
			// Ignore schema errors here so admin pages still load.
		}
	}

	$ensured = true;
}

function normalizeWebsiteStatus($value)
{
	return strtoupper((string) $value) === 'N' ? 'N' : 'Y';
}

function isStandingOrdersEnabled($db = null)
{
	static $cached = null;
	if ($cached !== null) {
		return $cached;
	}
	if ($db === null) {
		$db = new Database();
	}
	try {
		$row = $db->getRow('SELECT enable_standing_orders FROM general_settings WHERE id = 1 LIMIT 1');
		$cached = isset($row['enable_standing_orders']) && ((int)$row['enable_standing_orders'] === 1);
	} catch (Exception $e) {
		$cached = false;
	}
	return $cached;
}
?>



