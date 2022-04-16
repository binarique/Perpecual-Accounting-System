<?php
require_once("geo.handler.php");

class Locale{

private $helper;

private $handler;

public function __construct($help){
$this->helper = $help;
$this->handler = new HANDLER();
}

public function getSupportedCountries(){
$response = array();
$stmt = $this->helper->runQuery("SELECT * FROM supported_countries");
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
extract($row);
$country = array(
"country_id" => $this->helper->encryptID($country_id),
"country_name" => $country_name,
"country_iso_code" => $country_iso_code,
"used_currency_id" => $this->helper->encryptID($used_currency_id));
array_push($response, $country);
}
return $response;
}

}
?>