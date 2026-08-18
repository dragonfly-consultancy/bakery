<?php

class Database{

		public $isConn;
		protected $datab;

	//connect to db
	//connect to db
	public function __construct($username = "root" , $password = "" , $host = "localhost" , $dbname = "beakryuat_live", $options =[ ]){
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
    $query = $db->getRow('SELECT * FROM url WHERE type = ? LIMIT 1', [1]);
    if (!$query) {
        $query = $db->getRow('SELECT * FROM url WHERE id = 1');
    }
    return $query['url'];
}
function filterNumbers($var){return preg_replace('[0-9]', ' ', $var);}

function currency($price)
{
	if(empty($price)){
		$price = 0;
	}else{
		$price = filterNumbers($price);
	}
	$SiteCurrency = "";
	if(isset($_SESSION['currency'])){$SiteCurrency = $_SESSION['currency'];}

	if(empty($SiteCurrency)){
		$db = new database();
		$query = $db->getRow('SELECT rate, currency FROM currency WHERE activated = ?', ["Y"]);
		$rate = $query['rate'];
		$price = $rate * $price;
		$price = number_format($price, 2);
		$pricewithCurrency = $query['currency'] . " " . $price;
		$_SESSION['currency'] = $query['currency'];
		$_SESSION['currencyConvertion'] = $query['rate'];
	}else{
		$rate = $_SESSION['currencyConvertion'];
		$price = $rate * $price;
		$price = number_format($price, 2);
		$pricewithCurrency = $_SESSION['currency'] . " " . $price;
	}


    return $pricewithCurrency;
}
function Redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);

    exit();
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
			// Keep pages working even if schema updates are not permitted in this request.
		}
	}

	$ensured = true;
}

function normalizeWebsiteStatus($value)
{
	return strtoupper((string) $value) === 'N' ? 'N' : 'Y';
}
?>